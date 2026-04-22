CREATE TABLE IF NOT EXISTS principal (
    principal_id VARCHAR(50) PRIMARY KEY,
    principal_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO principal (principal_id, principal_name, email, password)
SELECT 'PRINCIPAL001', 'College Principal', 'principal@gms.com', '$2y$10$J1VQmyhoFNVxMoVGEnlumehqK.s6GKTR99kQd09OwHMid8AOR6tGa'
WHERE NOT EXISTS (
    SELECT 1 FROM principal WHERE principal_id = 'PRINCIPAL001'
);
