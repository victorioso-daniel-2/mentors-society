# 🏛️ Mentors Society Permission System

## 📋 **Overview**

The Mentors Society application implements a comprehensive role-based access control (RBAC) system with hierarchical permissions. This system ensures that users can only access features and perform actions appropriate to their role within the organization.

## 🏛️ **Role Hierarchy (Priority Levels)**

| Role                           | Priority | Description                             |
|--------------------------------|----------|-----------------------------------------|
| President                      | 1        | Organization President (All permissions)|
| VPI                            | 2        | VP for Internal Affairs                 |
| VpEx                           | 3        | VP for External Affairs                 |
| Secretary General              | 4        | Secretary General                       |
| Assistant Secretary            | 5        | Assistant Secretary                     |
| Treasurer                      | 6        | Organization Treasurer                  |
| Auditor                        | 7        | Organization Auditor                    |
| PRO - Math                     | 8        | Public Relations Officer for Math       |
| PRO - English                  | 9        | Public Relations Officer for English    |
| Business Manager - Math        | 10       | Business Manager for Math               |
| Business Manager - English     | 11       | Business Manager for English            |
| MS Representative              | 12       | MS Representative                       |
| Student                        | 99       | Regular Student Member                  |
| Class President                | 20       | Class President                         |

## 📋 **Permission Categories**

### 👥 **User Management**
- `user.view` - View user profiles
- `user.create` - Create new users
- `user.edit` - Edit user information
- `user.delete` - Delete users

### 🎓 **Student Management**
- `student.view` - View student information
- `student.create` - Create new student records
- `student.edit` - Edit student information
- `student.delete` - Delete student records
- `student.manage_classes` - Manage student class assignments
- `student.import_csv` - Import student data from CSV

### 📅 **Event Management**
- `event.view` - View events
- `event.create` - Create new events
- `event.edit` - Edit event details
- `event.delete` - Delete events
- `event.manage_registrations` - Manage event registrations
- `event.manage_participants` - Manage event participants
- `event.view_evaluations` - View event evaluations

### 💰 **Financial Management**
- `financial.view` - View financial records
- `financial.create` - Create financial transactions
- `financial.edit` - Edit financial records
- `financial.delete` - Delete financial records
- `financial.verify` - Verify financial transactions
- `financial.export` - Export financial reports
- `financial.view_receipts` - View receipt photos

### 📦 **Inventory Management**
- `inventory.view` - View inventory items
- `inventory.create` - Add new inventory items
- `inventory.edit` - Edit inventory information
- `inventory.delete` - Delete inventory items
- `inventory.manage_borrowings` - Manage item borrowing
- `inventory.record_conditions` - Record item conditions
- `inventory.export` - Export inventory reports

### 🤝 **Sponsor Management**
- `sponsor.view` - View sponsor information
- `sponsor.create` - Add new sponsors
- `sponsor.edit` - Edit sponsor details
- `sponsor.delete` - Delete sponsors
- `sponsor.assign_events` - Assign sponsors to events

### 📋 **Task Management**
- `task.view` - View tasks
- `task.create` - Create new tasks
- `task.edit` - Edit task details
- `task.delete` - Delete tasks
- `task.assign` - Assign tasks to users
- `task.complete` - Mark tasks as complete

### 📊 **Reporting**
- `report.view` - View reports
- `report.generate` - Generate reports
- `report.export` - Export reports
- `report.dashboard` - Access dashboard analytics

### 🏫 **Academic Management**
- `academic_year.view` - View academic years
- `academic_year.create` - Create academic years
- `academic_year.edit` - Edit academic years
- `academic_year.delete` - Delete academic years
- `class.view` - View classes
- `class.create` - Create classes
- `class.edit` - Edit class information
- `class.delete` - Delete classes
- `class.edit_own` - Edit own class record (Class President only)
- `class.manage_students` - Update student status (graduate, dropped, etc.) in own class
- `class.manage_subjects` - Manage subjects for own class
- `class.manage_schedules` - Manage schedules for own class
- `class.manage_professors` - Manage professors for own class

### 🔧 **System Administration**
- `system.view_logs` - View system logs
- `system.manage_permissions` - Manage user permissions
- `system.manage_roles` - Manage roles

## 🎯 **Role-Specific Permission Sets**

### 👑 **President (Priority 1)**
- **All permissions** - Complete system access
- Can override any permission for any user
- Can manage all aspects of the organization

