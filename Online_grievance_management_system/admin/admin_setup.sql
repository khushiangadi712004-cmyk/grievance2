CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id VARCHAR(50) NOT NULL UNIQUE,
    admin_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    mypswd VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin (admin_id, admin_name, email, mypswd)
SELECT 'ADMIN001', 'System Admin', 'admin@gms.com', '$2y$10$J1VQmyhoFNVxMoVGEnlumehqK.s6GKTR99kQd09OwHMid8AOR6tGa'
WHERE NOT EXISTS (
    SELECT 1 FROM admin WHERE admin_id = 'ADMIN001'
);
