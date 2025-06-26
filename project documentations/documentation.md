# Information Management System Documentation

## System Overview

This document outlines the normalized database structure and streamlined processes for the Information Management System. The system manages a single student organization's records, events, financial transactions (cash-based), and inventory.

## Database Model

The database follows a fully normalized structure with proper relationships between entities. Relationship notations used:

- `||--||`: Mandatory One-to-One
- `||--o|`: Mandatory One-to-Optional One
- `||--|{`: Mandatory One-to-Many
- `|o--|{`: Optional One-to-Many

Note: The database design avoids many-to-many relationships to maintain simplicity and performance.

## Core Entities

### User and Role Management

#### Entities
- **USER**: Stores core user information
- **ROLE**: Defines available roles and base permissions
- **ROLE_PERMISSION**: Maps granular permissions to roles
- **USER_ROLE**: Tracks role assignments with time periods
- **ACADEMIC_YEAR**: Defines academic year boundaries
- **PERMISSION**: Individual system permissions that can be granted

#### Role Permission Management
Roles have base permissions defined at the role level, but individual permissions can be added or removed for specific user-role assignments:

1. **Base Role Permissions**:
   - Each role (e.g., Treasurer) comes with a predefined set of permissions
   - These serve as the starting template when assigning a role

2. **Custom Permission Adjustments**:
   - Administrators can increase or decrease permissions for individual users
   - Custom permissions are stored in USER_ROLE_PERMISSION table
   - System checks both role-level and user-specific permissions

3. **Permission Inheritance**:
   - Users with multiple roles inherit all permissions from each role
   - Custom permissions can override role defaults

### Event Management

#### Entities
- **EVENT**: Core event information
- **EVENT_STATUS**: Tracks event lifecycle (planned, active, completed, cancelled)
- **EVENT_REGISTRATION**: Student registrations for events
- **EVENT_PARTICIPATION**: Actual attendance and feedback

### Financial Management

#### Entities
- **TRANSACTION**: Records all financial transactions (cash-based)
- **TRANSACTION_TYPE**: Categorizes transactions (income, expense, etc.)
- **FINANCIAL_RECORD**: Links transactions to events or other entities

#### Cash-Based Financial Process
1. **Payment Collection**:
   - Treasurer physically collects cash payments
   - Records transaction in system via QR code scanning or student lookup
   - System generates receipt reference number
   - No online payments are processed within the system

2. **Transaction Verification**:
   - Treasurer uploads receipt photo as proof of transaction
   - Auditor can verify transactions against physical receipts
   - System maintains complete transaction history

### Inventory Management

#### Entities
- **INVENTORY_ITEM**: Tracks organization assets
- **ITEM_BORROWING**: Records borrowing history
- **ITEM_CONDITION**: Documents item condition at checkout/return

### Student Records

#### Entities
- **STUDENT**: Student-specific attributes
- **CLASS**: Class groupings
- **STUDENT_CLASS**: Maps students to classes

### Audit System

#### Entities
- **TRANSACTION_LOG**: Comprehensive audit trail of all system actions

## Core Processes

### Role Transition Process

1. **Initiation**:
   - Current officer initiates role transfer
   - System captures timestamp automatically

2. **Execution**:
   - Current officer role marked inactive (end_date = current timestamp)
   - New officer role created (start_date = current timestamp)
   - Permission adjustments transferred or reset based on configuration

3. **Documentation**:
   - Transaction logged with before/after state
   - Historical record maintained for reporting

### Permission Adjustment Process

1. **Access**:
   - Authorized users access role management interface
   - Select specific user-role combination

2. **Modification**:
   - View current permissions (base + custom)
   - Add or remove individual permissions
   - Document reason for adjustment

3. **Application**:
   - Changes take effect immediately
   - System logs all permission changes

### Event Management Process

1. **Creation & Approval**:
   - Officer creates event (PLANNED status)
   - Approval changes status to ACTIVE

2. **Registration**:
   - Students register for active events
   - System enforces capacity limits

3. **Execution**:
   - Attendance recorded during event
   - Status updated to COMPLETED after event

### Financial Management Process

1. **Cash Collection**:
   - Treasurer physically receives cash payment
   - Logs into system and accesses financial module
   - Finds student via QR code scan or search by ID/name
   - Records payment details and uploads receipt photo

2. **Transaction Recording**:
   - System generates transaction record
   - Links transaction to event if applicable
   - Updates student balance if needed

3. **Verification**:
   - Auditor reviews transaction records
   - Compares system records with physical receipts
   - Approves or flags transactions for review

### Inventory Management Process

1. **Item Tracking**:
   - Items added to inventory with details
   - Availability tracked in real-time

2. **Borrowing**:
   - Students request items
   - Condition documented at checkout
   - Return process includes condition comparison

## System Architecture

### Technology Stack

1. **Backend Framework**: Laravel (PHP)
   - Robust ORM for database interactions
   - Built-in authentication and permission system
   - Strong community support and documentation
   - RESTful API capabilities

2. **Frontend Framework**: Vue.js
   - Component-based architecture
   - Reactive data binding
   - Lightweight and fast
   - Seamless integration with Laravel (Laravel+Vue is a common stack)

3. **Database**: MySQL
   - Relational database that supports complex queries
   - Transaction support for data integrity
   - Widely used with excellent documentation
   - Good performance with proper indexing

4. **Mobile Support**:
   - Progressive Web App (PWA) approach
   - Responsive design for all device sizes
   - QR code scanning capability via device camera

5. **Deployment**:
   - LAMP stack (Linux, Apache, MySQL, PHP)
   - Containerization optional (Docker)
   - Shared hosting or VPS depending on budget

### Architecture Pattern

1. **MVC Pattern**:
   - Models: Database entities and business logic
   - Views: Vue.js components and templates
   - Controllers: Request handling and response generation

2. **Repository Pattern**:
   - Separation of data access logic
   - Easier unit testing
   - Consistent data access methods

3. **Service Layer**:
   - Business logic encapsulation
   - Reusable services across controllers
   - Cleaner controller code

### Security Considerations

1. **Authentication**:
   - Multi-factor authentication for sensitive roles
   - Role-based access control
   - Session management and timeout

2. **Data Protection**:
   - Input validation and sanitization
   - CSRF protection
   - XSS prevention

3. **Audit Trail**:
   - Comprehensive logging of all actions
   - User activity tracking
   - Change history for sensitive data

## System Benefits

1. **Historical Integrity**:
   - Complete officer history preserved
   - Role transitions fully documented
   - Permission changes tracked

2. **Flexible Permissions**:
   - Base role templates for consistency
   - Custom adjustments for special cases
   - Clear permission inheritance rules

3. **Comprehensive Audit**:
   - All actions logged with user, timestamp, and details
   - Before/after states recorded for changes
   - Support for compliance and accountability

4. **Streamlined Processes**:
   - Automated role transitions
   - Simplified event management
   - Integrated inventory tracking
   - Cash-based financial tracking with digital records
