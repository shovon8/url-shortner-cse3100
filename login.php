<?php
require_once "config/db.php";
require_once "utils/User.class.php";


if(isset($_POST['email']) && !empty($_POST['email'])
    &&  isset($_POST['password']) && !empty($_POST['password']) ) {
    $email      = $_POST['email'];
    $password   = $_POST['password'];

    $status = User::auth($email, $password, $data);


    if($status === User::LOGIN_SUCCESS) {
        session_start();
        $_SESSION['key'] = serialize($data);
        header('Location: index.php');
    } else {
        header('Location: index.php?login=0');
    }

} else {
    // if the form fields are not filled, then don't do anything
    header('Location: index.php?login=20');
}