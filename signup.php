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
                //check if password confirmation is successful
                if($_POST['password']!=$_POST['confirm_password']){
                    echo "Password Does Not Match";
                }
                else{
                    $check_user_not_exist=true;
                    require 'database.php';

                    $stmt1 = $mysqli->prepare("select username from users");
                    if(!$stmt1){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $stmt1->execute();
                    $stmt1->bind_result($username_from_table);
                    while($stmt1->fetch()){
                        if ($username_from_table==$_POST['username']){
                            echo "Hi ";
                            echo $_POST['username'];
                            echo "! You already signed up with us!";
                            $check_user_not_exist=false;
                            exit;
                        }
                    }
                    $stmt1->close();
                    if($check_user_not_exist){
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
                        echo "Thank you for signing up with us! Click the link to login now!<br>";
                        echo "Your username is: ";
                        echo  $username;
                        echo ", and your password is: ";
                        echo (String)$_POST['password'];
                    }
                }
            }
            else{
                echo "you need to enter username and password to sign up";
            }
        }
    ?>



</body>
</html>