-- Import this file in phpMyAdmin after selecting the grievance database.
-- It inserts realistic college complaints using existing student register numbers.
-- If you have fewer than 6 students, only the matching SELECT statements will insert rows.

INSERT INTO complaint
    (register_no, staff_id, category_id, department_no, description, file_upload, status, date_submitted)
SELECT register_no, NULL, 2, 1,
       'Two tube lights in Classroom BCA-204 are not working properly, making evening lectures difficult to attend.',
       'classroom-light-issue.jpg',
       'Pending',
       '2026-03-18 09:20:00'
FROM student
ORDER BY register_no
LIMIT 1;

INSERT INTO complaint
    (register_no, staff_id, category_id, department_no, description, file_upload, status, date_submitted)
SELECT register_no, NULL, 2, 2,
       'Water leakage is visible near the science block corridor after rainfall, and the floor becomes slippery during class hours.',
       'corridor-water-leak.jpg',
       'In Progress',
       '2026-03-20 11:10:00'
FROM student
ORDER BY register_no
LIMIT 1 OFFSET 1;

INSERT INTO complaint
    (register_no, staff_id, category_id, department_no, description, file_upload, status, date_submitted)
SELECT register_no, NULL, 3, 3,
       'Scholarship verification for our semester is still pending in the office, and students have not received any update.',
       '',
       'Pending',
       '2026-03-22 14:35:00'
FROM student
ORDER BY register_no
LIMIT 1 OFFSET 2;

INSERT INTO complaint
    (register_no, staff_id, category_id, department_no, description, file_upload, status, date_submitted)
SELECT register_no, NULL, 2, 4,
       'Several benches in the library reading section are damaged and one side support is loose, which is unsafe for students.',
       'library-bench-damage.jpg',
       'Resolved',
       '2026-03-24 10:05:00'
FROM student
ORDER BY register_no
LIMIT 1 OFFSET 3;

INSERT INTO complaint
    (register_no, staff_id, category_id, department_no, description, file_upload, status, date_submitted)
SELECT register_no, NULL, 2, 1,
       'The open theatre seating area has not been cleaned properly after the last event, and plastic waste is still lying around.',
       'open-theatre-cleanliness.jpg',
       'Pending',
       '2026-03-25 16:40:00'
FROM student
ORDER BY register_no
LIMIT 1 OFFSET 4;

INSERT INTO complaint
    (register_no, staff_id, category_id, department_no, description, file_upload, status, date_submitted)
SELECT register_no, NULL, 1, 2,
       'Internal assessment marks for one subject are not yet visible in the portal even after the faculty announced submission.',
       '',
       'In Progress',
       '2026-03-27 12:15:00'
FROM student
ORDER BY register_no
LIMIT 1 OFFSET 5;
