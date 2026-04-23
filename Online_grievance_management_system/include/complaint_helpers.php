<?php

if (!function_exists('category_name')) {
    function category_name($categoryId)
    {
        $categories = [
            1 => 'Academic',
            2 => 'Infrastructure',
            3 => 'Administration',
        ];

        return $categories[(int) $categoryId] ?? ('Category ' . $categoryId);
    }
}

if (!function_exists('department_name')) {
    function department_name($departmentNo)
    {
        $departments = [
            1 => 'BCA',
            2 => 'BSC',
            3 => 'B.COM',
            4 => 'BBA',
        ];

        return $departments[(int) $departmentNo] ?? ('Dept ' . $departmentNo);
    }
}

if (!function_exists('category_route')) {
    function category_route($categoryId)
    {
        $routes = [
            1 => 'HOD',
            2 => 'Principal',
            3 => 'Management',
        ];

        return $routes[(int) $categoryId] ?? 'HOD';
    }
}

if (!function_exists('next_escalation_role')) {
    function next_escalation_role($roleName)
    {
        return match ($roleName) {
            'HOD' => 'Principal',
            'Principal' => 'Management',
            default => null,
        };
    }
}

if (!function_exists('status_class')) {
    function status_class($status)
    {
        if ($status === 'Pending') {
            return 'pending';
        }

        if ($status === 'In Progress') {
            return 'progress';
        }

        return 'resolved';
    }
}

if (!function_exists('column_exists')) {
    function column_exists($conn, $tableName, $columnName)
    {
        $tableName = mysqli_real_escape_string($conn, $tableName);
        $columnName = mysqli_real_escape_string($conn, $columnName);
        $result = @mysqli_query($conn, "SHOW COLUMNS FROM {$tableName} LIKE '{$columnName}'");
        return $result && mysqli_num_rows($result) > 0;
    }
}

if (!function_exists('ensure_complaint_columns')) {
    function ensure_complaint_columns($conn)
    {
        static $ensured = false;

        if ($ensured || !$conn) {
            return;
        }

        $requiredColumns = [
            'complaint' => [
                'assigned_to' => "ALTER TABLE complaint ADD COLUMN assigned_to VARCHAR(50) NULL",
                'escalated_to' => "ALTER TABLE complaint ADD COLUMN escalated_to VARCHAR(20) NULL",
                'escalation_reason' => "ALTER TABLE complaint ADD COLUMN escalation_reason TEXT NULL",
                'escalated_at' => "ALTER TABLE complaint ADD COLUMN escalated_at DATETIME NULL",
                'handled_by_role' => "ALTER TABLE complaint ADD COLUMN handled_by_role VARCHAR(20) NULL",
            ],
            'staff_complaint' => [
                'assigned_to' => "ALTER TABLE staff_complaint ADD COLUMN assigned_to VARCHAR(50) NULL",
                'escalated_to' => "ALTER TABLE staff_complaint ADD COLUMN escalated_to VARCHAR(20) NULL",
                'escalation_reason' => "ALTER TABLE staff_complaint ADD COLUMN escalation_reason TEXT NULL",
                'escalated_at' => "ALTER TABLE staff_complaint ADD COLUMN escalated_at DATETIME NULL",
                'handled_by_role' => "ALTER TABLE staff_complaint ADD COLUMN handled_by_role VARCHAR(20) NULL",
            ],
        ];

        foreach ($requiredColumns as $tableName => $columns) {
            foreach ($columns as $columnName => $query) {
                if (!column_exists($conn, $tableName, $columnName)) {
                    @mysqli_query($conn, $query);
                }
            }
        }

        @mysqli_query(
            $conn,
            "UPDATE complaint
             SET assigned_to = CASE category_id
                 WHEN 1 THEN 'HOD'
                 WHEN 2 THEN 'Principal'
                 WHEN 3 THEN 'Management'
                 ELSE assigned_to
             END
             WHERE assigned_to IS NULL OR assigned_to = ''"
        );

        @mysqli_query(
            $conn,
            "UPDATE staff_complaint
             SET assigned_to = CASE category_id
                 WHEN 1 THEN 'HOD'
                 WHEN 2 THEN 'Principal'
                 WHEN 3 THEN 'Management'
                 ELSE assigned_to
             END
             WHERE assigned_to IS NULL OR assigned_to = ''"
        );

        $ensured = true;
    }
}

