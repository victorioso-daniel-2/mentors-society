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
            'academic_year_id' => 1,
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31',
            'description' => '2024-2025'
        ]);

        // Create role
        $role = Role::create([
            'role_id' => 1,
            'role_name' => 'Student',
            'description' => 'Regular student role',
            'role_priority' => 99
        ]);

        // Create student first
        $student = Student::create([
            'student_number' => '2021-00112-TG-0',
            'first_name' => 'Janella',
            'last_name' => 'Boncodin',
            'middle_initial' => 'A',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'email' => 'janella.boncodin@example.com',
            'academic_status' => 'active'
        ]);

        // Create user with matching student_number
        $user = User::create([
            'student_number' => '2021-00112-TG-0',
            'password' => Hash::make('JanellaAnneBoncodin'),
            'status' => 'active'
        ]);

        // Assign role to user
        UserRole::create([
            'user_role_id' => 1,
            'student_number' => '2021-00112-TG-0',
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
            'student_number' => '2021-00112-TG-0',
            'password' => 'JanellaAnneBoncodin'
        ]);
        dump($response->json());
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'student_number',
                            'first_name',
                            'last_name',
                            'email'
                        ],
                        'roles',
                        'token',
                        'token_type'
                    ]
                ])
                ->assertJsonFragment([
                    'first_name' => 'Janella',
                    'last_name' => 'Boncodin',
                    'email' => 'janella.boncodin@example.com',
                    'student_number' => '2021-00112-TG-0'
                ]);
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
            'student_number' => '2021-00112-TG-0',
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
        // Create a student without a user account
        Student::create([
            'student_number' => '2021-99999-TG-0',
            'first_name' => 'No',
            'last_name' => 'User',
            'middle_initial' => null,
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'email' => 'no.user@example.com',
            'academic_status' => 'active'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-99999-TG-0',
            'password' => 'NoUser'
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
        // Create student record for this user first
        Student::firstOrCreate([
            'student_number' => '2021-99998-TG-0',
            'first_name' => 'No',
            'last_name' => 'Password',
            'middle_initial' => null,
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'email' => 'no.password@example.com',
            'academic_status' => 'active'
        ]);

        // Now create a user with an empty password string instead of null
        User::firstOrCreate([
            'student_number' => '2021-99998-TG-0',
        ], [
            'password' => '', // Use empty string instead of null
            'status' => 'active'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'student_number' => '2021-99998-TG-0',
            'password' => 'anypassword'
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
        // First login to get a token
        $loginResponse = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00112-TG-0',
            'password' => 'JanellaAnneBoncodin'
        ]);
        dump($loginResponse->json());
        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.token');

        // Now logout with the token
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
        // First login to get a token
        $loginResponse = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00112-TG-0',
            'password' => 'JanellaAnneBoncodin'
        ]);
        dump($loginResponse->json());
        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.token');

        // Now get user info with the token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user' => [
                            'student_number',
                            'first_name',
                            'last_name',
                            'email'
                        ]
                    ]
                ])
                ->assertJsonFragment([
                    'first_name' => 'Janella',
                    'last_name' => 'Boncodin',
                    'email' => 'janella.boncodin@example.com',
                    'student_number' => '2021-00112-TG-0'
                ]);
    }
} 