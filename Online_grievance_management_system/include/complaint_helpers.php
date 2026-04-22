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

if (!function_exists('fetch_count')) {
    function fetch_count($conn, $query)
    {
        $result = mysqli_query($conn, $query);
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
        $allowedTables = ['complaint', 'staff_complaint'];
        if (!in_array($tableName, $allowedTables, true) || $complaintId <= 0) {
            return 'Invalid complaint selection.';
        }

        $escapedTable = $tableName;
        $remarks = trim($remarks);

        if ($action === 'progress') {
            $query = "UPDATE {$escapedTable}
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

            $query = "UPDATE {$escapedTable}
                      SET escalated_to = ?,
                        
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