### 🏢 **Vice President for Internal Affairs (Priority 2)**
- User and student management
- Event coordination and management
- Financial oversight
- Inventory management
- Academic year and class management
- Task assignment and completion
- Reporting and dashboard access

### 🌐 **Vice President for External Affairs (Priority 3)**
- Event creation and management
- Sponsor management
- Task coordination
- Financial transaction creation
- Reporting capabilities

### 📝 **Secretary General (Priority 4)**
- Complete user and student management
- Event registration management
- Task coordination
- **Full academic year and class management (view, create, edit, delete)**
- CSV import capabilities
- Reporting access

### 📋 **Assistant Secretary (Priority 5)**
- User and student viewing/editing
- Event registration management
- Task completion
- **Full academic year and class management (view, create, edit, delete)**
- Basic reporting

### 💰 **Treasurer (Priority 6)**
- Complete financial management
- Financial verification and export
- Receipt viewing
- Event evaluation access
- Sponsor management
- Comprehensive reporting

### 🔍 **Auditor (Priority 7)**
- Financial verification and audit
- Inventory condition monitoring
- Event evaluation review
- System log access
- Export capabilities for audit trails

### 📢 **PRO - Math (Priority 8)**
- Event creation and management
- Sponsor coordination
- Task management
- Reporting capabilities

### 📢 **PRO - English (Priority 9)**
- Event creation and management
- Sponsor coordination
- Task management
- Reporting capabilities

### 💼 **Business Manager - Math (Priority 10)**
- Inventory management
- Financial transaction creation
- Sponsor management
- Reporting capabilities

### 💼 **Business Manager - English (Priority 11)**
- Inventory management
- Financial transaction creation
- Sponsor management
- Reporting capabilities

### 🎯 **MS Representative (Priority 12)**
- Event coordination and management
- Participant management
- Task assignment and completion
- Evaluation access
- Reporting capabilities

### 👨‍🎓 **Student (Priority 99)**
- View events
- Complete assigned tasks
- View inventory items
- Basic system access

### 🏅 **Class President (Priority 20)**
- Can edit their own class record
- Can update student status (graduate, dropped, etc.) for their class
- Can manage subjects, schedules, and professors for their class
- Cannot manage other classes or system-wide settings

## 🔄 **Permission Resolution Logic**

### 1. **Role Hierarchy**
- Lower priority numbers = higher authority
- Higher roles can override lower role permissions
- President (1) can access everything

### 2. **Permission Inheritance**
- Users inherit permissions from their assigned roles
- Multiple roles are supported with priority-based resolution
- Custom user role permissions can override role defaults

### 3. **Custom Overrides**
- `UserRolePermission` table allows custom permission adjustments
- Can grant or revoke specific permissions for individual users
- Supports reason tracking for audit purposes

### 4. **Time-Based Access**
- `UserRole` table tracks role assignments with time periods
- Permissions are only active during valid role periods
- Supports academic year-based role management

## 🛡️ **Security Features**

### **Audit Trail**
- All permission changes are logged
- User actions are tracked in `TRANSACTION_LOG`
- Complete audit trail for compliance

### **Permission Validation**
- Server-side permission checks on all API endpoints
- Client-side permission filtering for UI elements
- Middleware-based access control

### **Role-Based UI**
- Dynamic menu generation based on permissions
- Feature visibility controlled by user permissions
- Responsive interface adaptation

## 🚀 **Implementation Details**

### **Database Schema**
```sql
-- Core permission tables
PERMISSION (permission_id, permission_name, description)
ROLE (role_id, role_name, description, role_priority)
ROLE_PERMISSION (role_id, permission_id)

-- User role management
USER_ROLE (user_role_id, user_id, role_id, academic_year_id, start_date, end_date)
USER_ROLE_PERMISSION (user_role_id, permission_id, is_granted, reason)
```

### **API Endpoints**
- `GET /api/permissions` - List user permissions
- `POST /api/permissions/check` - Check specific permission
- `GET /api/roles` - List available roles
- `POST /api/user-roles` - Assign roles to users

### **Middleware**
- `CheckPermission` - Validates specific permissions
- `CheckRole` - Validates user roles
- `CheckPermissionOrRole` - Flexible permission checking

This permission system provides a robust, scalable foundation for managing access control in the Mentors Society application while maintaining security and auditability. 