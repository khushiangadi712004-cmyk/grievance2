<?php
include 'include/conn.php';

// Auto escalate HOD complaints pending for 2 days.
$q1 = "UPDATE complaint
       SET assigned_to = 'Principal',
           escalated_to = 'Principal',
           escalation_reason = 'Auto escalated after 2 days pending at HOD level',
           escalated_at = NOW(),
           handled_by_role = 'System'
       WHERE status = 'Pending'
         AND assigned_to = 'HOD'
         AND TIMESTAMPDIFF(DAY, date_submitted, NOW()) >= 2";
mysqli_query($conn, $q1);

$q2 = "UPDATE staff_complaint
       SET assigned_to = 'Principal',
           escalated_to = 'Principal',
           escalation_reason = 'Auto escalated after 2 days pending at HOD level',
           escalated_at = NOW(),
           handled_by_role = 'System'
       WHERE status = 'Pending'
         AND assigned_to = 'HOD'
         AND TIMESTAMPDIFF(DAY, date_submitted, NOW()) >= 2";
mysqli_query($conn, $q2);

// Auto escalate Principal complaints pending for 4 days.
$q3 = "UPDATE complaint
       SET assigned_to = 'Management',
           escalated_to = 'Management',
           escalation_reason = 'Auto escalated after 4 days pending at Principal level',
           escalated_at = NOW(),
           handled_by_role = 'System'
       WHERE status = 'Pending'
         AND assigned_to = 'Principal'
         AND TIMESTAMPDIFF(DAY, date_submitted, NOW()) >= 4";
mysqli_query($conn, $q3);

$q4 = "UPDATE staff_complaint
       SET assigned_to = 'Management',
           escalated_to = 'Management',
           escalation_reason = 'Auto escalated after 4 days pending at Principal level',
           escalated_at = NOW(),
           handled_by_role = 'System'
       WHERE status = 'Pending'
         AND assigned_to = 'Principal'
         AND TIMESTAMPDIFF(DAY, date_submitted, NOW()) >= 4";
mysqli_query($conn, $q4);

echo "Auto escalation completed.";
