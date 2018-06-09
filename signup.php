<?php
session_start();
require_once "config/db.php";
require_once "utils/User.class.php";


/*
 * STATUS CODES
 * 1 = SUCCESS
 * 2 = DUPLICATE EMAIL
 * 3 = FATAL ERROR
 * 5 = FORM FIELDS ARE INCORRECTLY FILLED
 * */

if( isset($_POST['name']) && !empty($_POST['name'])
&&  isset($_POST['email']) && !empty($_POST['email'])
&&  isset($_POST['password']) && !empty($_POST['password']) ) {
    $email      = $_POST['email'];
    $name       = $_POST['name'];
    $password   = $_POST['password'];

    $status = User::create($email, $password, $name);


    if($status === User::CREATE_SUCCESS) {
        $_SESSION['status'] = 'SIGN_SUCCESS';
        header('Location: index.php?signup=1');
    } else if($status === User::CREATE_FAILED_DUPLICATE_EMAIL) {
        header('Location: index.php?signup=2');
    } else {
        header('Location: index.php?signup=3');
    }

} else {
    // if the form fields are not filled, then don't do anything
    header('Location: index.php?signup=10');
}
