<?php
session_start();
require_once "config/db.php";
require_once "utils/User.class.php";
require_once "utils/Link.class.php";

/*
 * STATUS CODES
 * 1 = SUCCESS
 * */

if( isset($_POST['link']) && !empty($_POST['link'])) {
    $link      = $_POST['link'];

    $user = null;

    if(isset($_SESSION['key'])) {
        $user = unserialize($_SESSION['key']);
    }

    $status = Link::addLink($link, $user, $hash);


    if($status === Link::CREATE_SUCCESS) {
        $_SESSION['hash'] = $hash;
        $_SESSION['status'] = 'LINK_SUCCESS';
        header('Location: index.php?shorten=1');
    } else if($status === Link::USER_INVALID) {
        header('Location: index.php?shorten=2');
    } else {
        header('Location: index.php?shorten=3');
    }

} else {
    // if the form fields are not filled, then don't do anything
    header('Location: index.php?shorten=10');
}
