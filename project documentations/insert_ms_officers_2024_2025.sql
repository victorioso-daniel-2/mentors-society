-- MS OFFICERS A.Y. 2024-2025 INSERTION SCRIPT
-- Updated for new database structure where STUDENT references USER

-- 1. Insert Academic Year 2024-2025
INSERT INTO ACADEMIC_YEAR (start_date, end_date, description) 
VALUES ('2024-06-01', '2025-05-31', 'Academic Year 2024-2025');

-- 2. Insert Roles/Positions
INSERT INTO ROLE (role_name, description) VALUES
('President', 'Organization President'),
('Vice President for Internal Affairs', 'VP for Internal Affairs'),
('Vice President for External Affairs', 'VP for External Affairs'),
('Secretary General', 'Secretary General'),
('Assistant Secretary', 'Assistant Secretary'),
('Treasurer', 'Organization Treasurer'),
('Auditor', 'Organization Auditor'),
('PRO - Math', 'Public Relations Officer for Math'),
('PRO - English', 'Public Relations Officer for English'),
('Business Manager - Math', 'Business Manager for Math'),
('Business Manager - English', 'Business Manager for English'),
('MS Representative', 'MS Representative');

-- 3. Insert Users (Officers)
INSERT INTO USER (name, email) VALUES
('Janella Anne Boncodin', 'janella.boncodin@student.pup.edu.ph'),
('Jenny Sai Onan', 'jenny.onan@student.pup.edu.ph'),
('Kenth Joshua Mesias', 'kenth.mesias@student.pup.edu.ph'),
('Jusell Olmedo', 'jusell.olmedo@student.pup.edu.ph'),
('Rose Ann Salak', 'roseann.salak@student.pup.edu.ph'),
('Harold Lansangan', 'harold.lansangan@student.pup.edu.ph'),
('Queenilyn Martinez', 'queenilyn.martinez@student.pup.edu.ph'),
('Kate Andrei Bonagua', 'kate.bonagua@student.pup.edu.ph'),
('John Ace Ching', 'johnace.ching@student.pup.edu.ph'),
('Ayesha Heart Tolentino', 'ayesha.tolentino@student.pup.edu.ph'),
('Jake Capalaran', 'jake.capalaran@student.pup.edu.ph'),
('Kianpaul Belina', 'kianpaul.belina@student.pup.edu.ph');

-- 4. Insert Students (Officers as Students) - now references USER table
INSERT INTO STUDENT (user_id, student_number) VALUES
((SELECT user_id FROM USER WHERE name = 'Janella Anne Boncodin'), '2021-00112-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Jenny Sai Onan'), '2022-00081-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Kenth Joshua Mesias'), '2023-00024-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Jusell Olmedo'), '2022-00080-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Rose Ann Salak'), '2022-00082-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Harold Lansangan'), '2023-00020-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Queenilyn Martinez'), '2022-00077-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Kate Andrei Bonagua'), '2023-00481-TG-0'),
((SELECT user_id FROM USER WHERE name = 'John Ace Ching'), '2023-00045-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Ayesha Heart Tolentino'), '2022-00083-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Jake Capalaran'), '2023-00042-TG-0'),
((SELECT user_id FROM USER WHERE name = 'Kianpaul Belina'), '2023-00005-TG-0');

-- 5. Insert Classes
INSERT INTO CLASS (class_name, academic_year_id) VALUES
('BSED MATH', (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025')),
('BSED ENGLISH', (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'));

-- 6. Insert Student-Class Assignments
INSERT INTO STUDENT_CLASS (student_id, class_id, academic_year_id, year_level) VALUES
-- Math students
((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Janella Anne Boncodin')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED MATH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Fourth Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Jenny Sai Onan')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED MATH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Third Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Kenth Joshua Mesias')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED MATH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Second Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Jusell Olmedo')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED MATH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Third Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Rose Ann Salak')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED MATH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Third Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Harold Lansangan')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED MATH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Second Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Queenilyn Martinez')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED MATH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Third Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Kate Andrei Bonagua')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED MATH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Second Year'),

-- English students
((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'John Ace Ching')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED ENGLISH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Second Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Ayesha Heart Tolentino')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED ENGLISH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Third Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Jake Capalaran')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED ENGLISH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Second Year'),

((SELECT student_id FROM STUDENT WHERE user_id = (SELECT user_id FROM USER WHERE name = 'Kianpaul Belina')), 
 (SELECT class_id FROM CLASS WHERE class_name = 'BSED MATH'), 
 (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025'), 'Second Year');

-- 7. Insert User-Role Assignments
SET @academic_year_id = (SELECT academic_year_id FROM ACADEMIC_YEAR WHERE description = 'Academic Year 2024-2025');

INSERT INTO USER_ROLE (user_id, role_id, academic_year_id, start_date, end_date) VALUES
((SELECT user_id FROM USER WHERE name = 'Janella Anne Boncodin'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'President'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Jenny Sai Onan'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'Vice President for Internal Affairs'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Kenth Joshua Mesias'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'Vice President for External Affairs'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Jusell Olmedo'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'Secretary General'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Rose Ann Salak'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'Assistant Secretary'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Harold Lansangan'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'Treasurer'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Queenilyn Martinez'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'Auditor'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Kate Andrei Bonagua'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'PRO - Math'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'John Ace Ching'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'PRO - English'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Ayesha Heart Tolentino'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'Business Manager - Math'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Jake Capalaran'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'Business Manager - English'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59'),

((SELECT user_id FROM USER WHERE name = 'Kianpaul Belina'), 
 (SELECT role_id FROM ROLE WHERE role_name = 'MS Representative'), 
 @academic_year_id, '2024-06-01 00:00:00', '2025-05-31 23:59:59');

-- Verification Queries (optional - run these to verify the data was inserted correctly)

-- Check all inserted users
SELECT 'Users' as table_name, COUNT(*) as count FROM USER;

-- Check all inserted students
SELECT 'Students' as table_name, COUNT(*) as count FROM STUDENT;

-- Check user-role assignments
SELECT u.name, r.role_name, ay.description as academic_year
FROM USER_ROLE ur
JOIN USER u ON ur.user_id = u.user_id
JOIN ROLE r ON ur.role_id = r.role_id
JOIN ACADEMIC_YEAR ay ON ur.academic_year_id = ay.academic_year_id
ORDER BY r.role_name;

-- Check student-class assignments with user info
SELECT u.name, s.student_number, c.class_name, sc.year_level, ay.description as academic_year
FROM STUDENT_CLASS sc
JOIN STUDENT s ON sc.student_id = s.student_id
JOIN USER u ON s.user_id = u.user_id
JOIN CLASS c ON sc.class_id = c.class_id
JOIN ACADEMIC_YEAR ay ON sc.academic_year_id = ay.academic_year_id
ORDER BY c.class_name, sc.year_level; 