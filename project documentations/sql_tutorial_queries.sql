-- SQL TUTORIAL: SELECTING STUDENTS AND JOIN OPERATIONS
-- This file contains examples for the Student Organization Management System

-- =====================================================
-- 1. BASIC SELECT QUERIES FOR STUDENTS
-- =====================================================

-- Select all students with their student numbers
SELECT * FROM STUDENT;

-- Select specific columns for students
SELECT student_id, student_number, user_id FROM STUDENT;

-- Select students with user information (using JOIN)
SELECT s.student_id, s.student_number, u.name, u.email
FROM STUDENT s
JOIN USER u ON s.user_id = u.user_id;

-- =====================================================
-- 2. ORDERING RESULTS (ASCENDING AND DESCENDING)
-- =====================================================

-- Order students by student number in ASCENDING order (A to Z, 1 to 9)
SELECT s.student_id, s.student_number, u.name, u.email
FROM STUDENT s
JOIN USER u ON s.user_id = u.user_id
ORDER BY s.student_number ASC;  -- ASC is optional, it's the default

-- Order students by student number in DESCENDING order (Z to A, 9 to 1)
SELECT s.student_id, s.student_number, u.name, u.email
FROM STUDENT s
JOIN USER u ON s.user_id = u.user_id
ORDER BY s.student_number DESC;

-- Order students by name in ASCENDING order (A to Z)
SELECT s.student_id, s.student_number, u.name, u.email
FROM STUDENT s
JOIN USER u ON s.user_id = u.user_id
ORDER BY u.name ASC;

-- Order students by name in DESCENDING order (Z to A)
SELECT s.student_id, s.student_number, u.name, u.email
FROM STUDENT s
JOIN USER u ON s.user_id = u.user_id
ORDER BY u.name DESC;

-- Multiple ordering: First by class, then by name
SELECT s.student_id, s.student_number, u.name, c.class_name, sc.year_level
FROM STUDENT s
JOIN USER u ON s.user_id = u.user_id
JOIN STUDENT_CLASS sc ON s.student_id = sc.student_id
JOIN CLASS c ON sc.class_id = c.class_id
ORDER BY c.class_name ASC, u.name ASC;

-- =====================================================
-- 3. INNER JOIN EXAMPLES
-- =====================================================

-- INNER JOIN: Returns only records that have matching values in both tables
-- Students with their class information (only students who are assigned to classes)
SELECT s.student_id, s.student_number, u.name, c.class_name, sc.year_level
FROM STUDENT s
INNER JOIN USER u ON s.user_id = u.user_id
INNER JOIN STUDENT_CLASS sc ON s.student_id = sc.student_id
INNER JOIN CLASS c ON sc.class_id = c.class_id
ORDER BY c.class_name, u.name;

-- Students with their officer roles (only students who are officers)
SELECT s.student_id, s.student_number, u.name, r.role_name
FROM STUDENT s
INNER JOIN USER u ON s.user_id = u.user_id
INNER JOIN USER_ROLE ur ON u.user_id = ur.user_id
INNER JOIN ROLE r ON ur.role_id = r.role_id
ORDER BY r.role_name, u.name;

-- Students with event registrations (only students who registered for events)
SELECT s.student_id, s.student_number, u.name, e.name as event_name, er.registration_date
FROM STUDENT s
INNER JOIN USER u ON s.user_id = u.user_id
INNER JOIN EVENT_REGISTRATION er ON s.student_id = er.student_id
INNER JOIN EVENT e ON er.event_id = e.event_id
ORDER BY e.event_date, u.name;

-- =====================================================
-- 4. LEFT OUTER JOIN EXAMPLES
-- =====================================================

-- LEFT JOIN: Returns all records from the left table and matching records from the right table
-- All students with their class information (including students not assigned to classes)
SELECT s.student_id, s.student_number, u.name, c.class_name, sc.year_level
FROM STUDENT s
LEFT JOIN USER u ON s.user_id = u.user_id
LEFT JOIN STUDENT_CLASS sc ON s.student_id = sc.student_id
LEFT JOIN CLASS c ON sc.class_id = c.class_id
ORDER BY c.class_name, u.name;

-- All students with their officer roles (including students who are not officers)
SELECT s.student_id, s.student_number, u.name, r.role_name
FROM STUDENT s
LEFT JOIN USER u ON s.user_id = u.user_id
LEFT JOIN USER_ROLE ur ON u.user_id = ur.user_id
LEFT JOIN ROLE r ON ur.role_id = r.role_id
ORDER BY r.role_name, u.name;

-- Find students who are NOT officers (NULL values in role_name)
SELECT s.student_id, s.student_number, u.name, r.role_name
FROM STUDENT s
LEFT JOIN USER u ON s.user_id = u.user_id
LEFT JOIN USER_ROLE ur ON u.user_id = ur.user_id
LEFT JOIN ROLE r ON ur.role_id = r.role_id
WHERE r.role_name IS NULL
ORDER BY u.name;

-- =====================================================
-- 5. RIGHT OUTER JOIN EXAMPLES
-- =====================================================

