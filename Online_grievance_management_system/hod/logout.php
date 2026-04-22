<?php
session_start();
session_unset();
session_destroy();

header('Location: hod_login.php');
exit();
?>
