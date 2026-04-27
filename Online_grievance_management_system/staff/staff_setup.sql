CREATE TABLE IF NOT EXISTS staff (
    staff_id INT PRIMARY KEY,
    stname VARCHAR(30) NULL,
    department_no INT NULL,
    email VARCHAR(30) NULL,
    password VARCHAR(60) NULL,
    created_at DATETIME NULL,
    phone_no VARCHAR(15) NULL,
    design VARCHAR(15) NULL
);

INSERT INTO staff (staff_id, stname, department_no, email, password, created_at, phone_no, design)
SELECT 101, 'Support Staff', 1, 'staff@gms.com', '$2y$10$J1VQmyhoFNVxMoVGEnlumehqK.s6GKTR99kQd09OwHMid8AOR6tGa', NOW(), 987654321, NULL
WHERE NOT EXISTS (
    SELECT 1 FROM staff WHERE staff_id = 101
);
