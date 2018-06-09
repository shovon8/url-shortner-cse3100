<?php
session_start();
require_once "config/db.php";
require_once "utils/User.class.php";
require_once "utils/Link.class.php";


$loggedIn = false;

if(isset($_SESSION['key'])) {
    $user = unserialize($_SESSION['key']);

    if($user->getId() > 0) {
        $loggedIn = true;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Short and Share with Li.nk</title>
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/libs/fontawesome/css/fontawesome-all.min.css">
</head>
<body>
<div class="page">
<?php
if(isset($_SESSION['status']) && $_SESSION['status'] === 'SIGN_SUCCESS') {
?>
 <div class="status status-success">
    <p><b class="fa fa-check-circle"> Sign up complete.</b></p>
 </div>
<?php
session_unset();
}
?>


    <?php
    if($loggedIn) {
    ?>

    <div class="status status-login">
        <p><b class="fa fa-user"> <?php echo $user->getName(); ?></b> | <a href="logout.php">Log Out</a></p>
    </div>
    <?php } ?>

    <header>
        <h1>Li.nk - Smarter, Shorter Link</h1>
        <p>Short and Share with the World for Free</p>
    </header>


    <div class="body">
        <div class="linkContainer">
            <form id="shortenForm" action="shorten.php" method="post">
                <input type="text" name="link" value="" placeholder="Put your link here" />
                <input type="hidden" name="anonymous" value="1" />
                <button type="submit">Shorten</button>
            </form>

            <?php
            if(isset($_SESSION['status']) && $_SESSION['status'] === 'LINK_SUCCESS') {

            ?>
                <div class="shortSuccess">
                    <p><b class="fa fa-link"></b> Here is your generated link:</p>
                    <span>http://<?php echo $_SERVER['SERVER_NAME']; ?>/<?php echo $_SESSION['hash']; ?></span>
                </div>

            <?php
            $_SESSION['status'] = null;
            }
            ?>
        </div>

        <?php 
        if($loggedIn) {
        ?>
            <div class="linkList">
                <h2>Your Recently Generated Links:</h2>
                <table>
                    <tr>
                        <th>Src Link</th>
                        <th>Hash Link</th>
                        <th>Visits</th>
                        <th>Date Added</th>
                    </tr>
                
                <?php
                $status = Link::getLinks($user, $links);

                if($status === Link::ITERATION_SUCCESS) {
                    foreach($links as $link) {
                        echo "<tr>";
                        echo "<td>{$link['link']}</td>";
                        echo "<td><a href='http://{$_SERVER['SERVER_NAME']}/{$link['hash']}'>http://{$_SERVER['SERVER_NAME']}/{$link['hash']}</a></td>";
                        echo "<td><span>{$link['visits']}</span></td>";
                        echo "<td>" . date('M d, Y', time($link['date'])) . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
                </table>
            </div>
        <?php
        }
        ?>

        <?php
        if(!$loggedIn) {
            ?>
            <div class="loginContainer">
                <p>Already have an account? Please log in.</p>
                <form id="loginForm" action="login.php" method="post">
                    <input type="text" name="email" value="" placeholder="Email"/>
                    <input type="password" name="password" value="" placeholder="Password"/>
                    <button type="submit"><b class="fa fa-lock"> Log In</b></button>
                </form>
            </div>
            <div class="signupContainer">
                <p>Don't have an account? Please sign up.</p>
                <form id="signupForm" action="signup.php" method="post">
                    <input type="text" name="email" value="" placeholder="Email"/>
                    <input type="text" name="name" value="" placeholder="Full Name"/>
                    <input type="password" name="password" value="" placeholder="Password"/>
                    <button type="submit"><b class="fa fa-lock"> Sign Up</b></button>
                </form>
            </div>
            <?php
        }
        ?>


    </div>



    <footer>
        <p>&copy; <?php echo date('Y');?> Li.nk Inc. All rights reserved</p>
    </footer>
</div>
</body>
</html>