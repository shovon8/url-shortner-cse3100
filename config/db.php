<?php
define('USER', 'shovon');
define('HOST', 'localhost');
define('PASSWORD', '123');
define('DB', 'li.nk');


$mysqli = new mysqli(HOST, USER, PASSWORD, DB);

if($mysqli->connect_errno) {
    die("Error: unable to connect to the database.");
}
