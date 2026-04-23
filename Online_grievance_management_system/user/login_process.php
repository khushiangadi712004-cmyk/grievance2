<?php
session_start();
include '../include/conn.php';

/* ✅ Check if form is submitted */
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = $_POST['email'];
    $mypswd = $_POST['mypswd'];

    $query = "SELECT * FROM student WHERE email='$email' OR register_no='$email'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);

       if(password_verify($mypswd, $row['mypswd'])){

            $_SESSION['register_no'] = $row['register_no'];

            header("Location: dashboard_user.php");
            exit();
        }
        else{
            echo "Wrong password";
        }
    }
    else{
        echo "User not found";
    }

} else {
    echo "Invalid access";
}
?>