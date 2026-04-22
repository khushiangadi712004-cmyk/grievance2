ALTER TABLE complaint
    ADD COLUMN IF NOT EXISTS assigned_to VARCHAR(20) NULL,
    ADD COLUMN IF NOT EXISTS escalated_to VARCHAR(20) NULL,
    ADD COLUMN IF NOT EXISTS escalation_reason TEXT NULL,
    ADD COLUMN IF NOT EXISTS escalated_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS handled_by_role VARCHAR(20) NULL;

ALTER TABLE staff_complaint
    ADD COLUMN IF NOT EXISTS assigned_to VARCHAR(20) NULL,
    ADD COLUMN IF NOT EXISTS escalated_to VARCHAR(20) NULL,
    ADD COLUMN IF NOT EXISTS escalation_reason TEXT NULL,
    ADD COLUMN IF NOT EXISTS escalated_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS handled_by_role VARCHAR(20) NULL;

UPDATE complaint
SET assigned_to = CASE category_id
    WHEN 1 THEN 'HOD'
    WHEN 2 THEN 'Principal'
    WHEN 3 THEN 'Management'
    ELSE assigned_to
END
WHERE assigned_to IS NULL OR assigned_to = '';
