<?php
session_start();
require_once "config/db.php";
require_once "utils/User.class.php";
require_once "utils/Link.class.php";

$hash = $_GET['hash'];

$link = Link::getLinkByHash($hash);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="refresh" content="5;URL=<?php echo $link;?>"/>
    <title>Redirecting...</title>
    <link rel="stylesheet" href="assets/libs/fontawesome/css/fontawesome-all.min.css">
</head>
<body>
<h1>Please wait...</h1>
<p>You will be redirected to <i class="fas fa-external-link-alt"></i> <span style="color: green;"><?php echo $link;?></span> within 5 seconds.</p>
<p>If not, please click <a href="<?php echo $link;?>">here</a>.</p>
</body>
</html>