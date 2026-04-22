CREATE TABLE IF NOT EXISTS hod (
    hod_id VARCHAR(50) PRIMARY KEY,
    hod_name VARCHAR(100) NOT NULL,
    department_no INT NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO hod (hod_id, hod_name, department_no, email, password)
SELECT 'HOD001', 'Department HOD', 1, 'hod@gms.com', '$2y$10$J1VQmyhoFNVxMoVGEnlumehqK.s6GKTR99kQd09OwHMid8AOR6tGa'
WHERE NOT EXISTS (
    SELECT 1 FROM hod WHERE hod_id = 'HOD001'
);
