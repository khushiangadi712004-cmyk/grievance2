<?php

if (!function_exists('notification_table_name')) {
    function notification_table_name($conn)
    {
        static $table_name = null;

        if ($table_name !== null) {
            return $table_name;
        }

        $possible_tables = ['notification', 'notiification'];

        foreach ($possible_tables as $possible_table) {
            $stmt = mysqli_prepare(
                $conn,
                "SELECT TABLE_NAME
                 FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                 LIMIT 1"
            );

            if (!$stmt) {
                continue;
            }

            mysqli_stmt_bind_param($stmt, 's', $possible_table);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                mysqli_stmt_close($stmt);
                $table_name = $possible_table;
                return $table_name;
            }

            mysqli_stmt_close($stmt);
        }

        $table_name = '';
        return $table_name;
    }
}

if (!function_exists('notification_table_exists')) {
    function notification_table_exists($conn)
    {
        return notification_table_name($conn) !== '';
    }
}

if (!function_exists('add_notification')) {
    function add_notification($conn, $complaint_id, $user_type, $user_id, $message)
    {
        if (!notification_table_exists($conn)) {
            return false;
        }

        $table_name = notification_table_name($conn);
        $status = 'Unread';
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO `{$table_name}` (complaint_id, user_type, user_id, message, date_sent, status)
             VALUES (?, ?, ?, ?, NOW(), ?)"
        );

        if (!$stmt) {
            return false;
        }

        $user_id = (string) $user_id;
        mysqli_stmt_bind_param($stmt, 'issss', $complaint_id, $user_type, $user_id, $message, $status);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }
}

if (!function_exists('notify_assigned_role')) {
    function notify_assigned_role($conn, $complaint_id, $assigned_to, $department_no, $message)
    {
        if ($assigned_to === 'HOD') {
            $stmt = mysqli_prepare($conn, "SELECT hod_id FROM hod WHERE department_no = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $department_no);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while ($row = mysqli_fetch_assoc($result)) {
                    add_notification($conn, $complaint_id, 'HOD', $row['hod_id'], $message);
                }
                mysqli_stmt_close($stmt);
            }
            return;
        }

        if ($assigned_to === 'Principal') {
            $stmt = mysqli_prepare($conn, "SELECT principal_id FROM principal");
            if ($stmt) {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while ($row = mysqli_fetch_assoc($result)) {
                    add_notification($conn, $complaint_id, 'Principal', $row['principal_id'], $message);
                }
                mysqli_stmt_close($stmt);
            }
            return;
        }

        if ($assigned_to === 'Management') {
            $stmt = mysqli_prepare($conn, "SELECT management_id FROM management");
            if ($stmt) {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while ($row = mysqli_fetch_assoc($result)) {
                    add_notification($conn, $complaint_id, 'Management', $row['management_id'], $message);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

if (!function_exists('get_complaint_owner')) {
    function get_complaint_owner($conn, $table_name, $complaint_id)
    {
        if ($table_name === 'complaint') {
            $stmt = mysqli_prepare($conn, "SELECT register_no AS user_id FROM complaint WHERE complaint_id = ? LIMIT 1");
            $user_type = 'Student';
        } elseif ($table_name === 'staff_complaint') {
            $stmt = mysqli_prepare($conn, "SELECT staff_id AS user_id FROM staff_complaint WHERE complaint_id = ? LIMIT 1");
            $user_type = 'Staff';
        } else {
            return null;
        }

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $complaint_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$row || $row['user_id'] === null || $row['user_id'] === '') {
            return null;
        }

        return [
            'user_type' => $user_type,
            'user_id' => (string) $row['user_id'],
        ];
    }
}

if (!function_exists('get_user_notifications')) {
    function get_user_notifications($conn, $user_type, $user_id, $limit = 5)
    {
        $notifications = [];
        if (!notification_table_exists($conn)) {
            return $notifications;
        }

        $limit = max(1, (int) $limit);
        $user_id = (string) $user_id;

        $table_name = notification_table_name($conn);
        $stmt = mysqli_prepare(
            $conn,
            "SELECT notification_id, complaint_id, message, date_sent, status
             FROM `{$table_name}`
             WHERE user_type = ? AND user_id = ?
             ORDER BY date_sent DESC, notification_id DESC
             LIMIT ?"
        );

        if (!$stmt) {
            return $notifications;
        }

        mysqli_stmt_bind_param($stmt, 'ssi', $user_type, $user_id, $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $notifications;
    }
}

if (!function_exists('mark_notification_read')) {
    function mark_notification_read($conn, $notification_id, $user_type, $user_id)
    {
        if (!notification_table_exists($conn)) {
            return false;
        }

        $table_name = notification_table_name($conn);
        $status = 'Read';
        $user_id = (string) $user_id;
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE `{$table_name}`
             SET status = ?
             WHERE notification_id = ? AND user_type = ? AND user_id = ?"
        );

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'siss', $status, $notification_id, $user_type, $user_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }
}
