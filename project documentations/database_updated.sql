-- =============================================
-- Mentors Society Database Schema
-- =============================================

-- Creating database
CREATE DATABASE mentors_society;
USE mentors_society;

-- =============================================
-- Core Tables
-- =============================================

-- ACADEMIC_YEAR: Defines academic year boundaries
CREATE TABLE ACADEMIC_YEAR (
    academic_year_id INT AUTO_INCREMENT PRIMARY KEY,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    description VARCHAR(100),
    UNIQUE (start_date, end_date)
);

-- =============================================
-- User and Role Management
-- =============================================

-- PERMISSION: Individual system permissions
CREATE TABLE PERMISSION (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- ROLE: Defines roles with base permissions
CREATE TABLE ROLE (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    role_priority INT -- Lower number = higher priority (President = 1, Regular Student = 99)
);

-- ROLE_PERMISSION: Maps permissions to roles
CREATE TABLE ROLE_PERMISSION (
    role_id INT,
    permission_id INT,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES ROLE(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES PERMISSION(permission_id) ON DELETE CASCADE
);

-- USER: Base user table for all system users
-- This implements a role specialization pattern where USER contains common attributes
-- and specialized tables (like STUDENT) extend it with role-specific attributes
CREATE TABLE USER (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    middle_initial VARCHAR(5),
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- USER_ROLE: Tracks role assignments with time periods
CREATE TABLE USER_ROLE (
    user_role_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    role_id INT,
    academic_year_id INT,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES USER(user_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES ROLE(role_id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES ACADEMIC_YEAR(academic_year_id) ON DELETE RESTRICT
);

-- USER_ROLE_PERMISSION: Custom permission adjustments for user roles
CREATE TABLE USER_ROLE_PERMISSION (
    user_role_id INT,
    permission_id INT,
    is_granted BOOLEAN NOT NULL,
    reason TEXT,
    PRIMARY KEY (user_role_id, permission_id),
    FOREIGN KEY (user_role_id) REFERENCES USER_ROLE(user_role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES PERMISSION(permission_id) ON DELETE CASCADE
);

-- =============================================
-- Student Management
-- =============================================

-- CLASS: Defines class groupings (e.g., BSED MATH, BSED ENGLISH)
CREATE TABLE CLASS (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL UNIQUE,
    academic_year_id INT,
    FOREIGN KEY (academic_year_id) REFERENCES ACADEMIC_YEAR(academic_year_id) ON DELETE RESTRICT
);

-- STUDENT: Extends USER with student-specific attributes
-- This table implements the role specialization pattern
CREATE TABLE STUDENT (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    student_number VARCHAR(20) UNIQUE,
    FOREIGN KEY (user_id) REFERENCES USER(user_id) ON DELETE CASCADE
);

-- STUDENT_CLASS: Maps students to classes with year level
CREATE TABLE STUDENT_CLASS (
    student_id INT,
    class_id INT,
    academic_year_id INT,
    year_level VARCHAR(50) NOT NULL DEFAULT 'Other' CHECK (year_level IN ('First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Other')),
    PRIMARY KEY (student_id, class_id, academic_year_id),
    FOREIGN KEY (student_id) REFERENCES STUDENT(student_id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES CLASS(class_id) ON DELETE RESTRICT,
    FOREIGN KEY (academic_year_id) REFERENCES ACADEMIC_YEAR(academic_year_id) ON DELETE RESTRICT
);

-- =============================================
-- Event Management
-- =============================================

-- EVENT_STATUS: Tracks event lifecycle
CREATE TABLE EVENT_STATUS (
    status_id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) NOT NULL UNIQUE
);

-- EVENT: Stores event details
CREATE TABLE EVENT (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    venue VARCHAR(100) NOT NULL,
    status_id INT,
    created_by INT,
    capacity INT,
    FOREIGN KEY (status_id) REFERENCES EVENT_STATUS(status_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES USER(user_id) ON DELETE SET NULL
);

-- EVENT_REGISTRATION: Student registrations for events
CREATE TABLE EVENT_REGISTRATION (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    student_id INT,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES EVENT(event_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES STUDENT(student_id) ON DELETE CASCADE
);

-- EVENT_PARTICIPATION: Tracks attendance and general feedback
CREATE TABLE EVENT_PARTICIPATION (
    participation_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    student_id INT,
    attended BOOLEAN NOT NULL,
    feedback TEXT,
    feedback_date TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES EVENT(event_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES STUDENT(student_id) ON DELETE CASCADE
);

-- EVENT_EVALUATION: Stores structured evaluation responses
CREATE TABLE EVENT_EVALUATION (
    evaluation_id INT AUTO_INCREMENT PRIMARY KEY,
    participation_id INT,
    category VARCHAR(50) NOT NULL,
    question_text TEXT NOT NULL,
    response VARCHAR(50),
    numerical_rating INT CHECK (numerical_rating BETWEEN 1 AND 10),
    FOREIGN KEY (participation_id) REFERENCES EVENT_PARTICIPATION(participation_id) ON DELETE CASCADE
);

-- =============================================
-- Sponsor Management
-- =============================================

-- SPONSOR: Stores sponsor details
CREATE TABLE SPONSOR (
    sponsor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    instagram_url VARCHAR(255)
);

-- EVENT_SPONSOR: Maps sponsors to events
CREATE TABLE EVENT_SPONSOR (
    event_id INT,
    sponsor_id INT,
    PRIMARY KEY (event_id, sponsor_id),
    FOREIGN KEY (event_id) REFERENCES EVENT(event_id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES SPONSOR(sponsor_id) ON DELETE CASCADE
);

-- =============================================
-- Communication and Task Management
-- =============================================

-- SOCIAL_MEDIA: Tracks organization's social media accounts
CREATE TABLE SOCIAL_MEDIA (
    social_media_id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL UNIQUE
);

-- TASK: Tracks tasks (e.g., captions, pubmats, postings)
CREATE TABLE TASK (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    task_name VARCHAR(100) NOT NULL,
    officer_id INT,
    deadline DATE,
    date_posted DATE,
    time_posted TIME,
    status VARCHAR(50),
    link VARCHAR(255),
    category VARCHAR(50),
    FOREIGN KEY (event_id) REFERENCES EVENT(event_id) ON DELETE SET NULL,
    FOREIGN KEY (officer_id) REFERENCES USER(user_id) ON DELETE SET NULL
);

-- =============================================
-- Financial Management
-- =============================================

-- TRANSACTION_TYPE: Categorizes transactions
CREATE TABLE TRANSACTION_TYPE (
    type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL UNIQUE
);

-- TRANSACTION: Records cash-based transactions
CREATE TABLE TRANSACTION (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    type_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    receipt_photo VARCHAR(255),
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recorded_by INT,
    verified_by INT,
    FOREIGN KEY (event_id) REFERENCES EVENT(event_id) ON DELETE SET NULL,
    FOREIGN KEY (type_id) REFERENCES TRANSACTION_TYPE(type_id) ON DELETE RESTRICT,
    FOREIGN KEY (recorded_by) REFERENCES USER(user_id) ON DELETE SET NULL,
    FOREIGN KEY (verified_by) REFERENCES USER(user_id) ON DELETE SET NULL
);

-- FINANCIAL_RECORD: Links transactions to events or other entities
CREATE TABLE FINANCIAL_RECORD (
    financial_record_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    transaction_id INT,
    description TEXT,
    FOREIGN KEY (event_id) REFERENCES EVENT(event_id) ON DELETE SET NULL,
    FOREIGN KEY (transaction_id) REFERENCES TRANSACTION(transaction_id) ON DELETE CASCADE
);

-- =============================================
-- Inventory Management
-- =============================================

-- INVENTORY_ITEM: Tracks organization assets
CREATE TABLE INVENTORY_ITEM (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    quantity_total INT NOT NULL,
    quantity_used INT DEFAULT 0,
    quantity_added INT DEFAULT 0,
    remaining_quantity INT GENERATED ALWAYS AS (quantity_total - quantity_used + quantity_added) STORED,
    last_updated DATE NOT NULL
);

-- ITEM_CONDITION: Tracks item condition
CREATE TABLE ITEM_CONDITION (
    condition_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    condition_description TEXT,
    recorded_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recorded_by INT,
    FOREIGN KEY (item_id) REFERENCES INVENTORY_ITEM(item_id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES USER(user_id) ON DELETE SET NULL
);

-- ITEM_BORROWING: Records borrowing history
CREATE TABLE ITEM_BORROWING (
    borrowing_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    student_id INT,
    borrow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    return_date TIMESTAMP,
    condition_id_borrow INT,
    condition_id_return INT,
    FOREIGN KEY (item_id) REFERENCES INVENTORY_ITEM(item_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES STUDENT(student_id) ON DELETE CASCADE,
    FOREIGN KEY (condition_id_borrow) REFERENCES ITEM_CONDITION(condition_id) ON DELETE SET NULL,
    FOREIGN KEY (condition_id_return) REFERENCES ITEM_CONDITION(condition_id) ON DELETE SET NULL
);

-- =============================================
-- Auditing
-- =============================================

-- TRANSACTION_LOG: Comprehensive audit trail
CREATE TABLE TRANSACTION_LOG (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT NOT NULL,
    before_state TEXT,
    after_state TEXT,
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES USER(user_id) ON DELETE SET NULL
);

-- =============================================
-- Indexes for Performance Optimization
-- =============================================

-- USER table
CREATE UNIQUE INDEX idx_user_email ON USER(email);

-- STUDENT table
CREATE UNIQUE INDEX idx_student_number ON STUDENT(student_number);
CREATE INDEX idx_student_userid ON STUDENT(user_id);

-- USER_ROLE table
CREATE INDEX idx_userrole_userid ON USER_ROLE(user_id);
CREATE INDEX idx_userrole_roleid ON USER_ROLE(role_id);
CREATE INDEX idx_userrole_academicyearid ON USER_ROLE(academic_year_id);

-- USER_ROLE_PERMISSION table
CREATE INDEX idx_userrolepermission_userroleid ON USER_ROLE_PERMISSION(user_role_id);
CREATE INDEX idx_userrolepermission_permissionid ON USER_ROLE_PERMISSION(permission_id);

-- ROLE_PERMISSION table
CREATE INDEX idx_rolepermission_roleid ON ROLE_PERMISSION(role_id);
CREATE INDEX idx_rolepermission_permissionid ON ROLE_PERMISSION(permission_id);

-- STUDENT_CLASS table
CREATE INDEX idx_studentclass_studentid ON STUDENT_CLASS(student_id);
CREATE INDEX idx_studentclass_classid ON STUDENT_CLASS(class_id);
CREATE INDEX idx_studentclass_academicyearid ON STUDENT_CLASS(academic_year_id);

-- CLASS table
CREATE INDEX idx_class_academicyearid ON CLASS(academic_year_id);

-- EVENT table
CREATE INDEX idx_event_statusid ON EVENT(status_id);
CREATE INDEX idx_event_createdby ON EVENT(created_by);

-- EVENT_REGISTRATION table
CREATE INDEX idx_eventreg_eventid ON EVENT_REGISTRATION(event_id);
CREATE INDEX idx_eventreg_studentid ON EVENT_REGISTRATION(student_id);

-- EVENT_PARTICIPATION table
CREATE INDEX idx_eventpart_eventid ON EVENT_PARTICIPATION(event_id);
CREATE INDEX idx_eventpart_studentid ON EVENT_PARTICIPATION(student_id);

-- EVENT_EVALUATION table
CREATE INDEX idx_eventeval_participationid ON EVENT_EVALUATION(participation_id);

-- TRANSACTION table
CREATE INDEX idx_transaction_eventid ON TRANSACTION(event_id);
CREATE INDEX idx_transaction_typeid ON TRANSACTION(type_id);
CREATE INDEX idx_transaction_recordedby ON TRANSACTION(recorded_by);
CREATE INDEX idx_transaction_verifiedby ON TRANSACTION(verified_by);

-- FINANCIAL_RECORD table
CREATE INDEX idx_financialrecord_eventid ON FINANCIAL_RECORD(event_id);
CREATE INDEX idx_financialrecord_transactionid ON FINANCIAL_RECORD(transaction_id);

-- ITEM_CONDITION table
CREATE INDEX idx_itemcondition_itemid ON ITEM_CONDITION(item_id);
CREATE INDEX idx_itemcondition_recordedby ON ITEM_CONDITION(recorded_by);

-- ITEM_BORROWING table
CREATE INDEX idx_itemborrowing_itemid ON ITEM_BORROWING(item_id);
CREATE INDEX idx_itemborrowing_studentid ON ITEM_BORROWING(student_id);
CREATE INDEX idx_itemborrowing_conditionid_borrow ON ITEM_BORROWING(condition_id_borrow);
CREATE INDEX idx_itemborrowing_conditionid_return ON ITEM_BORROWING(condition_id_return);

-- TRANSACTION_LOG table
CREATE INDEX idx_transactionlog_userid ON TRANSACTION_LOG(user_id);

-- =====================
-- CONSTRAINTS (if not already present)
-- =====================
ALTER TABLE STUDENT ADD CONSTRAINT uq_student_number UNIQUE (student_number);
ALTER TABLE USER ADD CONSTRAINT uq_user_email UNIQUE (email);
ALTER TABLE CLASS ADD CONSTRAINT uq_class_classname UNIQUE (class_name);
ALTER TABLE INVENTORY_ITEM ADD CONSTRAINT uq_inventoryitem_itemname UNIQUE (item_name); 