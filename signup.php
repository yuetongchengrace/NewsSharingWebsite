<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
</head>
<body>
    <!--form for sign up-->
    <form action="signup.php" method="POST" id="sign_up_form">
        <span class="big">Username:</span><br>
        <input type="text" name="username" id="signup_username"/> <br><br>
        <span class="big">Password:</span><br>
        <input type="password" name="password" id="signup_password" /> <br><br>
        <span class="big">Confirm Password:</span><br>
        <input type="password" name="confirm_password" id="signup_confirm_password" />
        <input type="submit" value="Signup" name="submit"/>
    </form>
    <div id="sign_up_link"><a href="login.php">Click to login</a></div>

    <?php
        if(isset($_POST['submit'])){
            if($_POST['username']!=null&&$_POST['password']!=null){
                require 'database.php';
                //check if password confirmation is successful
                if($_POST['password']!=$_POST['confirm_password']){
                    echo "Password Does Not Match";
                }
                else{
                    $username = (String)$_POST['username'];

                    //salt and hash password
                    $hashed_password = password_hash((String)$_POST['password'], PASSWORD_DEFAULT);

                    //add user to table users
                    $stmt = $mysqli->prepare("insert into users (username, password) values (?, ?)");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }

                    $stmt->bind_param('ss', $username, $hashed_password);
                    
                    $stmt->execute();

                    $stmt->close();
                    echo "Thank you for signing up with us! Click the link to login now!";
                }
            }
            else{
                echo "you need to enter username and password to sign up";
            }
        }
    ?>



</body>
</html>