-- RIGHT JOIN: Returns all records from the right table and matching records from the left table
-- All classes with their students (including classes with no students)
SELECT c.class_name, s.student_id, s.student_number, u.name, sc.year_level
FROM STUDENT s
RIGHT JOIN USER u ON s.user_id = u.user_id
RIGHT JOIN STUDENT_CLASS sc ON s.student_id = sc.student_id
RIGHT JOIN CLASS c ON sc.class_id = c.class_id
ORDER BY c.class_name, u.name;

-- All roles with their assigned users (including roles with no assignments)
SELECT r.role_name, u.name, s.student_number
FROM STUDENT s
RIGHT JOIN USER u ON s.user_id = u.user_id
RIGHT JOIN USER_ROLE ur ON u.user_id = ur.user_id
RIGHT JOIN ROLE r ON ur.role_id = r.role_id
ORDER BY r.role_name, u.name;

-- =====================================================
-- 6. FULL OUTER JOIN (MySQL doesn't support FULL OUTER JOIN directly)
-- =====================================================

-- Simulating FULL OUTER JOIN using UNION
-- All students and all classes (including unmatched records from both sides)
SELECT 'STUDENT' as source, s.student_id, s.student_number, u.name, c.class_name
FROM STUDENT s
LEFT JOIN USER u ON s.user_id = u.user_id
LEFT JOIN STUDENT_CLASS sc ON s.student_id = sc.student_id
LEFT JOIN CLASS c ON sc.class_id = c.class_id

UNION

SELECT 'CLASS' as source, NULL as student_id, NULL as student_number, NULL as name, c.class_name
FROM CLASS c
LEFT JOIN STUDENT_CLASS sc ON c.class_id = sc.class_id
WHERE sc.student_id IS NULL
ORDER BY class_name, name;

-- =====================================================
-- 7. PRACTICAL EXAMPLES FOR YOUR SYSTEM
-- =====================================================

-- Get all students with their complete information
SELECT 
    s.student_id,
    s.student_number,
    u.name,
    u.email,
    c.class_name,
    sc.year_level,
    ay.description as academic_year
FROM STUDENT s
LEFT JOIN USER u ON s.user_id = u.user_id
LEFT JOIN STUDENT_CLASS sc ON s.student_id = sc.student_id
LEFT JOIN CLASS c ON sc.class_id = c.class_id
LEFT JOIN ACADEMIC_YEAR ay ON sc.academic_year_id = ay.academic_year_id
ORDER BY c.class_name, sc.year_level, u.name;

-- Get all officers with their roles and student information
SELECT 
    s.student_id,
    s.student_number,
    u.name,
    u.email,
    r.role_name,
    c.class_name,
    sc.year_level
FROM STUDENT s
INNER JOIN USER u ON s.user_id = u.user_id
INNER JOIN USER_ROLE ur ON u.user_id = ur.user_id
INNER JOIN ROLE r ON ur.role_id = r.role_id
LEFT JOIN STUDENT_CLASS sc ON s.student_id = sc.student_id
LEFT JOIN CLASS c ON sc.class_id = c.class_id
ORDER BY r.role_name, u.name;

-- Get regular students (not officers) with their class information
SELECT 
    s.student_id,
    s.student_number,
    u.name,
    u.email,
    c.class_name,
    sc.year_level
FROM STUDENT s
INNER JOIN USER u ON s.user_id = u.user_id
LEFT JOIN STUDENT_CLASS sc ON s.student_id = sc.student_id
LEFT JOIN CLASS c ON sc.class_id = c.class_id
LEFT JOIN USER_ROLE ur ON u.user_id = ur.user_id
WHERE ur.user_role_id IS NULL
ORDER BY c.class_name, sc.year_level, u.name;

-- Count students by class and year level
SELECT 
    c.class_name,
    sc.year_level,
    COUNT(s.student_id) as student_count
FROM STUDENT s
INNER JOIN STUDENT_CLASS sc ON s.student_id = sc.student_id
INNER JOIN CLASS c ON sc.class_id = c.class_id
GROUP BY c.class_name, sc.year_level
ORDER BY c.class_name, sc.year_level;

-- =====================================================
-- 8. COMPLEX JOIN EXAMPLES
-- =====================================================

-- Students with their events, registrations, and participation
SELECT 
    s.student_number,
    u.name,
    e.name as event_name,
    e.event_date,
    er.registration_date,
    ep.attended,
    ep.feedback
FROM STUDENT s
INNER JOIN USER u ON s.user_id = u.user_id
LEFT JOIN EVENT_REGISTRATION er ON s.student_id = er.student_id
LEFT JOIN EVENT e ON er.event_id = e.event_id
LEFT JOIN EVENT_PARTICIPATION ep ON (s.student_id = ep.student_id AND e.event_id = ep.event_id)
ORDER BY e.event_date, u.name;

-- Students with their inventory borrowing history
SELECT 
    s.student_number,
    u.name,
    ii.item_name,
    ib.borrow_date,
    ib.return_date,
    ic.condition_description
FROM STUDENT s
INNER JOIN USER u ON s.user_id = u.user_id
LEFT JOIN ITEM_BORROWING ib ON s.student_id = ib.student_id
LEFT JOIN INVENTORY_ITEM ii ON ib.item_id = ii.item_id
LEFT JOIN ITEM_CONDITION ic ON ib.condition_id_borrow = ic.condition_id
ORDER BY ib.borrow_date DESC, u.name; 