-- =============================================
-- Mentors Society Test Database Schema
-- =============================================

USE mentors_society_test;

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
CREATE TABLE USER (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    middle_initial VARCHAR(5),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- PERSONAL_ACCESS_TOKENS: Laravel Sanctum table for API token authentication
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id)
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

-- STUDENT: Extends USER with student-specific attributes
CREATE TABLE STUDENT (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    student_number VARCHAR(20) UNIQUE,
    FOREIGN KEY (user_id) REFERENCES USER(user_id) ON DELETE CASCADE
);

-- =============================================
-- CLASS: Defines class groupings (e.g., BSED MATH, BSED ENGLISH)
CREATE TABLE CLASS (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL UNIQUE,
    academic_year_id INT,
    class_president_id INT,
    status ENUM('active', 'graduated', 'dropped') DEFAULT 'active',
    remarks VARCHAR(255),
    FOREIGN KEY (academic_year_id) REFERENCES ACADEMIC_YEAR(academic_year_id) ON DELETE RESTRICT,
    FOREIGN KEY (class_president_id) REFERENCES USER(user_id) ON DELETE SET NULL
);

-- CLASS_SUBJECT: Subjects for each class
CREATE TABLE CLASS_SUBJECT (
    class_subject_id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    academic_year_id INT,
    subject_name VARCHAR(100),
    description VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    UNIQUE (class_id, subject_name, academic_year_id),
    FOREIGN KEY (class_id) REFERENCES CLASS(class_id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES ACADEMIC_YEAR(academic_year_id) ON DELETE SET NULL
);

-- CLASS_SCHEDULE: Schedules for each class/subject
CREATE TABLE CLASS_SCHEDULE (
    class_schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    subject_id INT,
    academic_year_id INT,
    day_of_week VARCHAR(20),
    start_time TIME,
    end_time TIME,
    room VARCHAR(100),
    UNIQUE (class_id, subject_id, day_of_week, start_time, academic_year_id),
    FOREIGN KEY (class_id) REFERENCES CLASS(class_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES CLASS_SUBJECT(class_subject_id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES ACADEMIC_YEAR(academic_year_id) ON DELETE SET NULL
);

-- CLASS_PROFESSOR: Professors for each class/subject
CREATE TABLE CLASS_PROFESSOR (
    class_professor_id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    subject_id INT,
    academic_year_id INT,
    professor_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(30),
    UNIQUE (class_id, professor_name, academic_year_id),
    FOREIGN KEY (class_id) REFERENCES CLASS(class_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES CLASS_SUBJECT(class_subject_id) ON DELETE SET NULL,
    FOREIGN KEY (academic_year_id) REFERENCES ACADEMIC_YEAR(academic_year_id) ON DELETE SET NULL
);

-- STUDENT_CLASS: Maps students to classes with year level
CREATE TABLE STUDENT_CLASS (
    student_id INT,
    class_id INT,
    academic_year_id INT,
    year_level VARCHAR(50) NOT NULL DEFAULT 'Other',
    PRIMARY KEY (student_id, class_id, academic_year_id),
    FOREIGN KEY (student_id) REFERENCES STUDENT(student_id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES CLASS(class_id) ON DELETE RESTRICT,
    FOREIGN KEY (academic_year_id) REFERENCES ACADEMIC_YEAR(academic_year_id) ON DELETE RESTRICT
);

--
-- Add to PERMISSION:
--   class.edit_own, class.manage_students, class.manage_subjects, class.manage_schedules, class.manage_professors
-- Add to ROLE:
--   Class President (priority 20) 