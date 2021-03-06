<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
</head>
<body>
    <div id="about_title">Welcome to Your Custom News Website!</div>
    <div id="intro">
        We are an open platform where you can connect with folks around the world by posting/commenting about any trending news!<br>
        Without signing up, you can view any story posted on our website. Signed up users will have extra access including posting news/comments, editing and deleting
        any previously made posts or comments. Enjoy your time on this website!
    </div>
    <?php
    session_start();
    if(isset($_SESSION["username"])){
        ?>
        <div><a href="main.php">Back to main page</a></div>
        <?php
    }
    else{
    ?>
    <div>Already have an account? <a href="login.php">Log in now!</a></div>
    <div>Don't have an account yet? <a href="signup.php">Sign up now</a> to enjoy the best news sharing website</div>, or <a href="main.php"> visit as guest</a> now and sign up anytime u want!</div>
    <?php
    session_destroy();
    }
    ?>
</body>
</html>