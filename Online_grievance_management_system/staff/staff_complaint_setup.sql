CREATE TABLE IF NOT EXISTS staff_complaint (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    category_id INT NOT NULL,
    department_no INT NOT NULL,
    description TEXT NOT NULL,
    file_upload VARCHAR(255) NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    date_submitted DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_staff_complaint_staff
        FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);
