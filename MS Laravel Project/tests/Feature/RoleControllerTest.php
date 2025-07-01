<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AcademicYear;
use App\Models\UserRole;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->academicYear = AcademicYear::firstOrCreate([
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31'
        ], [
            'description' => 'Academic Year 2024-2025'
        ]);

        $this->permissions = collect([
            Permission::create(['permission_name' => 'user.view']),
            Permission::create(['permission_name' => 'user.create']),
            Permission::create(['permission_name' => 'user.edit']),
            Permission::create(['permission_name' => 'user.delete']),
        ]);

        // Create test student for user
        $this->student = Student::create([
            'student_number' => '2024-ROLE-001',
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
            'student_number' => '2024-ROLE-001',
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

        // Assign all permissions to president role
        $presidentRole->permissions()->sync($this->permissions->pluck('permission_id'));

        Sanctum::actingAs($this->user);
    }

    public function test_it_can_list_roles()
    {
        $response = $this->getJson('/api/roles');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'role_id',
                                'role_name',
                                'description',
                                'role_priority',
                                'permissions'
                            ]
                        ]
                    ],
                    'message'
                ]);
    }

    public function test_it_can_list_roles_with_search()
    {
        $response = $this->getJson('/api/roles?search=President');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    public function test_it_can_create_role()
    {
        $roleData = [
            'role_name' => 'Test Role',
            'description' => 'A test role',
            'role_priority' => 10,
            'permissions' => [$this->permissions[0]->permission_id]
        ];

        $response = $this->postJson('/api/roles', $roleData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Role created successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        'role_id',
                        'role_name',
                        'description',
                        'role_priority',
                        'permissions'
                    ]
                ]);

        $this->assertDatabaseHas('role', [
            'role_name' => 'Test Role',
            'description' => 'A test role',
            'role_priority' => 10
        ]);
    }

    public function test_it_validates_role_creation()
    {
        $response = $this->postJson('/api/roles', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['role_name', 'role_priority']);
    }

    public function test_it_prevents_duplicate_role_name()
    {
        Role::create([
            'role_name' => 'Duplicate Role',
            'description' => 'First role',
            'role_priority' => 5
        ]);

        $response = $this->postJson('/api/roles', [
            'role_name' => 'Duplicate Role',
            'description' => 'Second role',
            'role_priority' => 6
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['role_name']);
    }

    public function test_it_can_get_specific_role()
    {
        $role = Role::create([
            'role_name' => 'Test Role',
            'description' => 'A test role',
            'role_priority' => 10
        ]);

        $response = $this->getJson("/api/roles/{$role->role_id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'role_id' => $role->role_id,
                        'role_name' => 'Test Role'
                    ]
                ]);
    }

    public function test_it_returns_404_for_nonexistent_role()
    {
        $response = $this->getJson('/api/roles/99999');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Role not found'
                ]);
    }

    public function test_it_can_update_role()
    {
        $role = Role::create([
            'role_name' => 'Original Role',
            'description' => 'Original description',
            'role_priority' => 10
        ]);

        $updateData = [
            'role_name' => 'Updated Role',
            'description' => 'Updated description',
            'role_priority' => 15
        ];

        $response = $this->putJson("/api/roles/{$role->role_id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Role updated successfully'
                ]);

        $this->assertDatabaseHas('role', [
            'role_id' => $role->role_id,
            'role_name' => 'Updated Role',
            'description' => 'Updated description',
            'role_priority' => 15
        ]);
    }

    public function test_it_can_delete_role()
    {
        $role = Role::create([
            'role_name' => 'Delete Test Role',
            'description' => 'Role to delete',
            'role_priority' => 20
        ]);

        $response = $this->deleteJson("/api/roles/{$role->role_id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Role deleted successfully'
                ]);

        $this->assertDatabaseMissing('role', [
            'role_id' => $role->role_id
        ]);
    }

    public function test_it_cannot_delete_role_assigned_to_users()
    {
        // Create the student first
        $student = Student::create([
            'student_number' => '2024-ROLE-002',
            'first_name' => 'Test',
            'last_name' => 'User',
            'middle_initial' => 'B',
            'email' => 'roleuser2@example.com',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        // Now create the user
        $user = User::create([
            'student_number' => '2024-ROLE-002',
            'password' => bcrypt('password123'),
            'status' => 'active'
        ]);

        $role = Role::create([
            'role_name' => 'Assigned Role',
            'description' => 'Role assigned to user',
            'role_priority' => 25
        ]);

        // Assign role to user using UserRole directly
        UserRole::create([
            'student_number' => $user->student_number,
            'role_id' => $role->role_id,
            'academic_year_id' => $this->academicYear->academic_year_id,
            'start_date' => now()
        ]);

        $response = $this->deleteJson("/api/roles/{$role->role_id}");

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Cannot delete role that is assigned to users'
                ]);
    }

    public function test_it_can_assign_permissions_to_role()
    {
        $role = Role::create([
            'role_name' => 'Permission Test Role',
            'description' => 'Role for permission testing',
            'role_priority' => 30
        ]);

        $permissionIds = $this->permissions->pluck('permission_id')->toArray();

        $response = $this->postJson("/api/roles/{$role->role_id}/permissions", [
            'permissions' => $permissionIds
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Permissions assigned successfully'
                ]);

        $this->assertDatabaseHas('role_permission', [
            'role_id' => $role->role_id,
            'permission_id' => $permissionIds[0]
        ]);
    }

    public function test_it_validates_permission_assignment()
    {
        $role = Role::create([
            'role_name' => 'Validation Test Role',
            'description' => 'Role for validation testing',
            'role_priority' => 35
        ]);

        $response = $this->postJson("/api/roles/{$role->role_id}/permissions", [
            'permissions' => [99999] // Non-existent permission
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['permissions.0']);
    }

    public function test_it_can_get_role_permissions()
    {
        $role = Role::create([
            'role_name' => 'Permissions Test Role',
            'description' => 'Role for getting permissions',
            'role_priority' => 40
        ]);

        $role->permissions()->attach($this->permissions->pluck('permission_id'));

        $response = $this->getJson("/api/roles/{$role->role_id}/permissions");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Role permissions retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'permission_id',
                            'permission_name',
                            'description'
                        ]
                    ]
                ]);
    }

    public function test_it_can_get_all_permissions()
    {
        $response = $this->getJson('/api/roles/permissions/all');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Permissions retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'permission_id',
                            'permission_name',
                            'description'
                        ]
                    ]
                ]);
    }
} 