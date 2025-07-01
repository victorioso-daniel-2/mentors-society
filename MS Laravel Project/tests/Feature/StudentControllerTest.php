<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use App\Models\StudentClass;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data with unique identifiers
        $this->academicYear = AcademicYear::create([
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31',
            'description' => 'Academic Year 2024-2025'
        ]);

        $this->testClass = ClassModel::create([
            'class_name' => 'BSED MATH',
            'academic_year_id' => $this->academicYear->academic_year_id
        ]);

        // Create test user with president role
        $this->user = User::create([
            'first_name' => 'Test',
            'last_name' => 'President',
            'email' => 'president@test.com',
            'password' => bcrypt('password123')
        ]);

        $presidentRole = Role::create([
            'role_name' => 'President',
            'description' => 'Organization President',
            'role_priority' => 1
        ]);

        // Assign president role to user using UserRole directly
        UserRole::create([
            'student_number' => $this->user->student_number,
            'role_id' => $presidentRole->role_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'start_date' => now()
        ]);

        Sanctum::actingAs($this->user);
    }

    public function test_it_can_list_students()
    {
        $response = $this->getJson('/api/students');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'student_number',
                                'user' => [
                                    'email'
                                ]
                            ]
                        ]
                    ],
                    'message'
                ]);
    }

    public function test_it_can_list_students_with_search()
    {
        $response = $this->getJson('/api/students?search=test');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    public function test_it_can_list_students_with_class_filter()
    {
        $response = $this->getJson("/api/students?class_id={$this->testClass->class_id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    public function test_it_can_create_student()
    {
        $studentData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_initial' => 'A',
            'email' => 'john.doe@test.com',
            'password' => 'password123',
            'student_number' => '2024-0001',
            'classes' => [
                [
                    'class_id' => $this->testClass->class_id,
                    'academic_year_id' => $this->academicYear->academic_year_id,
                    'year_level' => 'First Year'
                ]
            ]
        ];

        $response = $this->postJson('/api/students', $studentData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Student created successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        'student_number',
                        'user' => [
                            'email'
                        ]
                    ]
                ]);

        $this->assertDatabaseHas('user', [
            'email' => 'john.doe@test.com',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $this->assertDatabaseHas('student', [
            'student_number' => '2024-0001'
        ]);
    }

    public function test_it_validates_student_creation()
    {
        $response = $this->postJson('/api/students', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password', 'student_number']);
    }

    public function test_it_prevents_duplicate_student_number()
    {
        // Create first student
        $student = Student::create([
            'student_number' => User::create([
                'first_name' => 'First',
                'last_name' => 'Student',
                'email' => 'first@test.com',
                'password' => bcrypt('password123')
            ])->student_number,
            'student_number' => '2024-0001'
        ]);

        // Try to create second student with same number
        $response = $this->postJson('/api/students', [
            'first_name' => 'Second',
            'last_name' => 'Student',
            'email' => 'second@test.com',
            'password' => 'password123',
            'student_number' => '2024-0001'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['student_number']);
    }

    public function test_it_prevents_duplicate_email()
    {
        // Create first user
        User::create([
            'first_name' => 'First',
            'last_name' => 'User',
            'email' => 'duplicate@test.com',
            'password' => bcrypt('password123')
        ]);

        // Try to create student with same email
        $response = $this->postJson('/api/students', [
            'first_name' => 'Second',
            'last_name' => 'User',
            'email' => 'duplicate@test.com',
            'password' => 'password123',
            'student_number' => '2024-0002'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_it_can_get_specific_student()
    {
        $student = Student::create([
            'student_number' => User::create([
                'first_name' => 'Test',
                'last_name' => 'Student',
                'email' => 'test.student@test.com',
                'password' => bcrypt('password123')
            ])->student_number,
            'student_number' => '2024-0003'
        ]);

        $response = $this->getJson("/api/students/{$student->student_number}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'student_number' => '2024-0003'
                    ]
                ]);
    }

    public function test_it_returns_404_for_nonexistent_student()
    {
        $response = $this->getJson('/api/students/99999');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Student not found'
                ]);
    }

    public function test_it_can_update_student()
    {
        $student = Student::create([
            'student_number' => User::create([
                'first_name' => 'Original',
                'last_name' => 'Student',
                'email' => 'original@test.com',
                'password' => bcrypt('password123')
            ])->student_number,
            'student_number' => '2024-0004'
        ]);

        $updateData = [
            'first_name' => 'Updated',
            'last_name' => 'Student',
            'email' => 'updated@test.com',
            'student_number' => '2024-0005'
        ];

        $response = $this->putJson("/api/students/{$student->student_number}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Student updated successfully'
                ]);

        $this->assertDatabaseHas('user', [
            'student_number' => $student->student_number,
            'first_name' => 'Updated',
            'email' => 'updated@test.com'
        ]);

        $this->assertDatabaseHas('student', [
            'student_number' => '2024-0005'
        ]);
    }

    public function test_it_can_delete_student()
    {
        $student = Student::create([
            'student_number' => User::create([
                'first_name' => 'Delete',
                'last_name' => 'Student',
                'email' => 'delete@test.com',
                'password' => bcrypt('password123')
            ])->student_number,
            'student_number' => '2024-0006'
        ]);

        $response = $this->deleteJson("/api/students/{$student->student_number}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Student deleted successfully'
                ]);

        $this->assertDatabaseMissing('student', [
            'student_number' => $student->student_number
        ]);

        $this->assertDatabaseMissing('user', [
            'student_number' => $student->student_number
        ]);
    }

    public function test_it_can_get_student_classes()
    {
        $student = Student::create([
            'student_number' => User::create([
                'first_name' => 'Class',
                'last_name' => 'Student',
                'email' => 'class@test.com',
                'password' => bcrypt('password123')
            ])->student_number,
            'student_number' => '2024-0007'
        ]);

        // Assign class to student
        StudentClass::create([
            'student_id' => $student->student_id,
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'First Year'
        ]);

        $response = $this->getJson("/api/students/{$student->student_number}/classes");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Student classes retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'student_number',
                            'class_id',
                            'academic_year_id',
                            'year_level'
                        ]
                    ]
                ]);
    }

    public function test_it_can_assign_class_to_student()
    {
        $student = Student::create([
            'student_number' => User::create([
                'first_name' => 'Assign',
                'last_name' => 'Student',
                'email' => 'assign@test.com',
                'password' => bcrypt('password123')
            ])->student_number,
            'student_number' => '2024-0008'
        ]);

        $classData = [
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'Second Year'
        ];

        $response = $this->postJson("/api/students/{$student->student_number}/classes", $classData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Class assigned successfully'
                ]);

        $this->assertDatabaseHas('student_class', [
            'student_id' => $student->student_id,
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'Second Year'
        ]);
    }

    public function test_it_prevents_duplicate_class_assignment()
    {
        $student = Student::create([
            'student_number' => User::create([
                'first_name' => 'Duplicate',
                'last_name' => 'Student',
                'email' => 'duplicate.class@test.com',
                'password' => bcrypt('password123')
            ])->student_number,
            'student_number' => '2024-0009'
        ]);

        // Assign class first time
        StudentClass::create([
            'student_id' => $student->student_id,
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'Third Year'
        ]);

        // Try to assign same class again
        $response = $this->postJson("/api/students/{$student->student_number}/classes", [
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'Fourth Year'
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Student is already assigned to this class for this academic year'
                ]);
    }

    public function test_it_can_remove_class_assignment()
    {
        $student = Student::create([
            'student_number' => User::create([
                'first_name' => 'Remove',
                'last_name' => 'Student',
                'email' => 'remove@test.com',
                'password' => bcrypt('password123')
            ])->student_number,
            'student_number' => '2024-0010'
        ]);

        // Assign class
        StudentClass::create([
            'student_id' => $student->student_id,
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'Fourth Year'
        ]);

        $response = $this->deleteJson("/api/students/{$student->student_number}/classes", [
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Class assignment removed successfully'
                ]);

        $this->assertDatabaseMissing('student_class', [
            'student_id' => $student->student_id,
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id
        ]);
    }

    public function test_it_can_get_available_classes()
    {
        $response = $this->getJson('/api/students/classes/available');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Available classes retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'class_id',
                            'class_name',
                            'academic_year_id'
                        ]
                    ]
                ]);
    }
} 