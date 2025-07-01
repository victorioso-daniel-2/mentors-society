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

    protected $academicYear;
    protected $testClass;
    protected $student;
    protected $user;

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

        // Create test student for user
        $this->student = Student::create([
            'student_number' => '2024-STUDENT-001',
            'first_name' => 'Test',
            'last_name' => 'President',
            'middle_initial' => 'A',
            'email' => 'president@test.com',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        // Create test user with president role
        $this->user = User::create([
            'student_number' => '2024-STUDENT-001',
            'password' => bcrypt('password123'),
            'status' => 'active'
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
                                'first_name',
                                'last_name',
                                'middle_initial',
                                'email',
                                'course',
                                'year_level',
                                'section',
                                'academic_status',
                                'user' => [
                                    'student_number',
                                    'status'
                                ],
                                'student_classes'
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
            'course' => 'BSIT',
            'year_level' => 'First Year',
            'section' => 'A',
            'academic_status' => 'active',
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
                        'email'
                    ]
                ]);

        $this->assertDatabaseHas('student', [
            'student_number' => '2024-0001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@test.com'
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
        // Create first student and user
        Student::create([
            'student_number' => '2024-0001',
            'first_name' => 'First',
            'last_name' => 'Student',
            'middle_initial' => 'B',
            'email' => 'first@test.com',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        User::create([
            'student_number' => '2024-0001',
            'password' => bcrypt('password123'),
            'status' => 'active'
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
        // Create first student and user
        Student::create([
            'student_number' => '2024-0002',
            'first_name' => 'First',
            'last_name' => 'User',
            'middle_initial' => 'C',
            'email' => 'duplicate@test.com',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        User::create([
            'student_number' => '2024-0002',
            'password' => bcrypt('password123'),
            'status' => 'active'
        ]);
        // Try to create student with same email
        $response = $this->postJson('/api/students', [
            'first_name' => 'Second',
            'last_name' => 'User',
            'email' => 'duplicate@test.com',
            'password' => 'password123',
            'student_number' => '2024-0003'
        ]);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_it_can_get_specific_student()
    {
        Student::create([
            'student_number' => '2024-0003',
            'first_name' => 'Test',
            'last_name' => 'Student',
            'middle_initial' => 'D',
            'email' => 'test.student@test.com',
            'course' => 'BSIT',
            'year_level' => 'First Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        User::create([
            'student_number' => '2024-0003',
            'password' => bcrypt('password123'),
            'status' => 'active'
        ]);

        $response = $this->getJson("/api/students/2024-0003");

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
        Student::create([
            'student_number' => '2024-0004',
            'first_name' => 'Original',
            'last_name' => 'Student',
            'middle_initial' => 'E',
            'email' => 'original@test.com',
            'course' => 'BSIT',
            'year_level' => 'First Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        User::create([
            'student_number' => '2024-0004',
            'password' => bcrypt('password123'),
            'status' => 'active'
        ]);

        $updateData = [
            'first_name' => 'Updated',
            'last_name' => 'Student',
            'middle_initial' => 'E',
            'email' => 'updated@test.com',
            'student_number' => '2024-0005'
        ];

        $response = $this->putJson("/api/students/2024-0004", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Student updated successfully'
                ]);

        $this->assertDatabaseHas('student', [
            'student_number' => '2024-0004',
            'first_name' => 'Updated',
            'email' => 'updated@test.com'
        ]);
    }

    public function test_it_can_delete_student()
    {
        Student::create([
            'student_number' => '2024-0006',
            'first_name' => 'Delete',
            'last_name' => 'Student',
            'middle_initial' => 'F',
            'email' => 'delete@test.com',
            'course' => 'BSIT',
            'year_level' => 'First Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        User::create([
            'student_number' => '2024-0006',
            'password' => bcrypt('password123'),
            'status' => 'active'
        ]);

        $response = $this->deleteJson("/api/students/2024-0006");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Student deleted successfully'
                ]);

        $this->assertDatabaseMissing('student', [
            'student_number' => '2024-0006'
        ]);

        $this->assertDatabaseMissing('user', [
            'student_number' => '2024-0006'
        ]);
    }

    public function test_it_can_get_student_classes()
    {
        Student::create([
            'student_number' => '2024-0007',
            'first_name' => 'Class',
            'last_name' => 'Student',
            'middle_initial' => 'G',
            'email' => 'class@test.com',
            'course' => 'BSIT',
            'year_level' => 'First Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        User::create([
            'student_number' => '2024-0007',
            'password' => bcrypt('password123'),
            'status' => 'active'
        ]);

        // Assign class to student
        StudentClass::create([
            'student_number' => '2024-0007',
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'First Year'
        ]);

        $response = $this->getJson("/api/students/2024-0007/classes");

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
        Student::create([
            'student_number' => '2024-0008',
            'first_name' => 'Assign',
            'last_name' => 'Student',
            'middle_initial' => 'H',
            'email' => 'assign@test.com',
            'course' => 'BSIT',
            'year_level' => 'Second Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        User::create([
            'student_number' => '2024-0008',
            'password' => bcrypt('password123'),
            'status' => 'active'
        ]);

        $classData = [
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'Second Year'
        ];

        $response = $this->postJson("/api/students/2024-0008/classes", $classData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Class assigned successfully'
                ]);

        $this->assertDatabaseHas('student_class', [
            'student_number' => '2024-0008',
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'Second Year'
        ]);
    }

    public function test_it_prevents_duplicate_class_assignment()
    {
        Student::create([
            'student_number' => '2024-0009',
            'first_name' => 'Duplicate',
            'last_name' => 'Student',
            'middle_initial' => 'I',
            'email' => 'duplicate.class@test.com',
            'course' => 'BSIT',
            'year_level' => 'Third Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        User::create([
            'student_number' => '2024-0009',
            'password' => bcrypt('password123'),
            'status' => 'active'
        ]);
        // Assign class first time
        StudentClass::create([
            'student_number' => '2024-0009',
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'Third Year'
        ]);
        // Try to assign same class again
        $response = $this->postJson("/api/students/2024-0009/classes", [
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
        Student::create([
            'student_number' => '2024-0010',
            'first_name' => 'Remove',
            'last_name' => 'Student',
            'middle_initial' => 'J',
            'email' => 'remove@test.com',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        User::create([
            'student_number' => '2024-0010',
            'password' => bcrypt('password123'),
            'status' => 'active'
        ]);
        // Assign class
        StudentClass::create([
            'student_number' => '2024-0010',
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'year_level' => 'Fourth Year'
        ]);
        $response = $this->deleteJson("/api/students/2024-0010/classes", [
            'class_id' => $this->testClass->class_id,
            'academic_year_id' => $this->academicYear->academic_year_id
        ]);
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Class assignment removed successfully'
                ]);
        $this->assertDatabaseMissing('student_class', [
            'student_number' => '2024-0010',
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