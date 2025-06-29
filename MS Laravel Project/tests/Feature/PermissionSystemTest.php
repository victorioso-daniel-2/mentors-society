<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AcademicYear;
use App\Models\UserRole;
use App\Models\UserRolePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\RolePermission;

class PermissionSystemTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run the permission seeder
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        
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

        // Create test users
        $this->president = User::create([
            'first_name' => 'John',
            'last_name' => 'President',
            'email' => 'president@example.com',
            'password' => Hash::make('password123')
        ]);

        $this->auditor = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Auditor',
            'email' => 'auditor@example.com',
            'password' => Hash::make('password123')
        ]);

        $this->student = User::create([
            'first_name' => 'Bob',
            'last_name' => 'Student',
            'email' => 'student@example.com',
            'password' => Hash::make('password123')
        ]);

        // Create students
        $this->presidentStudent = Student::create([
            'user_id' => $this->president->user_id,
            'student_number' => '2021-00001-TG-0'
        ]);

        $this->auditorStudent = Student::create([
            'user_id' => $this->auditor->user_id,
            'student_number' => '2021-00002-TG-0'
        ]);

        $this->regularStudent = Student::create([
            'user_id' => $this->student->user_id,
            'student_number' => '2021-00003-TG-0'
        ]);

        // Get roles
        $this->presidentRole = Role::where('role_name', 'President')->first();
        $this->auditorRole = Role::where('role_name', 'Auditor')->first();
        $this->studentRole = Role::where('role_name', 'Student')->first();

        // Assign roles
        UserRole::create([
            'user_id' => $this->president->user_id,
            'role_id' => $this->presidentRole->role_id,
            'academic_year_id' => $academicYear->academic_year_id,
            'start_date' => now(),
            'end_date' => null
        ]);

        UserRole::create([
            'user_id' => $this->auditor->user_id,
            'role_id' => $this->auditorRole->role_id,
            'academic_year_id' => $academicYear->academic_year_id,
            'start_date' => now(),
            'end_date' => null
        ]);

        UserRole::create([
            'user_id' => $this->student->user_id,
            'role_id' => $this->studentRole->role_id,
            'academic_year_id' => $academicYear->academic_year_id,
            'start_date' => now(),
            'end_date' => null
        ]);
    }

    /**
     * Test that President has all permissions
     */
    public function test_president_has_all_permissions()
    {
        $presidentUserRole = $this->president->userRoles()->first();
        
        // Test some key permissions that President should have
        $keyPermissions = [
            'user.manage_roles',
            'financial.delete',
            'system.manage_permissions',
            'event.delete'
        ];

        foreach ($keyPermissions as $permission) {
            $this->assertTrue(
                $presidentUserRole->hasPermission($permission),
                "President should have permission: {$permission}"
            );
        }
    }

    /**
     * Test that Auditor has correct base permissions
     */
    public function test_auditor_has_correct_base_permissions()
    {
        $auditorUserRole = $this->auditor->userRoles()->first();
        
        // Permissions Auditor should have
        $shouldHave = [
            'financial.view',
            'financial.verify',
            'inventory.view',
            'inventory.record_conditions',
            'system.view_logs'
        ];

        foreach ($shouldHave as $permission) {
            $this->assertTrue(
                $auditorUserRole->hasPermission($permission),
                "Auditor should have permission: {$permission}"
            );
        }

        // Permissions Auditor should NOT have
        $shouldNotHave = [
            'financial.create',
            'financial.delete',
            'inventory.create',
            'inventory.delete',
            'user.manage_roles'
        ];

        foreach ($shouldNotHave as $permission) {
            $this->assertFalse(
                $auditorUserRole->hasPermission($permission),
                "Auditor should NOT have permission: {$permission}"
            );
        }
    }

    /**
     * Test that Student has limited permissions
     */
    public function test_student_has_limited_permissions()
    {
        $studentUserRole = $this->student->userRoles()->first();
        
        // Permissions Student should have
        $shouldHave = [
            'event.view',
            'task.view',
            'task.complete',
            'inventory.view'
        ];

        foreach ($shouldHave as $permission) {
            $this->assertTrue(
                $studentUserRole->hasPermission($permission),
                "Student should have permission: {$permission}"
            );
        }

        // Permissions Student should NOT have
        $shouldNotHave = [
            'event.create',
            'event.edit',
            'financial.view',
            'user.manage_roles',
            'system.manage_permissions'
        ];

        foreach ($shouldNotHave as $permission) {
            $this->assertFalse(
                $studentUserRole->hasPermission($permission),
                "Student should NOT have permission: {$permission}"
            );
        }
    }

    /**
     * Test custom permission override
     */
    public function test_custom_permission_override()
    {
        $auditorUserRole = $this->auditor->userRoles()->first();
        $inventoryCreatePermission = Permission::where('permission_name', 'inventory.create')->first();

        // Initially, auditor should NOT have inventory.create permission
        $this->assertFalse($auditorUserRole->hasPermission('inventory.create'));

        // Add custom permission override
        UserRolePermission::create([
            'user_role_id' => $auditorUserRole->user_role_id,
            'permission_id' => $inventoryCreatePermission->permission_id,
            'is_granted' => true,
            'reason' => 'Special assignment for inventory management'
        ]);

        // Now auditor should have inventory.create permission
        $this->assertTrue($auditorUserRole->hasPermission('inventory.create'));

        // Test revoking permission
        UserRolePermission::where([
            'user_role_id' => $auditorUserRole->user_role_id,
            'permission_id' => $inventoryCreatePermission->permission_id
        ])->update(['is_granted' => false]);

        // Auditor should NOT have the permission again
        $this->assertFalse($auditorUserRole->hasPermission('inventory.create'));
    }

    /**
     * Test role priority system
     */
    public function test_role_priority_system()
    {
        $this->assertEquals(1, $this->presidentRole->role_priority);
        $this->assertEquals(5, $this->auditorRole->role_priority);
        $this->assertEquals(99, $this->studentRole->role_priority);

        // President should have higher priority than Auditor
        $this->assertTrue($this->presidentRole->role_priority < $this->auditorRole->role_priority);
        
        // Auditor should have higher priority than Student
        $this->assertTrue($this->auditorRole->role_priority < $this->studentRole->role_priority);
    }

    /**
     * Test user role relationships
     */
    public function test_user_role_relationships()
    {
        // Test user has roles
        $this->assertTrue($this->president->hasRole('President'));
        $this->assertTrue($this->auditor->hasRole('Auditor'));
        $this->assertTrue($this->student->hasRole('Student'));

        // Test user does not have other roles
        $this->assertFalse($this->auditor->hasRole('President'));
        $this->assertFalse($this->student->hasRole('Auditor'));

        // Test user has specific permissions
        $this->assertTrue($this->president->hasPermission('user.manage_roles'));
        $this->assertTrue($this->auditor->hasPermission('financial.verify'));
        $this->assertTrue($this->student->hasPermission('event.view'));

        // Test user does not have specific permissions
        $this->assertFalse($this->auditor->hasPermission('user.manage_roles'));
        $this->assertFalse($this->student->hasPermission('financial.view'));
    }

    public function test_role_hierarchy_resolution()
    {
        // Create roles with different priorities
        $president = Role::create(['role_name' => 'President', 'role_priority' => 1]);
        $vpInternal = Role::create(['role_name' => 'Vice President for Internal Affairs', 'role_priority' => 2]);
        $secretaryGeneral = Role::create(['role_name' => 'Secretary General', 'role_priority' => 4]);
        $student = Role::create(['role_name' => 'Student', 'role_priority' => 99]);

        // Create permissions
        $viewPermission = Permission::create(['permission_name' => 'user.view']);
        $editPermission = Permission::create(['permission_name' => 'user.edit']);

        // Assign permissions to roles
        RolePermission::create(['role_id' => $president->role_id, 'permission_id' => $viewPermission->permission_id]);
        RolePermission::create(['role_id' => $president->role_id, 'permission_id' => $editPermission->permission_id]);
        RolePermission::create(['role_id' => $vpInternal->role_id, 'permission_id' => $viewPermission->permission_id]);
        RolePermission::create(['role_id' => $secretaryGeneral->role_id, 'permission_id' => $viewPermission->permission_id]);

        // Create user with multiple roles
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com'
        ]);

        // Assign roles to user
        UserRole::create([
            'user_id' => $user->user_id,
            'role_id' => $president->role_id,
            'academic_year_id' => 1,
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        UserRole::create([
            'user_id' => $user->user_id,
            'role_id' => $vpInternal->role_id,
            'academic_year_id' => 1,
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        // Test permission resolution - should get highest priority role permissions
        $this->assertTrue($user->hasPermission('user.view'));
        $this->assertTrue($user->hasPermission('user.edit')); // Only president has this
    }

    public function test_custom_user_role_permissions()
    {
        // Create roles
        $treasurer = Role::create(['role_name' => 'Treasurer', 'role_priority' => 6]);
        $auditor = Role::create(['role_name' => 'Auditor', 'role_priority' => 7]);

        // Create permissions
        $financialView = Permission::create(['permission_name' => 'financial.view']);
        $financialEdit = Permission::create(['permission_name' => 'financial.edit']);
        $financialVerify = Permission::create(['permission_name' => 'financial.verify']);

        // Assign base permissions to roles
        RolePermission::create(['role_id' => $treasurer->role_id, 'permission_id' => $financialView->permission_id]);
        RolePermission::create(['role_id' => $treasurer->role_id, 'permission_id' => $financialEdit->permission_id]);
        RolePermission::create(['role_id' => $auditor->role_id, 'permission_id' => $financialView->permission_id]);
        RolePermission::create(['role_id' => $auditor->role_id, 'permission_id' => $financialVerify->permission_id]);

        // Create user with treasurer role
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'Treasurer',
            'email' => 'treasurer@example.com'
        ]);

        $userRole = UserRole::create([
            'user_id' => $user->user_id,
            'role_id' => $treasurer->role_id,
            'academic_year_id' => 1,
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        // Test base permissions
        $this->assertTrue($user->hasPermission('financial.view'));
        $this->assertTrue($user->hasPermission('financial.edit'));
        $this->assertFalse($user->hasPermission('financial.verify'));

        // Add custom permission override
        UserRolePermission::create([
            'user_role_id' => $userRole->user_role_id,
            'permission_id' => $financialVerify->permission_id,
            'is_granted' => true,
            'reason' => 'Special audit assignment'
        ]);

        // Test custom permission
        $this->assertTrue($user->hasPermission('financial.verify'));

        // Revoke a base permission
        UserRolePermission::create([
            'user_role_id' => $userRole->user_role_id,
            'permission_id' => $financialEdit->permission_id,
            'is_granted' => false,
            'reason' => 'Temporary restriction'
        ]);

        $this->assertFalse($user->hasPermission('financial.edit'));
    }

    public function test_academic_year_based_roles()
    {
        // Create academic years
        $academicYear2024 = AcademicYear::create([
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31',
            'description' => 'Academic Year 2024-2025'
        ]);

        $academicYear2025 = AcademicYear::create([
            'start_date' => '2025-06-01',
            'end_date' => '2026-05-31',
            'description' => 'Academic Year 2025-2026'
        ]);

        // Create roles
        $president = Role::create(['role_name' => 'President', 'role_priority' => 1]);
        $secretaryGeneral = Role::create(['role_name' => 'Secretary General', 'role_priority' => 4]);

        // Create permissions
        $userView = Permission::create(['permission_name' => 'user.view']);
        $userEdit = Permission::create(['permission_name' => 'user.edit']);

        // Assign permissions
        RolePermission::create(['role_id' => $president->role_id, 'permission_id' => $userView->permission_id]);
        RolePermission::create(['role_id' => $president->role_id, 'permission_id' => $userEdit->permission_id]);
        RolePermission::create(['role_id' => $secretaryGeneral->role_id, 'permission_id' => $userView->permission_id]);

        // Create user
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com'
        ]);

        // Assign roles for different academic years
        UserRole::create([
            'user_id' => $user->user_id,
            'role_id' => $president->role_id,
            'academic_year_id' => $academicYear2024->academic_year_id,
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31'
        ]);

        UserRole::create([
            'user_id' => $user->user_id,
            'role_id' => $secretaryGeneral->role_id,
            'academic_year_id' => $academicYear2025->academic_year_id,
            'start_date' => '2025-06-01',
            'end_date' => '2026-05-31'
        ]);

        // Test permissions for 2024-2025 (President)
        $this->travelTo('2024-12-01');
        $this->assertTrue($user->hasPermission('user.view'));
        $this->assertTrue($user->hasPermission('user.edit'));

        // Test permissions for 2025-2026 (Secretary General)
        $this->travelTo('2025-12-01');
        $this->assertTrue($user->hasPermission('user.view'));
        $this->assertFalse($user->hasPermission('user.edit'));

        $this->travelBack();
    }

    public function test_specific_officer_permissions()
    {
        // Create specific officer roles
        $proMath = Role::create(['role_name' => 'PRO - Math', 'role_priority' => 8]);
        $proEnglish = Role::create(['role_name' => 'PRO - English', 'role_priority' => 9]);
        $businessManagerMath = Role::create(['role_name' => 'Business Manager - Math', 'role_priority' => 10]);
        $msRepresentative = Role::create(['role_name' => 'MS Representative', 'role_priority' => 12]);

        // Create permissions
        $eventView = Permission::create(['permission_name' => 'event.view']);
        $eventCreate = Permission::create(['permission_name' => 'event.create']);
        $sponsorView = Permission::create(['permission_name' => 'sponsor.view']);
        $inventoryView = Permission::create(['permission_name' => 'inventory.view']);
        $financialView = Permission::create(['permission_name' => 'financial.view']);

        // Assign permissions to PRO roles
        RolePermission::create(['role_id' => $proMath->role_id, 'permission_id' => $eventView->permission_id]);
        RolePermission::create(['role_id' => $proMath->role_id, 'permission_id' => $eventCreate->permission_id]);
        RolePermission::create(['role_id' => $proMath->role_id, 'permission_id' => $sponsorView->permission_id]);

        RolePermission::create(['role_id' => $proEnglish->role_id, 'permission_id' => $eventView->permission_id]);
        RolePermission::create(['role_id' => $proEnglish->role_id, 'permission_id' => $eventCreate->permission_id]);
        RolePermission::create(['role_id' => $proEnglish->role_id, 'permission_id' => $sponsorView->permission_id]);

        // Assign permissions to Business Manager roles
        RolePermission::create(['role_id' => $businessManagerMath->role_id, 'permission_id' => $inventoryView->permission_id]);
        RolePermission::create(['role_id' => $businessManagerMath->role_id, 'permission_id' => $financialView->permission_id]);

        // Assign permissions to MS Representative
        RolePermission::create(['role_id' => $msRepresentative->role_id, 'permission_id' => $eventView->permission_id]);
        RolePermission::create(['role_id' => $msRepresentative->role_id, 'permission_id' => $eventCreate->permission_id]);

        // Create users for each role
        $proMathUser = User::create(['first_name' => 'PRO', 'last_name' => 'Math', 'email' => 'promath@example.com']);
        $proEnglishUser = User::create(['first_name' => 'PRO', 'last_name' => 'English', 'email' => 'proenglish@example.com']);
        $businessManagerUser = User::create(['first_name' => 'Business', 'last_name' => 'Manager', 'email' => 'business@example.com']);
        $msRepUser = User::create(['first_name' => 'MS', 'last_name' => 'Rep', 'email' => 'msrep@example.com']);

        // Assign roles
        UserRole::create([
            'user_id' => $proMathUser->user_id,
            'role_id' => $proMath->role_id,
            'academic_year_id' => 1,
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        UserRole::create([
            'user_id' => $proEnglishUser->user_id,
            'role_id' => $proEnglish->role_id,
            'academic_year_id' => 1,
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        UserRole::create([
            'user_id' => $businessManagerUser->user_id,
            'role_id' => $businessManagerMath->role_id,
            'academic_year_id' => 1,
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        UserRole::create([
            'user_id' => $msRepUser->user_id,
            'role_id' => $msRepresentative->role_id,
            'academic_year_id' => 1,
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        // Test PRO permissions
        $this->assertTrue($proMathUser->hasPermission('event.view'));
        $this->assertTrue($proMathUser->hasPermission('event.create'));
        $this->assertTrue($proMathUser->hasPermission('sponsor.view'));
        $this->assertFalse($proMathUser->hasPermission('inventory.view'));

        $this->assertTrue($proEnglishUser->hasPermission('event.view'));
        $this->assertTrue($proEnglishUser->hasPermission('event.create'));
        $this->assertTrue($proEnglishUser->hasPermission('sponsor.view'));
        $this->assertFalse($proEnglishUser->hasPermission('inventory.view'));

        // Test Business Manager permissions
        $this->assertTrue($businessManagerUser->hasPermission('inventory.view'));
        $this->assertTrue($businessManagerUser->hasPermission('financial.view'));
        $this->assertFalse($businessManagerUser->hasPermission('event.create'));

        // Test MS Representative permissions
        $this->assertTrue($msRepUser->hasPermission('event.view'));
        $this->assertTrue($msRepUser->hasPermission('event.create'));
        $this->assertFalse($msRepUser->hasPermission('inventory.view'));
    }
} 