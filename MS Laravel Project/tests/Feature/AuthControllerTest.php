<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use App\Models\AcademicYear;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->createTestData();
    }

    private function createTestData()
    {
        // Create academic year
        $academicYear = AcademicYear::create([
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31',
            'description' => 'Academic Year 2024-2025'
        ]);

        // Create role
        $role = Role::create([
            'role_name' => 'Student',
            'description' => 'Regular student role',
            'role_priority' => 99
        ]);

        // Create user
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_initial' => 'A',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123')
        ]);

        // Create student
        $student = Student::create([
            'user_id' => $user->user_id,
            'student_number' => '2021-00001-TG-0'
        ]);

        // Assign role to user
        UserRole::create([
            'user_id' => $user->user_id,
            'role_id' => $role->role_id,
            'academic_year_id' => $academicYear->academic_year_id,
            'start_date' => now(),
            'end_date' => null
        ]);
    }

    /**
     * Test successful login with valid student number and password
     */
    public function test_successful_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00001-TG-0',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'first_name',
                            'last_name',
                            'email',
                            'student_number'
                        ],
                        'roles',
                        'token',
                        'token_type'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => [
                            'first_name' => 'John',
                            'last_name' => 'Doe',
                            'email' => 'john.doe@example.com',
                            'student_number' => '2021-00001-TG-0'
                        ],
                        'token_type' => 'Bearer'
                    ]
                ]);

        // Check that token is not empty
        $this->assertNotEmpty($response->json('data.token'));
    }

    /**
     * Test login with invalid student number
     */
    public function test_login_with_invalid_student_number()
    {
        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-99999-TG-0',
            'password' => 'password123'
        ]);

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Student number not found'
                ]);
    }

    /**
     * Test login with invalid password
     */
    public function test_login_with_invalid_password()
    {
        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00001-TG-0',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid password'
                ]);
    }

    /**
     * Test login with missing student number
     */
    public function test_login_with_missing_student_number()
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['student_number']);
    }

    /**
     * Test login with missing password
     */
    public function test_login_with_missing_password()
    {
        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00001-TG-0'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test login with empty student number
     */
    public function test_login_with_empty_student_number()
    {
        $response = $this->postJson('/api/auth/login', [
            'student_number' => '',
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['student_number']);
    }

    /**
     * Test login with empty password
     */
    public function test_login_with_empty_password()
    {
        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00001-TG-0',
            'password' => ''
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test login with student number that has no associated user
     */
    public function test_login_with_student_number_no_user()
    {
        // Create a student without a user
        $student = Student::create([
            'user_id' => 99999, // Non-existent user
            'student_number' => '2021-00002-TG-0'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00002-TG-0',
            'password' => 'password123'
        ]);

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'User account not found'
                ]);
    }

    /**
     * Test login with user that has no password set
     */
    public function test_login_with_user_no_password()
    {
        // Create user without password
        $user = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'middle_initial' => 'B',
            'email' => 'jane.smith@example.com'
            // No password set
        ]);

        $student = Student::create([
            'user_id' => $user->user_id,
            'student_number' => '2021-00003-TG-0'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00003-TG-0',
            'password' => 'password123'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Password not set for this account'
                ]);
    }

    /**
     * Test login with student number format validation
     */
    public function test_login_with_invalid_student_number_format()
    {
        $response = $this->postJson('/api/auth/login', [
            'student_number' => 'invalid-format',
            'password' => 'password123'
        ]);

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Student number not found'
                ]);
    }

    /**
     * Test login with password too short
     */
    public function test_login_with_password_too_short()
    {
        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00001-TG-0',
            'password' => '123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test successful logout
     */
    public function test_successful_logout()
    {
        // First login to get token
        $loginResponse = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00001-TG-0',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        // Then logout
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Logout successful'
                ]);
    }

    /**
     * Test get current user info
     */
    public function test_get_current_user_info()
    {
        // First login to get token
        $loginResponse = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00001-TG-0',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        // Get user info
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user' => [
                            'id',
                            'first_name',
                            'last_name',
                            'email',
                            'student_number'
                        ]
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'user' => [
                            'first_name' => 'John',
                            'last_name' => 'Doe',
                            'email' => 'john.doe@example.com',
                            'student_number' => '2021-00001-TG-0'
                        ]
                    ]
                ]);
    }
} 