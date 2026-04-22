<?php
session_start();
if(!isset($_SESSION['staff_id'])){
    header('Location: staff_login.php');
    exit();
}

header('Location: my_complaints.php');
exit();