if (!function_exists('fetch_count')) {
    function fetch_count($conn, $query)
    {
        ensure_complaint_columns($conn);

        $result = @mysqli_query($conn, $query);
        if (!$result) {
            return 0;
        }

        $row = mysqli_fetch_assoc($result);
        return $row ? (int) array_values($row)[0] : 0;
    }
}

if (!function_exists('insert_complaint_history')) {
    function insert_complaint_history($conn, $complaintId, $sourceType, $status, $remarks, $handledByRole)
    {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO complaint_history (complaint_id, source_type, status, remarks, handled_by_role, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );

        if (!$stmt) {
            return;
        }

        mysqli_stmt_bind_param($stmt, 'issss', $complaintId, $sourceType, $status, $remarks, $handledByRole);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

if (!function_exists('handle_complaint_action')) {
    function handle_complaint_action($conn, $tableName, $complaintId, $sourceType, $roleName, $action, $targetRole = '', $remarks = '')
    {
        ensure_complaint_columns($conn);

        $allowedTables = ['complaint', 'staff_complaint'];
        if (!in_array($tableName, $allowedTables, true) || $complaintId <= 0) {
            return 'Invalid complaint selection.';
        }

        $remarks = trim($remarks);

        if ($action === 'progress') {
            $query = "UPDATE {$tableName}
                      SET status = 'In Progress',
                          handled_by_role = ?,
                          escalation_reason = CASE WHEN escalation_reason IS NULL OR escalation_reason = '' THEN ? ELSE escalation_reason END
                      WHERE complaint_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                return 'Unable to update complaint.';
            }
            mysqli_stmt_bind_param($stmt, 'ssi', $roleName, $remarks, $complaintId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            insert_complaint_history($conn, $complaintId, $sourceType, 'In Progress', $remarks, $roleName);
            return 'Complaint marked as in progress.';
        }

        if ($action === 'resolve') {
            if ($tableName === 'complaint') {
                $query = "UPDATE complaint
                          SET status = 'Resolved',
                              remarks = ?,
                              date_resolved = NOW(),
                              handled_by_role = ?
                          WHERE complaint_id = ?";
            } else {
                $query = "UPDATE staff_complaint
                          SET status = 'Resolved',
                              escalation_reason = CASE WHEN ? = '' THEN escalation_reason ELSE ? END,
                              handled_by_role = ?
                          WHERE complaint_id = ?";
            }

            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                return 'Unable to resolve complaint.';
            }

            if ($tableName === 'complaint') {
                mysqli_stmt_bind_param($stmt, 'ssi', $remarks, $roleName, $complaintId);
            } else {
                mysqli_stmt_bind_param($stmt, 'sssi', $remarks, $remarks, $roleName, $complaintId);
            }

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            insert_complaint_history($conn, $complaintId, $sourceType, 'Resolved', $remarks, $roleName);
            return 'Complaint resolved successfully.';
        }

        if ($action === 'escalate') {
            if ($targetRole === '') {
                $targetRole = next_escalation_role($roleName);
            }

            $allowedTargets = ['HOD', 'Principal', 'Management'];
            if (!in_array($targetRole, $allowedTargets, true)) {
                return 'No higher escalation level available.';
            }

            $query = "UPDATE {$tableName}
                      SET escalated_to = ?,
                          assigned_to = ?,
                          escalation_reason = ?,
                          escalated_at = NOW(),
                          handled_by_role = ?,
                          status = 'In Progress'
                      WHERE complaint_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                return 'Unable to escalate complaint.';
            }

            mysqli_stmt_bind_param($stmt, 'ssssi', $targetRole, $targetRole, $remarks, $roleName, $complaintId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            insert_complaint_history($conn, $complaintId, $sourceType, 'Escalated to ' . $targetRole, $remarks, $roleName);
            return 'Complaint escalated to ' . $targetRole . '.';
        }

        return 'Invalid action requested.';
    }
}
