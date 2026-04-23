CREATE TABLE IF NOT EXISTS management (
    management_id INT PRIMARY KEY,
    mname VARCHAR(20) NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO staff (staff_id, stname, department_no, email, password, created_at, phone_no, design)
SELECT 201, 'Management One', 1, 'management1@gms.com', '$2y$12$xUIhKtwrqHiTUI0/s5BbZePzv925x1v9SQLZIkPQ4MptsNn55xY4e', NOW(), 987650001, NULL
WHERE NOT EXISTS (
    SELECT 1 FROM staff WHERE staff_id = 201
);

INSERT INTO staff (staff_id, stname, department_no, email, password, created_at, phone_no, design)
SELECT 202, 'Management Two', 1, 'management2@gms.com', '$2y$12$WPbtPYGSM9y61vHZ73tgouakdlt/CfSho5RGbP6Gx41VHb0pZRQCC', NOW(), 987650002, NULL
WHERE NOT EXISTS (
    SELECT 1 FROM staff WHERE staff_id = 202
);

INSERT INTO management (management_id, mname, password)
SELECT 201, 'Management One', '$2y$12$xUIhKtwrqHiTUI0/s5BbZePzv925x1v9SQLZIkPQ4MptsNn55xY4e'
WHERE NOT EXISTS (
    SELECT 1 FROM management WHERE management_id = 201
);

INSERT INTO management (management_id, mname, password)
SELECT 202, 'Management Two', '$2y$12$WPbtPYGSM9y61vHZ73tgouakdlt/CfSho5RGbP6Gx41VHb0pZRQCC'
WHERE NOT EXISTS (
    SELECT 1 FROM management WHERE management_id = 202
);
