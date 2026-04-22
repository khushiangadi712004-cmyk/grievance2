-- Import this file in phpMyAdmin after selecting the grievance database.
-- It adds sample student/user records for testing login and complaint submission.
-- Passwords are hashed to match the current user login flow.

INSERT INTO student (sname, register_no, email, phone, mypswd, department_no)
SELECT 'Aarav Sharma', '24BCA101', 'aarav.sharma@gms.com', '9876501001', '$2y$10$wwGy6pDlYsvrcAggmRketuRIJkgj4dl0460hnzgB3hbXkVrs1kSKK', 1
WHERE NOT EXISTS (
    SELECT 1 FROM student WHERE register_no = '24BCA101'
);

INSERT INTO student (sname, register_no, email, phone, mypswd, department_no)
SELECT 'Nisha Verma', '24BSC102', 'nisha.verma@gms.com', '9876501002', '$2y$10$cbC5SevZsJvOgYFg/j/qg.DsCpQfX4HvSD7IomruwWcXuWyPV/eUi', 2
WHERE NOT EXISTS (
    SELECT 1 FROM student WHERE register_no = '24BSC102'
);

INSERT INTO student (sname, register_no, email, phone, mypswd, department_no)
SELECT 'Rohit Mehta', '24BCOM103', 'rohit.mehta@gms.com', '9876501003', '$2y$10$RE9pSTuP2X3u6kojmBg0m.DtjucUTg2em30volhoZbDA1WwEoksMW', 3
WHERE NOT EXISTS (
    SELECT 1 FROM student WHERE register_no = '24BCOM103'
);

INSERT INTO student (sname, register_no, email, phone, mypswd, department_no)
SELECT 'Priya Nair', '24BBA104', 'priya.nair@gms.com', '9876501004', '$2y$10$wwGy6pDlYsvrcAggmRketuRIJkgj4dl0460hnzgB3hbXkVrs1kSKK', 4
WHERE NOT EXISTS (
    SELECT 1 FROM student WHERE register_no = '24BBA104'
);

INSERT INTO student (sname, register_no, email, phone, mypswd, department_no)
SELECT 'Kiran Patel', '24BCA105', 'kiran.patel@gms.com', '9876501005', '$2y$10$cbC5SevZsJvOgYFg/j/qg.DsCpQfX4HvSD7IomruwWcXuWyPV/eUi', 1
WHERE NOT EXISTS (
    SELECT 1 FROM student WHERE register_no = '24BCA105'
);
