<?php
require_once "config/db.php";
require_once "utils/User.class.php";
require_once "utils/Link.class.php";

if(isset($_GET['link']) && !empty($_GET['link'])) {
    $src = $_GET['link'];
    
}



