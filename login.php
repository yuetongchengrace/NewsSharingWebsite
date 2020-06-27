<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
</head>
<body>
    <form action="login.php" method="POST" >
        <span class="big">Username:</span>
        <input type="text" name="username" id="username"/>
        <span class="big">Password:</span>
        <input type="password" name="password" id="password" />
        <input type="submit" value="Login" name="submit" class="login_page_buttons"/>
    </form>

    <?php
        if(isset($_POST['submit'])&&$_POST['username']!=null){
            $username=(String)$_POST['username'];
            $input_password=(String)$_POST['password'];
            require 'database.php';

            $stmt = $mysqli->prepare("select * from users where username='$username'");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
    
            $stmt->execute();
            $result= $stmt->get_result();
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            if(password_verify($input_password, $hashed_password)){
                session_start();
                $_SESSION["username"]=$username;
                header("Location: main.php");
                exit;
            }
    
            $stmt->close();
        }
    ?>
</body>
</html>
