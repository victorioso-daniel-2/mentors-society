<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =============================================
        // CREATE ALL PERMISSIONS
        // =============================================

        $permissions = [
            // =============================================
            // USER MANAGEMENT PERMISSIONS
            // =============================================
            ['permission_name' => 'user.view', 'description' => 'View user profiles'],
            ['permission_name' => 'user.create', 'description' => 'Create new users'],
            ['permission_name' => 'user.edit', 'description' => 'Edit user information'],
            ['permission_name' => 'user.delete', 'description' => 'Delete users'],
            ['permission_name' => 'user.manage_roles', 'description' => 'Assign/remove roles from users'],

            // =============================================
            // STUDENT MANAGEMENT PERMISSIONS
            // =============================================
            ['permission_name' => 'student.view', 'description' => 'View student information'],
            ['permission_name' => 'student.create', 'description' => 'Add new students'],
            ['permission_name' => 'student.edit', 'description' => 'Edit student information'],
            ['permission_name' => 'student.delete', 'description' => 'Remove students'],
            ['permission_name' => 'student.manage_classes', 'description' => 'Assign students to classes'],
            ['permission_name' => 'student.import_csv', 'description' => 'Import students from CSV'],

            // =============================================
            // EVENT MANAGEMENT PERMISSIONS
            // =============================================
            ['permission_name' => 'event.view', 'description' => 'View events'],
            ['permission_name' => 'event.create', 'description' => 'Create new events'],
            ['permission_name' => 'event.edit', 'description' => 'Edit event details'],
            ['permission_name' => 'event.delete', 'description' => 'Delete events'],
            ['permission_name' => 'event.manage_registrations', 'description' => 'Manage event registrations'],
            ['permission_name' => 'event.manage_participants', 'description' => 'Manage event participants'],
            ['permission_name' => 'event.view_evaluations', 'description' => 'View event evaluations'],
            ['permission_name' => 'event.export_data', 'description' => 'Export event data'],

            // =============================================
            // FINANCIAL MANAGEMENT PERMISSIONS
            // =============================================
            ['permission_name' => 'financial.view', 'description' => 'View financial records'],
            ['permission_name' => 'financial.create', 'description' => 'Create financial transactions'],
            ['permission_name' => 'financial.edit', 'description' => 'Edit financial records'],
            ['permission_name' => 'financial.delete', 'description' => 'Delete financial records'],
            ['permission_name' => 'financial.verify', 'description' => 'Verify financial transactions'],
            ['permission_name' => 'financial.export', 'description' => 'Export financial reports'],
            ['permission_name' => 'financial.view_receipts', 'description' => 'View receipt photos'],

            // =============================================
            // INVENTORY MANAGEMENT PERMISSIONS
            // =============================================
            ['permission_name' => 'inventory.view', 'description' => 'View inventory items'],
            ['permission_name' => 'inventory.create', 'description' => 'Add new inventory items'],
            ['permission_name' => 'inventory.edit', 'description' => 'Edit inventory items'],
            ['permission_name' => 'inventory.delete', 'description' => 'Delete inventory items'],
            ['permission_name' => 'inventory.manage_borrowings', 'description' => 'Manage item borrowings'],
            ['permission_name' => 'inventory.record_conditions', 'description' => 'Record item conditions'],
            ['permission_name' => 'inventory.export', 'description' => 'Export inventory reports'],

            // =============================================
            // SPONSOR MANAGEMENT PERMISSIONS
            // =============================================
            ['permission_name' => 'sponsor.view', 'description' => 'View sponsors'],
            ['permission_name' => 'sponsor.create', 'description' => 'Add new sponsors'],
            ['permission_name' => 'sponsor.edit', 'description' => 'Edit sponsor information'],
            ['permission_name' => 'sponsor.delete', 'description' => 'Delete sponsors'],
            ['permission_name' => 'sponsor.assign_events', 'description' => 'Assign sponsors to events'],

            // =============================================
            // TASK MANAGEMENT PERMISSIONS
            // =============================================
            ['permission_name' => 'task.view', 'description' => 'View tasks'],
            ['permission_name' => 'task.create', 'description' => 'Create new tasks'],
            ['permission_name' => 'task.edit', 'description' => 'Edit task details'],
            ['permission_name' => 'task.delete', 'description' => 'Delete tasks'],
            ['permission_name' => 'task.assign', 'description' => 'Assign tasks to officers'],
            ['permission_name' => 'task.complete', 'description' => 'Mark tasks as complete'],

            // =============================================
            // REPORTING PERMISSIONS
            // =============================================
            ['permission_name' => 'report.view', 'description' => 'View reports'],
            ['permission_name' => 'report.generate', 'description' => 'Generate reports'],
            ['permission_name' => 'report.export', 'description' => 'Export reports'],
            ['permission_name' => 'report.dashboard', 'description' => 'Access dashboard'],

            // =============================================
            // SYSTEM ADMINISTRATION PERMISSIONS
            // =============================================
            ['permission_name' => 'system.manage_roles', 'description' => 'Manage system roles'],
            ['permission_name' => 'system.manage_permissions', 'description' => 'Manage system permissions'],
            ['permission_name' => 'system.view_logs', 'description' => 'View system logs'],
            ['permission_name' => 'system.backup', 'description' => 'Create system backups'],

            // =============================================
            // ACADEMIC YEAR MANAGEMENT PERMISSIONS
            // =============================================
            ['permission_name' => 'academic_year.view', 'description' => 'View academic years'],
            ['permission_name' => 'academic_year.create', 'description' => 'Create academic years'],
            ['permission_name' => 'academic_year.edit', 'description' => 'Edit academic years'],
            ['permission_name' => 'academic_year.delete', 'description' => 'Delete academic years'],

            // =============================================
            // CLASS MANAGEMENT PERMISSIONS
            // =============================================
            ['permission_name' => 'class.view', 'description' => 'View classes'],
            ['permission_name' => 'class.create', 'description' => 'Create new classes'],
            ['permission_name' => 'class.edit', 'description' => 'Edit class information'],
            ['permission_name' => 'class.delete', 'description' => 'Delete classes'],
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // =============================================
        // CREATE ROLES WITH PRIORITY LEVELS
        // =============================================

        $roles = [
            ['role_name' => 'President', 'description' => 'Organization President', 'role_priority' => 1],
            ['role_name' => 'Vice President for Internal Affairs', 'description' => 'VP for Internal Affairs', 'role_priority' => 2],
            ['role_name' => 'Vice President for External Affairs', 'description' => 'VP for External Affairs', 'role_priority' => 3],
            ['role_name' => 'Secretary General', 'description' => 'Secretary General', 'role_priority' => 4],
            ['role_name' => 'Assistant Secretary', 'description' => 'Assistant Secretary', 'role_priority' => 5],
            ['role_name' => 'Treasurer', 'description' => 'Organization Treasurer', 'role_priority' => 6],
            ['role_name' => 'Auditor', 'description' => 'Organization Auditor', 'role_priority' => 7],
            ['role_name' => 'PRO - Math', 'description' => 'Public Relations Officer for Math', 'role_priority' => 8],
            ['role_name' => 'PRO - English', 'description' => 'Public Relations Officer for English', 'role_priority' => 9],
            ['role_name' => 'Business Manager - Math', 'description' => 'Business Manager for Math', 'role_priority' => 10],
            ['role_name' => 'Business Manager - English', 'description' => 'Business Manager for English', 'role_priority' => 11],
            ['role_name' => 'MS Representative', 'description' => 'MS Representative', 'role_priority' => 12],
            ['role_name' => 'Student', 'description' => 'Regular Student Member', 'role_priority' => 99],
        ];

        // Create all roles
        foreach ($roles as $role) {
            Role::create($role);
        }

        // =============================================
        // ASSIGN PERMISSIONS TO ROLES
        // =============================================

        $this->assignRolePermissions();
    }

    /**
     * Assign permissions to roles
     */
    private function assignRolePermissions(): void
    {
        // =============================================
        // PRESIDENT PERMISSIONS (ALL PERMISSIONS)
        // =============================================
        $presidentRole = Role::where('role_name', 'President')->first();
        $allPermissions = Permission::all();
        foreach ($allPermissions as $permission) {
            RolePermission::create([
                'role_id' => $presidentRole->role_id,
                'permission_id' => $permission->permission_id
            ]);
        }

        // =============================================
        // VICE PRESIDENT FOR INTERNAL AFFAIRS PERMISSIONS
        // =============================================
        $vpInternalRole = Role::where('role_name', 'Vice President for Internal Affairs')->first();
        $vpInternalPermissions = [
            'user.view', 'user.edit',
            'student.view', 'student.edit', 'student.manage_classes',
            'event.view', 'event.create', 'event.edit', 'event.manage_registrations', 'event.manage_participants', 'event.view_evaluations',
            'financial.view', 'financial.create', 'financial.edit', 'financial.verify',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.manage_borrowings',
            'sponsor.view', 'sponsor.create', 'sponsor.edit', 'sponsor.assign_events',
            'task.view', 'task.create', 'task.edit', 'task.assign', 'task.complete',
            'report.view', 'report.generate', 'report.dashboard',
            'academic_year.view', 'class.view', 'class.create', 'class.edit'
        ];
        $this->assignPermissionsToRole($vpInternalRole, $vpInternalPermissions);

        // =============================================
        // VICE PRESIDENT FOR EXTERNAL AFFAIRS PERMISSIONS
        // =============================================
        $vpExternalRole = Role::where('role_name', 'Vice President for External Affairs')->first();
        $vpExternalPermissions = [
            'event.view', 'event.create', 'event.edit', 'event.manage_registrations', 'event.manage_participants',
            'sponsor.view', 'sponsor.create', 'sponsor.edit', 'sponsor.assign_events',
            'task.view', 'task.create', 'task.edit', 'task.assign', 'task.complete',
            'report.view', 'report.generate',
            'financial.view', 'financial.create', 'financial.edit'
        ];
        $this->assignPermissionsToRole($vpExternalRole, $vpExternalPermissions);

        // =============================================
        // SECRETARY GENERAL PERMISSIONS
        // =============================================
        $secretaryGeneralRole = Role::where('role_name', 'Secretary General')->first();
        $secretaryGeneralPermissions = [
            'user.view', 'user.create', 'user.edit',
            'student.view', 'student.create', 'student.edit', 'student.manage_classes', 'student.import_csv',
            'event.view', 'event.create', 'event.edit', 'event.manage_registrations', 'event.manage_participants',
            'task.view', 'task.create', 'task.edit', 'task.assign', 'task.complete',
            'report.view', 'report.generate',
            'academic_year.view', 'class.view', 'class.create', 'class.edit'
        ];
        $this->assignPermissionsToRole($secretaryGeneralRole, $secretaryGeneralPermissions);

        // =============================================
        // ASSISTANT SECRETARY PERMISSIONS
        // =============================================
        $assistantSecretaryRole = Role::where('role_name', 'Assistant Secretary')->first();
        $assistantSecretaryPermissions = [
            'user.view', 'user.edit',
            'student.view', 'student.edit', 'student.manage_classes',
            'event.view', 'event.edit', 'event.manage_registrations',
            'task.view', 'task.edit', 'task.complete',
            'report.view', 'report.generate',
            'academic_year.view', 'class.view', 'class.edit'
        ];
        $this->assignPermissionsToRole($assistantSecretaryRole, $assistantSecretaryPermissions);

        // =============================================
        // TREASURER PERMISSIONS
        // =============================================
        $treasurerRole = Role::where('role_name', 'Treasurer')->first();
        $treasurerPermissions = [
            'financial.view', 'financial.create', 'financial.edit', 'financial.verify', 'financial.export', 'financial.view_receipts',
            'event.view', 'event.view_evaluations',
            'report.view', 'report.generate', 'report.export',
            'sponsor.view', 'sponsor.create', 'sponsor.edit'
        ];
        $this->assignPermissionsToRole($treasurerRole, $treasurerPermissions);

        // =============================================
        // AUDITOR PERMISSIONS
        // =============================================
        $auditorRole = Role::where('role_name', 'Auditor')->first();
        $auditorPermissions = [
            'financial.view', 'financial.verify', 'financial.export', 'financial.view_receipts',
            'inventory.view', 'inventory.record_conditions', 'inventory.export',
            'event.view', 'event.view_evaluations',
            'report.view', 'report.generate', 'report.export',
            'system.view_logs'
        ];
        $this->assignPermissionsToRole($auditorRole, $auditorPermissions);

        // =============================================
        // PRO - MATH PERMISSIONS
        // =============================================
        $proMathRole = Role::where('role_name', 'PRO - Math')->first();
        $proMathPermissions = [
            'event.view', 'event.create', 'event.edit', 'event.manage_registrations',
            'sponsor.view', 'sponsor.create', 'sponsor.edit', 'sponsor.assign_events',
            'task.view', 'task.create', 'task.edit', 'task.complete',
            'report.view', 'report.generate'
        ];
        $this->assignPermissionsToRole($proMathRole, $proMathPermissions);

        // =============================================
        // PRO - ENGLISH PERMISSIONS
        // =============================================
        $proEnglishRole = Role::where('role_name', 'PRO - English')->first();
        $proEnglishPermissions = [
            'event.view', 'event.create', 'event.edit', 'event.manage_registrations',
            'sponsor.view', 'sponsor.create', 'sponsor.edit', 'sponsor.assign_events',
            'task.view', 'task.create', 'task.edit', 'task.complete',
            'report.view', 'report.generate'
        ];
        $this->assignPermissionsToRole($proEnglishRole, $proEnglishPermissions);

        // =============================================
        // BUSINESS MANAGER - MATH PERMISSIONS
        // =============================================
        $businessManagerMathRole = Role::where('role_name', 'Business Manager - Math')->first();
        $businessManagerMathPermissions = [
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.manage_borrowings', 'inventory.record_conditions', 'inventory.export',
            'financial.view', 'financial.create', 'financial.edit',
            'sponsor.view', 'sponsor.create', 'sponsor.edit',
            'report.view', 'report.generate'
        ];
        $this->assignPermissionsToRole($businessManagerMathRole, $businessManagerMathPermissions);

        // =============================================
        // BUSINESS MANAGER - ENGLISH PERMISSIONS
        // =============================================
        $businessManagerEnglishRole = Role::where('role_name', 'Business Manager - English')->first();
        $businessManagerEnglishPermissions = [
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.manage_borrowings', 'inventory.record_conditions', 'inventory.export',
            'financial.view', 'financial.create', 'financial.edit',
            'sponsor.view', 'sponsor.create', 'sponsor.edit',
            'report.view', 'report.generate'
        ];
        $this->assignPermissionsToRole($businessManagerEnglishRole, $businessManagerEnglishPermissions);

        // =============================================
        // MS REPRESENTATIVE PERMISSIONS
        // =============================================
        $msRepresentativeRole = Role::where('role_name', 'MS Representative')->first();
        $msRepresentativePermissions = [
            'event.view', 'event.create', 'event.edit', 'event.manage_registrations', 'event.manage_participants', 'event.view_evaluations',
            'task.view', 'task.create', 'task.edit', 'task.assign', 'task.complete',
            'report.view', 'report.generate'
        ];
        $this->assignPermissionsToRole($msRepresentativeRole, $msRepresentativePermissions);

        // =============================================
        // STUDENT PERMISSIONS (BASIC PERMISSIONS)
        // =============================================
        $studentRole = Role::where('role_name', 'Student')->first();
        $studentPermissions = [
            'event.view',
            'task.view', 'task.complete',
            'inventory.view'
        ];
        $this->assignPermissionsToRole($studentRole, $studentPermissions);
    }

    /**
     * Helper method to assign permissions to a role
     */
    private function assignPermissionsToRole(Role $role, array $permissionNames): void
    {
        $permissions = Permission::whereIn('permission_name', $permissionNames)->get();
        
        foreach ($permissions as $permission) {
            RolePermission::create([
                'role_id' => $role->role_id,
                'permission_id' => $permission->permission_id
            ]);
        }
    }
} 