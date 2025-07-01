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

        // Create user
        $this->user = User::create([
            'student_number' => 1,
            'first_name' => 'Janella',
            'last_name' => 'Boncodin',
            'middle_initial' => 'A',
            'email' => 'janella.boncodin@example.com',
            'password' => Hash::make('JanellaAnneBoncodin')
        ]);

        // Create student record
        $this->student = Student::create([
            'student_id' => 1,
            'student_number' => '2021-00112-TG-0'
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

        // Debug: Check if login was successful
        if ($loginResponse->status() !== 200) {
            dd('Login failed in UserControllerTest:', $loginResponse->json());
        }

        $loginData = $loginResponse->json();
        
        // Debug: Check if token exists in response
        if (!isset($loginData['data']['token'])) {
            dd('No token in login response:', $loginData);
        }

        $this->token = $loginData['data']['token'];
        
        // Debug: Check token format
        if (empty($this->token)) {
            dd('Token is empty:', $this->token);
        }

        // Debug: Print token for verification
        echo "Token generated: " . substr($this->token, 0, 20) . "...\n";
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
                                'id',
                                'first_name',
                                'last_name',
                                'email',
                                'full_name',
                                'student_number',
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
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'full_name',
                        'student_number',
                        'roles'
                    ],
                    'message'
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $this->user->student_number,
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
        ])->getJson('/api/users/99999');

        $response->assertStatus(404)
                ->assertJson(['success' => false]);
    }

    /** @test */
    public function it_can_update_user_information()
    {
        $updateData = [
            'first_name' => 'Janella Updated',
            'last_name' => 'Boncodin Updated',
            'email' => 'janella.updated@gmail.com'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->putJson('/api/users/' . $this->user->student_number, $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'full_name',
                        'student_number',
                        'updated_at'
                    ],
                    'message'
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'first_name' => 'Janella Updated',
                        'last_name' => 'Boncodin Updated',
                        'email' => 'janella.updated@gmail.com'
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
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_validates_update_data()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->putJson('/api/users/' . $this->user->student_number, [
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(400)
                ->assertJson(['success' => false])
                ->assertJsonStructure(['errors']);
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
                            'description',
                            'academic_year_id',
                            'start_date',
                            'end_date',
                            'is_active'
                        ]
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_assign_role_to_user()
    {
        // Create a new role for testing
        $newRole = Role::create([
            'role_id' => 2,
            'role_name' => 'Test Role',
            'description' => 'Test role for assignment',
            'role_priority' => 2
        ]);

        $roleData = [
            'role_id' => $newRole->role_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d')
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/users/' . $this->user->student_number . '/roles', $roleData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user_role_id',
                        'role_id',
                        'role_name',
                        'academic_year_id',
                        'start_date',
                        'end_date'
                    ],
                    'message'
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'role_id' => $newRole->role_id,
                        'role_name' => 'Test Role'
                    ]
                ]);
    }

    /** @test */
    public function it_validates_role_assignment_data()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/users/' . $this->user->student_number . '/roles', [
            'role_id' => 99999, // Non-existent role
            'academic_year_id' => $this->academicYear->academic_year_id,
            'start_date' => now()->format('Y-m-d')
        ]);

        $response->assertStatus(400)
                ->assertJson(['success' => false])
                ->assertJsonStructure(['errors']);
    }

    /** @test */
    public function it_prevents_duplicate_role_assignment()
    {
        $roleData = [
            'role_id' => $this->role->role_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'start_date' => now()->format('Y-m-d')
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/users/' . $this->user->student_number . '/roles', $roleData);

        $response->assertStatus(400)
                ->assertJson(['success' => false])
                ->assertJsonFragment(['message' => 'User already has this role for the specified academic year']);
    }

    /** @test */
    public function it_can_remove_role_from_user()
    {
        // First, get the user role ID
        $userRole = UserRole::where('student_number', $this->user->student_number)
                           ->where('role_id', $this->role->role_id)
                           ->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->deleteJson('/api/users/' . $this->user->student_number . '/roles/' . $userRole->user_role_id);

        $response->assertStatus(200)
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_user_role()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->deleteJson('/api/users/' . $this->user->student_number . '/roles/99999');

        $response->assertStatus(404)
                ->assertJson(['success' => false]);
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
                            'id',
                            'first_name',
                            'last_name',
                            'email',
                            'full_name',
                            'student_number',
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
        ])->getJson('/api/users-search?query=Janella&limit=5');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'first_name',
                            'last_name',
                            'email',
                            'full_name',
                            'student_number'
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
        ])->getJson('/api/users-search?query=a');

        $response->assertStatus(400)
                ->assertJson(['success' => false])
                ->assertJsonStructure(['errors']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }
} 