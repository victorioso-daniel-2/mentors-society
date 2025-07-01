<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;
    protected $academicYear;
    protected $role;
    protected $student;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create academic year
        $this->academicYear = AcademicYear::firstOrCreate([
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31'
        ], [
            'description' => 'Academic Year 2024-2025'
        ]);

        // Create role
        $this->role = Role::create([
            'role_id' => 1,
            'role_name' => 'Student',
            'description' => 'Student role',
            'role_priority' => 1
        ]);

        // Create student first
        $this->student = Student::create([
            'student_number' => '2021-00112-TG-0',
            'first_name' => 'Janella',
            'last_name' => 'Boncodin',
            'middle_initial' => 'A',
            'email' => 'janella.boncodin@example.com',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);

        // Create user with matching student_number
        $this->user = User::create([
            'student_number' => '2021-00112-TG-0',
            'password' => Hash::make('JanellaAnneBoncodin'),
            'status' => 'active'
        ]);

        // Create user role
        $this->userRole = UserRole::create([
            'user_role_id' => 1,
            'student_number' => $this->user->student_number,
            'role_id' => $this->role->role_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'start_date' => now(),
            'end_date' => null
        ]);

        // Login and get token
        $loginResponse = $this->postJson('/api/auth/login', [
            'student_number' => '2021-00112-TG-0',
            'password' => 'JanellaAnneBoncodin'
        ]);

        $loginData = $loginResponse->json();
        $this->token = $loginData['data']['token'];
    }

    /** @test */
    public function it_can_list_users()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'current_page',
                        'data' => [
                            '*' => [
                                'student_number',
                                'first_name',
                                'last_name',
                                'email',
                                'full_name',
                                'status',
                                'roles'
                            ]
                        ]
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_list_users_with_search_filter()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users?search=Janella');

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonPath('data.data.0.first_name', 'Janella');
    }

    /** @test */
    public function it_can_list_users_with_role_filter()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users?role=Student');

        $response->assertStatus(200)
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_list_users_with_active_filter()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users?active=true');

        $response->assertStatus(200)
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_get_specific_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users/' . $this->user->student_number);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'student_number',
                        'first_name',
                        'last_name',
                        'email',
                        'full_name',
                        'status',
                        'roles'
                    ],
                    'message'
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'student_number' => $this->user->student_number,
                        'first_name' => 'Janella',
                        'last_name' => 'Boncodin',
                        'email' => 'janella.boncodin@example.com'
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users/nonexistent-user');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'User not found'
                ]);
    }

    /** @test */
    public function it_can_update_user_information()
    {
        $updateData = [
            'first_name' => 'Janella Updated',
            'last_name' => 'Boncodin Updated',
            'email' => 'janella.updated@example.com',
            'status' => 'inactive'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->putJson('/api/users/' . $this->user->student_number, $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'student_number',
                        'first_name',
                        'last_name',
                        'email',
                        'full_name',
                        'status',
                        'created_at',
                        'updated_at'
                    ],
                    'message'
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'student_number' => $this->user->student_number,
                        'first_name' => 'Janella Updated',
                        'last_name' => 'Boncodin Updated',
                        'email' => 'janella.updated@example.com',
                        'status' => 'inactive'
                    ]
                ]);
    }

    /** @test */
    public function it_can_update_user_password()
    {
        $updateData = [
            'password' => 'NewPassword123'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->putJson('/api/users/' . $this->user->student_number, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
    }

    /** @test */
    public function it_validates_update_data()
    {
        $invalidData = [
            'email' => 'invalid-email',
            'password' => '123' // too short
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->putJson('/api/users/' . $this->user->student_number, $invalidData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed'
                ])
                ->assertJsonStructure([
                    'errors' => [
                        'email',
                        'password'
                    ]
                ]);
    }

    /** @test */
    public function it_can_get_user_roles()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users/' . $this->user->student_number . '/roles');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'user_role_id',
                            'role_id',
                            'role_name',
                            'academic_year_id',
                            'start_date',
                            'end_date'
                        ]
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_assign_role_to_user()
    {
        // Create another role
        $newRole = Role::create([
            'role_id' => 2,
            'role_name' => 'Officer',
            'description' => 'Officer role',
            'role_priority' => 2
        ]);

        $roleData = [
            'role_id' => $newRole->role_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => null
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/users/' . $this->user->student_number . '/roles', $roleData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user_role_id',
                        'student_number',
                        'role_id',
                        'academic_year_id',
                        'start_date',
                        'end_date'
                    ],
                    'message'
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'student_number' => $this->user->student_number,
                        'role_id' => $newRole->role_id
                    ]
                ]);
    }

    /** @test */
    public function it_validates_role_assignment_data()
    {
        $invalidData = [
            'role_id' => 'invalid',
            'academic_year_id' => 'invalid'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/users/' . $this->user->student_number . '/roles', $invalidData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed'
                ]);
    }

    /** @test */
    public function it_prevents_duplicate_role_assignment()
    {
        $roleData = [
            'role_id' => $this->role->role_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => null
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/users/' . $this->user->student_number . '/roles', $roleData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'User already has this role for the specified academic year'
                ]);
    }

    /** @test */
    public function it_can_remove_role_from_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->deleteJson('/api/users/' . $this->user->student_number . '/roles/' . $this->userRole->user_role_id);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Role removed successfully'
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_user_role()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->deleteJson('/api/users/' . $this->user->student_number . '/roles/999');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'User role not found'
                ]);
    }

    /** @test */
    public function it_can_get_users_by_role()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users/role/Student');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'student_number',
                            'first_name',
                            'last_name',
                            'email',
                            'full_name',
                            'status',
                            'roles'
                        ]
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_search_users()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users/search?q=Janella');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'student_number',
                            'first_name',
                            'last_name',
                            'email',
                            'full_name',
                            'status',
                            'roles'
                        ]
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_validates_search_query()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/users/search');

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Search query is required'
                ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }
} 