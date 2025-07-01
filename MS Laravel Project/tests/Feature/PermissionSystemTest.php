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
    use RefreshDatabase, WithFaker;

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
        $academicYear = AcademicYear::firstOrCreate([
            'academic_year_id' => 1
        ], [
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31',
            'description' => 'Academic Year 2024-2025'
        ]);

        // Create students FIRST
        $this->presidentStudent = Student::firstOrCreate([
            'student_number' => '2021-00001-TG-0'
        ], [
            'first_name' => 'John',
            'last_name' => 'President',
            'middle_initial' => 'A',
            'email' => 'president@example.com',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active',
        ]);
        $this->auditorStudent = Student::firstOrCreate([
            'student_number' => '2021-00002-TG-0'
        ], [
            'first_name' => 'Jane',
            'last_name' => 'Auditor',
            'middle_initial' => 'B',
            'email' => 'auditor@example.com',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active',
        ]);
        $this->regularStudent = Student::firstOrCreate([
            'student_number' => '2021-00003-TG-0'
        ], [
            'first_name' => 'Bob',
            'last_name' => 'Student',
            'middle_initial' => 'C',
            'email' => 'student@example.com',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active',
        ]);

        // Now create users with matching student_number
        $this->president = User::firstOrCreate([
            'student_number' => '2021-00001-TG-0'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        $this->auditor = User::firstOrCreate([
            'student_number' => '2021-00002-TG-0'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        $this->student = User::firstOrCreate([
            'student_number' => '2021-00003-TG-0'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);

        // Get roles
        $this->presidentRole = Role::firstOrCreate([
            'role_name' => 'President'
        ], [
            'role_priority' => 1
        ]);
        $this->auditorRole = Role::firstOrCreate([
            'role_name' => 'Auditor'
        ], [
            'role_priority' => 7
        ]);
        $this->studentRole = Role::firstOrCreate([
            'role_name' => 'Student'
        ], [
            'role_priority' => 99
        ]);

        // Assign roles (use student_number)
        UserRole::firstOrCreate([
            'user_role_id' => 1
        ], [
            'student_number' => '2021-00001-TG-0',
            'role_id' => $this->presidentRole->role_id,
            'academic_year_id' => $academicYear->academic_year_id,
            'start_date' => now(),
            'end_date' => null
        ]);
        UserRole::firstOrCreate([
            'user_role_id' => 2
        ], [
            'student_number' => '2021-00002-TG-0',
            'role_id' => $this->auditorRole->role_id,
            'academic_year_id' => $academicYear->academic_year_id,
            'start_date' => now(),
            'end_date' => null
        ]);
        UserRole::firstOrCreate([
            'user_role_id' => 3
        ], [
            'student_number' => '2021-00003-TG-0',
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
        $presidentStudent = $this->presidentStudent;
        $presidentUser = $this->president;
        
        // Test some key permissions that President should have
        $keyPermissions = [
            'user.manage_roles',
            'financial.delete',
            'system.manage_permissions',
            'event.delete'
        ];

        foreach ($keyPermissions as $permission) {
            $this->assertTrue(
                $presidentUser->hasPermission($permission),
                "President should have permission: {$permission}"
            );
        }
    }

    /**
     * Test that Auditor has correct base permissions
     */
    public function test_auditor_has_correct_base_permissions()
    {
        $auditorStudent = $this->auditorStudent;
        $auditorUser = $this->auditor;
        
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
                $auditorUser->hasPermission($permission),
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
                $auditorUser->hasPermission($permission),
                "Auditor should NOT have permission: {$permission}"
            );
        }
    }

    /**
     * Test student has limited permissions
     */
    public function test_student_has_limited_permissions()
    {
        $studentStudent = $this->regularStudent;
        $studentUser = $this->student;
        
        // Permissions Student should have
        $shouldHave = [
            'user.edit', // Can edit their own profile
            'event.view',
            'inventory.view'
        ];

        foreach ($shouldHave as $permission) {
            $this->assertTrue(
                $studentUser->hasPermission($permission),
                "Student should have permission: {$permission}"
            );
        }

        // Permissions Student should NOT have
        $shouldNotHave = [
            'event.create',
            'event.edit',
            'task.view',
            'task.complete',
            'financial.view',
            'user.manage_roles',
            'system.manage_permissions'
        ];

        foreach ($shouldNotHave as $permission) {
            $this->assertFalse(
                $studentUser->hasPermission($permission),
                "Student should NOT have permission: {$permission}"
            );
        }
    }

    /**
     * Test custom permission override
     */
    public function test_custom_permission_override()
    {
        $auditorStudent = $this->auditorStudent;
        $auditorUser = $this->auditor;
        $inventoryCreatePermission = Permission::where('permission_name', 'inventory.create')->first();

        // Initially, auditor should NOT have inventory.create permission
        $this->assertFalse($auditorUser->hasPermission('inventory.create'));

        // Add custom permission override
        UserRolePermission::create([
            'user_role_id' => $auditorUser->userRoles()->first()->user_role_id,
            'permission_id' => $inventoryCreatePermission->permission_id,
            'is_granted' => true,
            'reason' => 'Special assignment for inventory management'
        ]);

        // Now auditor should have inventory.create permission
        $this->assertTrue($auditorUser->hasPermission('inventory.create'));

        // Test revoking permission
        UserRolePermission::where([
            'user_role_id' => $auditorUser->userRoles()->first()->user_role_id,
            'permission_id' => $inventoryCreatePermission->permission_id
        ])->update(['is_granted' => false]);

        // Auditor should NOT have the permission again
        $this->assertFalse($auditorUser->hasPermission('inventory.create'));
    }

    /**
     * Test role priority system
     */
    public function test_role_priority_system()
    {
        $this->assertEquals(1, $this->presidentRole->role_priority);
        $this->assertEquals(7, $this->auditorRole->role_priority);
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
        // Ensure academic year exists
        $academicYear = AcademicYear::firstOrCreate([
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31'
        ], [
            'description' => 'Academic Year 2024-2025'
        ]);

        // Create roles with different priorities
        $president = Role::firstOrCreate(['role_name' => 'President'], ['role_priority' => 1]);
        $vpInternal = Role::firstOrCreate(['role_name' => 'Vice President for Internal Affairs'], ['role_priority' => 2]);
        $secretaryGeneral = Role::firstOrCreate(['role_name' => 'Secretary General'], ['role_priority' => 4]);
        $student = Role::firstOrCreate(['role_name' => 'Student'], ['role_priority' => 99]);

        // Create permissions
        $viewPermission = Permission::firstOrCreate(['permission_name' => 'user.view']);
        $editPermission = Permission::firstOrCreate(['permission_name' => 'user.edit']);

        // Assign permissions to roles
        RolePermission::firstOrCreate(['role_id' => $president->role_id, 'permission_id' => $viewPermission->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $president->role_id, 'permission_id' => $editPermission->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $vpInternal->role_id, 'permission_id' => $viewPermission->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $secretaryGeneral->role_id, 'permission_id' => $viewPermission->permission_id]);

        // Create user with multiple roles
        $student = Student::firstOrCreate([
            'student_number' => '2024-TEST-001',
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'middle_initial' => 'A',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        $testUser = User::firstOrCreate([
            'student_number' => '2024-TEST-001'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        $user = $testUser;

        // Assign roles to user
        UserRole::firstOrCreate([
            'student_number' => $user->student_number,
            'role_id' => $president->role_id,
            'academic_year_id' => $academicYear->academic_year_id
        ], [
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        UserRole::firstOrCreate([
            'student_number' => $user->student_number,
            'role_id' => $vpInternal->role_id,
            'academic_year_id' => $academicYear->academic_year_id
        ], [
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        // Test permission resolution - should get highest priority role permissions
        $this->assertTrue($user->hasPermission('user.view'));
        $this->assertTrue($user->hasPermission('user.edit')); // Only president has this
    }

    public function test_custom_user_role_permissions()
    {
        // Ensure academic year exists
        $academicYear = AcademicYear::firstOrCreate([
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31'
        ], [
            'description' => 'Academic Year 2024-2025'
        ]);

        // Create roles
        $treasurer = Role::firstOrCreate(['role_name' => 'Treasurer'], ['role_priority' => 6]);
        $auditor = Role::firstOrCreate(['role_name' => 'Auditor'], ['role_priority' => 7]);

        // Create permissions
        $financialView = Permission::firstOrCreate(['permission_name' => 'financial.view']);
        $financialEdit = Permission::firstOrCreate(['permission_name' => 'financial.edit']);
        $inventoryCreate = Permission::firstOrCreate(['permission_name' => 'inventory.create']);

        // Assign base permissions to roles
        RolePermission::firstOrCreate(['role_id' => $treasurer->role_id, 'permission_id' => $financialView->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $treasurer->role_id, 'permission_id' => $financialEdit->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $auditor->role_id, 'permission_id' => $financialView->permission_id]);

        // Create student and user for treasurer
        $treasurerStudent = Student::firstOrCreate([
            'student_number' => '2024-TREASURER-001',
            'email' => 'treasurer@example.com',
            'first_name' => 'Test',
            'last_name' => 'Treasurer',
            'middle_initial' => 'B',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        $treasurerUser = User::firstOrCreate([
            'student_number' => '2024-TREASURER-001'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        $user = $treasurerUser;

        $userRole = UserRole::firstOrCreate([
            'student_number' => $user->student_number,
            'role_id' => $treasurer->role_id,
            'academic_year_id' => $academicYear->academic_year_id
        ], [
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        // Test base permissions
        $this->assertTrue($user->hasPermission('financial.view'));
        $this->assertTrue($user->hasPermission('financial.edit'));
        $this->assertFalse($user->hasPermission('inventory.create'));

        // Add custom permission override
        UserRolePermission::firstOrCreate([
            'user_role_id' => $userRole->user_role_id,
            'permission_id' => $inventoryCreate->permission_id
        ], [
            'is_granted' => true,
            'reason' => 'Special inventory assignment'
        ]);

        // Test custom permission
        $this->assertTrue($user->hasPermission('inventory.create'));

        // Revoke a base permission
        UserRolePermission::firstOrCreate([
            'user_role_id' => $userRole->user_role_id,
            'permission_id' => $financialEdit->permission_id
        ], [
            'is_granted' => false,
            'reason' => 'Temporary restriction'
        ]);

        $this->assertFalse($user->hasPermission('financial.edit'));
    }

    public function test_academic_year_based_roles()
    {
        // Create academic years
        $academicYear2024 = AcademicYear::firstOrCreate([
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31'
        ], [
            'description' => 'Academic Year 2024-2025'
        ]);

        $academicYear2025 = AcademicYear::firstOrCreate([
            'start_date' => '2025-06-01',
            'end_date' => '2026-05-31'
        ], [
            'description' => 'Academic Year 2025-2026'
        ]);

        // Create roles
        $president = Role::firstOrCreate(['role_name' => 'President'], ['role_priority' => 1]);
        $secretaryGeneral = Role::firstOrCreate(['role_name' => 'Secretary General'], ['role_priority' => 4]);

        // Create permissions
        $userView = Permission::firstOrCreate(['permission_name' => 'user.view']);
        $systemManageRoles = Permission::firstOrCreate(['permission_name' => 'system.manage_roles']);

        // Assign permissions
        RolePermission::firstOrCreate(['role_id' => $president->role_id, 'permission_id' => $userView->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $president->role_id, 'permission_id' => $systemManageRoles->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $secretaryGeneral->role_id, 'permission_id' => $userView->permission_id]);

        // Create user
        $student = Student::firstOrCreate([
            'student_number' => '2024-TEST-001',
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'middle_initial' => 'A',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        $testUser = User::firstOrCreate([
            'student_number' => '2024-TEST-001'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        $user = $testUser;

        // Assign roles for different academic years
        UserRole::firstOrCreate([
            'student_number' => $user->student_number,
            'role_id' => $president->role_id,
            'academic_year_id' => $academicYear2024->academic_year_id
        ], [
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31'
        ]);

        UserRole::firstOrCreate([
            'student_number' => $user->student_number,
            'role_id' => $secretaryGeneral->role_id,
            'academic_year_id' => $academicYear2025->academic_year_id
        ], [
            'start_date' => '2025-06-01',
            'end_date' => '2026-05-31'
        ]);

        // Test permissions for 2024-2025 (President)
        $this->travelTo('2024-12-01');
        $this->assertTrue($user->hasPermission('user.view'));
        $this->assertTrue($user->hasPermission('system.manage_roles'));

        // Test permissions for 2025-2026 (Secretary General)
        $this->travelTo('2025-12-01');
        $this->assertTrue($user->hasPermission('user.view'));
        $this->assertFalse($user->hasPermission('system.manage_roles'));

        $this->travelBack();
    }

    public function test_specific_officer_permissions()
    {
        // Ensure academic year exists
        $academicYear = AcademicYear::firstOrCreate([
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31'
        ], [
            'description' => 'Academic Year 2024-2025'
        ]);

        // Create specific officer roles
        $proMath = Role::firstOrCreate(['role_name' => 'PRO - Math'], ['role_priority' => 8]);
        $proEnglish = Role::firstOrCreate(['role_name' => 'PRO - English'], ['role_priority' => 9]);
        $businessManagerMath = Role::firstOrCreate(['role_name' => 'Business Manager - Math'], ['role_priority' => 10]);
        $msRepresentative = Role::firstOrCreate(['role_name' => 'MS Representative'], ['role_priority' => 12]);

        // Create permissions
        $eventView = Permission::firstOrCreate(['permission_name' => 'event.view']);
        $eventCreate = Permission::firstOrCreate(['permission_name' => 'event.create']);
        $sponsorView = Permission::firstOrCreate(['permission_name' => 'sponsor.view']);
        $inventoryView = Permission::firstOrCreate(['permission_name' => 'inventory.view']);
        $financialView = Permission::firstOrCreate(['permission_name' => 'financial.view']);

        // Assign permissions to PRO roles
        RolePermission::firstOrCreate(['role_id' => $proMath->role_id, 'permission_id' => $eventView->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $proMath->role_id, 'permission_id' => $eventCreate->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $proMath->role_id, 'permission_id' => $sponsorView->permission_id]);

        RolePermission::firstOrCreate(['role_id' => $proEnglish->role_id, 'permission_id' => $eventView->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $proEnglish->role_id, 'permission_id' => $eventCreate->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $proEnglish->role_id, 'permission_id' => $sponsorView->permission_id]);

        // Assign permissions to Business Manager roles
        RolePermission::firstOrCreate(['role_id' => $businessManagerMath->role_id, 'permission_id' => $inventoryView->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $businessManagerMath->role_id, 'permission_id' => $financialView->permission_id]);

        // Assign permissions to MS Representative
        RolePermission::firstOrCreate(['role_id' => $msRepresentative->role_id, 'permission_id' => $eventView->permission_id]);
        RolePermission::firstOrCreate(['role_id' => $msRepresentative->role_id, 'permission_id' => $eventCreate->permission_id]);

        // Create users for each role
        $proMathStudent = Student::firstOrCreate([
            'student_number' => '2024-PROMATH-001',
            'email' => 'promath@example.com',
            'first_name' => 'PRO',
            'last_name' => 'Math',
            'middle_initial' => 'C',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        $proMathUser = User::firstOrCreate([
            'student_number' => '2024-PROMATH-001'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        $proEnglishStudent = Student::firstOrCreate([
            'student_number' => '2024-PROENGLISH-001',
            'email' => 'proenglish@example.com',
            'first_name' => 'PRO',
            'last_name' => 'English',
            'middle_initial' => 'D',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        $proEnglishUser = User::firstOrCreate([
            'student_number' => '2024-PROENGLISH-001'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        $businessManagerStudent = Student::firstOrCreate([
            'student_number' => '2024-BUSINESS-001',
            'email' => 'business@example.com',
            'first_name' => 'Business',
            'last_name' => 'Manager',
            'middle_initial' => 'E',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        $businessManagerUser = User::firstOrCreate([
            'student_number' => '2024-BUSINESS-001'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        $msRepStudent = Student::firstOrCreate([
            'student_number' => '2024-MSREP-001',
            'email' => 'msrep@example.com',
            'first_name' => 'MS',
            'last_name' => 'Rep',
            'middle_initial' => 'F',
            'course' => 'BSIT',
            'year_level' => 'Fourth Year',
            'section' => 'A',
            'academic_status' => 'active'
        ]);
        $msRepUser = User::firstOrCreate([
            'student_number' => '2024-MSREP-001'
        ], [
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);

        // Assign roles
        UserRole::firstOrCreate([
            'student_number' => $proMathUser->student_number,
            'role_id' => $proMath->role_id,
            'academic_year_id' => $academicYear->academic_year_id
        ], [
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        UserRole::firstOrCreate([
            'student_number' => $proEnglishUser->student_number,
            'role_id' => $proEnglish->role_id,
            'academic_year_id' => $academicYear->academic_year_id
        ], [
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        UserRole::firstOrCreate([
            'student_number' => $businessManagerUser->student_number,
            'role_id' => $businessManagerMath->role_id,
            'academic_year_id' => $academicYear->academic_year_id
        ], [
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);

        UserRole::firstOrCreate([
            'student_number' => $msRepUser->student_number,
            'role_id' => $msRepresentative->role_id,
            'academic_year_id' => $academicYear->academic_year_id
        ], [
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