-- =============================================
-- Mentors Society Database Schema (Updated to match Laravel migrations)
-- =============================================

-- Creating database
CREATE DATABASE mentors_society;
USE mentors_society;

-- =============================================
-- Core Tables
-- =============================================

-- ACADEMIC_YEAR: Defines academic year boundaries
CREATE TABLE academic_year (
    academic_year_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    description VARCHAR(32) NOT NULL,
    UNIQUE KEY unique_academic_year (start_date, end_date)
);

-- =============================================
-- User and Role Management
-- =============================================

-- PERMISSION: Individual system permissions
CREATE TABLE permission (
    permission_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL
);

-- ROLE: Defines roles with base permissions
CREATE TABLE role (
    role_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    role_priority INT DEFAULT 0
);

-- ROLE_PERMISSION: Maps permissions to roles
CREATE TABLE role_permission (
    role_permission_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY unique_role_permission (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES role(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permission(permission_id) ON DELETE CASCADE
);

-- STUDENT: Core student information
CREATE TABLE student (
    student_number VARCHAR(20) PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_initial VARCHAR(5) NULL,
    course VARCHAR(100) NULL,
    year_level VARCHAR(50) NULL,
    section VARCHAR(10) NULL,
    academic_status ENUM('active', 'dropped', 'shifted', 'graduated') DEFAULT 'active',
    email VARCHAR(100) UNIQUE
);

-- USER: Authentication table (extends student)
CREATE TABLE user (
    student_number VARCHAR(20) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (student_number) REFERENCES student(student_number)
);

-- USER_ROLE: Tracks role assignments with time periods
CREATE TABLE user_role (
    user_role_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_number VARCHAR(20) NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    UNIQUE KEY unique_user_role (student_number, role_id, academic_year_id),
    FOREIGN KEY (student_number) REFERENCES user(student_number) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES role(role_id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_year(academic_year_id) ON DELETE CASCADE
);

-- USER_ROLE_PERMISSION: Custom permission adjustments for user roles
CREATE TABLE user_role_permission (
    user_role_permission_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY unique_user_role_permission (user_role_id, permission_id),
    FOREIGN KEY (user_role_id) REFERENCES user_role(user_role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permission(permission_id) ON DELETE CASCADE
);

-- =============================================
-- Class Management System
-- =============================================

-- CLASS: Defines class groupings
CREATE TABLE class (
    class_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    academic_year_id BIGINT UNSIGNED NULL,
    class_president_id VARCHAR(20) NULL,
    remarks VARCHAR(255) NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_year(academic_year_id),
    FOREIGN KEY (class_president_id) REFERENCES user(student_number)
);

-- CLASS_SUBJECT: Subjects taught in classes
CREATE TABLE class_subject (
    class_subject_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    class_id BIGINT UNSIGNED NOT NULL,
    academic_year_id BIGINT UNSIGNED NULL,
    subject_name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    UNIQUE KEY unique_class_subject (class_id, subject_name, academic_year_id),
    FOREIGN KEY (class_id) REFERENCES class(class_id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_year(academic_year_id) ON DELETE SET NULL
);

-- CLASS_SCHEDULE: Class schedules
CREATE TABLE class_schedule (
    class_schedule_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    class_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    academic_year_id BIGINT UNSIGNED NULL,
    day_of_week VARCHAR(20) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(100) NULL,
    UNIQUE KEY class_schedule_unique (class_id, subject_id, day_of_week, start_time, academic_year_id),
    FOREIGN KEY (class_id) REFERENCES class(class_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES class_subject(class_subject_id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_year(academic_year_id) ON DELETE SET NULL
);

-- CLASS_PROFESSOR: Professor assignments
CREATE TABLE class_professor (
    class_professor_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    class_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NULL,
    academic_year_id BIGINT UNSIGNED NULL,
    professor_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL,
    phone VARCHAR(30) NULL,
    UNIQUE KEY unique_class_professor (class_id, professor_name, academic_year_id),
    FOREIGN KEY (class_id) REFERENCES class(class_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES class_subject(class_subject_id) ON DELETE SET NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_year(academic_year_id) ON DELETE SET NULL
);

-- STUDENT_CLASS: Student enrollment in classes
CREATE TABLE student_class (
    student_number VARCHAR(20) NOT NULL,
    class_id BIGINT UNSIGNED NOT NULL,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    year_level VARCHAR(50) DEFAULT 'Other',
    PRIMARY KEY (student_number, class_id, academic_year_id),
    FOREIGN KEY (student_number) REFERENCES student(student_number),
    FOREIGN KEY (class_id) REFERENCES class(class_id) ON DELETE RESTRICT,
    FOREIGN KEY (academic_year_id) REFERENCES academic_year(academic_year_id) ON DELETE RESTRICT
);

-- =============================================
-- Event Management System
-- =============================================

-- EVENT_STATUS: Tracks event lifecycle
CREATE TABLE event_status (
    status_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) NOT NULL UNIQUE
);

-- EVENT: Stores event details
CREATE TABLE event (
    event_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    venue VARCHAR(100) NOT NULL,
    status_id BIGINT UNSIGNED NULL,
    created_by VARCHAR(20) NULL,
    capacity INT NULL,
    FOREIGN KEY (status_id) REFERENCES event_status(status_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES user(student_number) ON DELETE SET NULL
);

-- EVENT_REGISTRATION: Student registrations for events
CREATE TABLE event_registration (
    registration_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    student_number VARCHAR(20) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    FOREIGN KEY (student_number) REFERENCES student(student_number) ON DELETE CASCADE
);

-- EVENT_PARTICIPATION: Tracks attendance and general feedback
CREATE TABLE event_participation (
    participation_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    student_number VARCHAR(20) NOT NULL,
    attended BOOLEAN NOT NULL,
    feedback TEXT NULL,
    feedback_date TIMESTAMP NULL,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    FOREIGN KEY (student_number) REFERENCES student(student_number) ON DELETE CASCADE
);

-- EVENT_EVALUATION: Stores structured evaluation responses
CREATE TABLE event_evaluation (
    evaluation_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    participation_id BIGINT UNSIGNED NOT NULL,
    category VARCHAR(50) NOT NULL,
    question_text TEXT NOT NULL,
    response VARCHAR(50) NULL,
    numerical_rating INT NULL,
    FOREIGN KEY (participation_id) REFERENCES event_participation(participation_id) ON DELETE CASCADE
);

-- =============================================
-- Sponsor Management
-- =============================================

-- SPONSOR: Stores sponsor details
CREATE TABLE sponsor (
    sponsor_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    instagram_url VARCHAR(255) NULL
);

-- EVENT_SPONSOR: Maps sponsors to events
CREATE TABLE event_sponsor (
    event_id BIGINT UNSIGNED NOT NULL,
    sponsor_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (event_id, sponsor_id),
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsor(sponsor_id) ON DELETE CASCADE
);

-- =============================================
-- Communication and Task Management
-- =============================================

-- SOCIAL_MEDIA: Tracks organization's social media accounts
CREATE TABLE social_media (
    social_media_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL UNIQUE
);

-- TASK: Tracks tasks (e.g., captions, pubmats, postings)
CREATE TABLE task (
    task_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NULL,
    task_name VARCHAR(100) NOT NULL,
    officer_id VARCHAR(20) NULL,
    deadline DATE NULL,
    date_posted DATE NULL,
    time_posted TIME NULL,
    status VARCHAR(50) NULL,
    link VARCHAR(255) NULL,
    category VARCHAR(50) NULL,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE SET NULL,
    FOREIGN KEY (officer_id) REFERENCES user(student_number) ON DELETE SET NULL
);

-- =============================================
-- Financial Management
-- =============================================

-- TRANSACTION_TYPE: Transaction categories
CREATE TABLE transaction_type (
    type_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL UNIQUE,
    direction ENUM('income', 'outcome') NOT NULL
);

-- TRANSACTION: Records cash-based transactions
CREATE TABLE transaction (
    transaction_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NULL,
    type_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT NULL,
    receipt_photo VARCHAR(255) NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    student_number VARCHAR(20) NOT NULL,
    verified_by VARCHAR(20) NULL,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE SET NULL,
    FOREIGN KEY (type_id) REFERENCES transaction_type(type_id) ON DELETE RESTRICT,
    FOREIGN KEY (student_number) REFERENCES student(student_number),
    FOREIGN KEY (verified_by) REFERENCES user(student_number) ON DELETE SET NULL
);

-- FINANCIAL_RECORD: Links transactions to events or other entities
CREATE TABLE financial_record (
    financial_record_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NULL,
    transaction_id BIGINT UNSIGNED NOT NULL,
    description TEXT NULL,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE SET NULL,
    FOREIGN KEY (transaction_id) REFERENCES transaction(transaction_id) ON DELETE CASCADE
);

-- EVENT_BUDGET: Event-specific budgets
CREATE TABLE event_budget (
    event_budget_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_event_budget (event_id),
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE
);

-- ORGANIZATION_BUDGET: Organization-wide budgets per academic year
CREATE TABLE organization_budget (
    org_budget_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_org_budget (academic_year_id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_year(academic_year_id) ON DELETE CASCADE
);

-- =============================================
-- Inventory Management
-- =============================================

-- INVENTORY_ITEM: Tracks organization assets
CREATE TABLE inventory_item (
    item_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL UNIQUE,
    quantity_total INT NOT NULL,
    quantity_used INT DEFAULT 0,
    quantity_added INT DEFAULT 0,
    remaining_quantity INT GENERATED ALWAYS AS (quantity_total - quantity_used + quantity_added) STORED,
    last_updated DATE NOT NULL
);

-- ITEM_CONDITION: Tracks item condition
CREATE TABLE item_condition (
    condition_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id BIGINT UNSIGNED NOT NULL,
    condition_description TEXT NULL,
    recorded_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recorded_by VARCHAR(20) NULL,
    FOREIGN KEY (item_id) REFERENCES inventory_item(item_id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES user(student_number) ON DELETE SET NULL
);

-- ITEM_BORROWING: Records borrowing history
CREATE TABLE item_borrowing (
    borrowing_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id BIGINT UNSIGNED NOT NULL,
    student_number VARCHAR(20) NOT NULL,
    borrow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    return_date TIMESTAMP NULL,
    condition_id_borrow BIGINT UNSIGNED NULL,
    condition_id_return BIGINT UNSIGNED NULL,
    FOREIGN KEY (item_id) REFERENCES inventory_item(item_id) ON DELETE CASCADE,
    FOREIGN KEY (student_number) REFERENCES student(student_number) ON DELETE CASCADE,
    FOREIGN KEY (condition_id_borrow) REFERENCES item_condition(condition_id) ON DELETE SET NULL,
    FOREIGN KEY (condition_id_return) REFERENCES item_condition(condition_id) ON DELETE SET NULL
);

-- =============================================
-- Auditing
-- =============================================

-- TRANSACTION_LOG: Comprehensive audit trail
CREATE TABLE transaction_log (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_number VARCHAR(20) NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT NOT NULL,
    before_state TEXT NULL,
    after_state TEXT NULL,
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_number) REFERENCES user(student_number) ON DELETE SET NULL
);

-- =============================================
-- Laravel Sanctum Personal Access Tokens
-- =============================================

CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id)
);

-- =============================================
-- Indexes for Performance Optimization
-- =============================================

-- User and authentication indexes
CREATE INDEX idx_user_status ON user(status);
CREATE INDEX idx_student_email ON student(email);
CREATE INDEX idx_student_academic_status ON student(academic_status);

-- Role and permission indexes
CREATE INDEX idx_user_role_student ON user_role(student_number);
CREATE INDEX idx_user_role_academic_year ON user_role(academic_year_id);
CREATE INDEX idx_role_permission_role ON role_permission(role_id);
CREATE INDEX idx_user_role_permission_user_role ON user_role_permission(user_role_id);

-- Class management indexes
CREATE INDEX idx_class_academic_year ON class(academic_year_id);
CREATE INDEX idx_class_president ON class(class_president_id);
CREATE INDEX idx_class_subject_class ON class_subject(class_id);
CREATE INDEX idx_class_schedule_class ON class_schedule(class_id);
CREATE INDEX idx_class_schedule_subject ON class_schedule(subject_id);
CREATE INDEX idx_class_professor_class ON class_professor(class_id);
CREATE INDEX idx_student_class_student ON student_class(student_number);
CREATE INDEX idx_student_class_class ON student_class(class_id);

-- Event management indexes
CREATE INDEX idx_event_status ON event(status_id);
CREATE INDEX idx_event_created_by ON event(created_by);
CREATE INDEX idx_event_date ON event(event_date);
CREATE INDEX idx_event_registration_event ON event_registration(event_id);
CREATE INDEX idx_event_registration_student ON event_registration(student_number);
CREATE INDEX idx_event_participation_event ON event_participation(event_id);
CREATE INDEX idx_event_participation_student ON event_participation(student_number);
CREATE INDEX idx_event_evaluation_participation ON event_evaluation(participation_id);

-- Financial indexes
CREATE INDEX idx_transaction_event ON transaction(event_id);
CREATE INDEX idx_transaction_type ON transaction(type_id);
CREATE INDEX idx_transaction_student ON transaction(student_number);
CREATE INDEX idx_transaction_verified_by ON transaction(verified_by);
CREATE INDEX idx_transaction_date ON transaction(transaction_date);
CREATE INDEX idx_financial_record_event ON financial_record(event_id);
CREATE INDEX idx_financial_record_transaction ON financial_record(transaction_id);

-- Inventory indexes
CREATE INDEX idx_item_condition_item ON item_condition(item_id);
CREATE INDEX idx_item_condition_recorded_by ON item_condition(recorded_by);
CREATE INDEX idx_item_borrowing_item ON item_borrowing(item_id);
CREATE INDEX idx_item_borrowing_student ON item_borrowing(student_number);
CREATE INDEX idx_item_borrowing_borrow_date ON item_borrowing(borrow_date);

-- Audit indexes
CREATE INDEX idx_transaction_log_student ON transaction_log(student_number);
CREATE INDEX idx_transaction_log_entity ON transaction_log(entity_type, entity_id);
CREATE INDEX idx_transaction_log_action_date ON transaction_log(action_date);

-- =============================================
-- Sample Data (Optional)
-- =============================================

-- Insert sample transaction types
INSERT INTO transaction_type (type_name, direction) VALUES
    ('FRA Payment', 'income'),
    ('Event Fee', 'income'),
    ('Purchase', 'outcome'),
    ('Refund', 'outcome'),
    ('Penalty', 'income');

-- Insert sample event statuses
INSERT INTO event_status (status_name) VALUES
    ('Planning'),
    ('Registration Open'),
    ('Registration Closed'),
    ('In Progress'),
    ('Completed'),
    ('Cancelled');

-- =============================================
-- Notes
-- =============================================
-- This schema matches the Laravel migrations structure
-- All tables use student_number as the primary identifier for students
-- Foreign key relationships are properly defined with appropriate cascade/restrict behaviors
-- Indexes are created for optimal query performance
-- The schema supports a complete student organization management system
