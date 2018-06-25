<?php
define('USER', 'YOUR_DB_USERNAME');
define('HOST', 'YOUR_HOSTNAME');
define('PASSWORD', 'YOUR_DB_PASSWORD');
define('DB', 'YOUR_DB_NAME');


$mysqli = new mysqli(HOST, USER, PASSWORD, DB);

if($mysqli->connect_errno) {
    die("Error: unable to connect to the database.");
}
