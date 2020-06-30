<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
</head>
<body>
    <!--Link to about us-->
    <div class="about"><a href="about.php">About Us</a></div>
    <div id="title">Your Custom News Website</div>
    <!--form for log in-->
    <form action="login.php" method="POST" id="log_in_form">
        <span class="big">Username:</span>
        <input type="text" name="username" id="login_username"/>
        <span class="big">Password:</span>
        <input type="password" name="password" id="login_password"/>
        <input type="submit" value="Login" name="submit1" class="login_page_buttons"/>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
    </form>

    <!--form for sign up-->
    <div id="sign_up_link"><a href="signup.php">Click to sign up</a></div>

    <!--form for guest user-->
    <form action="main.php" method="POST" id="log_in_as_visitor">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
        Don't want to sign up?
        <input type="submit" value="Login as visitor" name="submit2" class="login_page_buttons"/>
    </form>

    <?php
        
        if(isset($_POST['submit2'])){
            session_start();
            $_SESSION["token"]=bin2hex(openssl_random_pseudo_bytes(32));
            exit;
        }

        if(isset($_POST['submit1'])&&$_POST['username']!=null){
            $username=(String)$_POST['username'];
            $input_password=(String)$_POST['password'];
            require 'database.php';

            //perpare login for user
            $stmt = $mysqli->prepare("select * from users where username='$username'");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
    
            $stmt->execute();
            $result= $stmt->get_result();
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            //verify password
            if(password_verify($input_password, $hashed_password)){
                session_start();
                //set token
                $_SESSION["token"]=bin2hex(openssl_random_pseudo_bytes(32));
                $_SESSION["username"]=(String)$username;
                header("Location: main.php");
                exit;
            }
            else{
                echo '<div id="login_not_valid">';
                echo "Username or password is not valid";
                echo '</div>';
            }
    
            $stmt->close();
        }
    ?>
</body>
</html>
