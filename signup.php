<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
</head>
<body>
    <form action="signup.php" method="POST" >
        <span class="big">Username:</span><br>
        <input type="text" name="username" id="username"/> <br><br>
        <span class="big">Password:</span><br>
        <input type="password" name="password" id="password" /> <br><br>
        <span class="big">Confirm Password:</span><br>
        <input type="password" name="confirm_password" id="password" />
        <input type="submit" value="Signup" name="submit" class="login_page_buttons"/>
    </form>


    <?php
        if(isset($_POST['submit'])&&$_POST['username']!=null&&$_POST['password']!=null){
            require 'database.php';
            if($_POST['password']!=$_POST['confirm_password']){
                echo "Password Does Not Match";
            }
            else{
                $username = (String)$_POST['username'];
                $hashed_password = password_hash((String)$_POST['password'], PASSWORD_DEFAULT);

                $stmt = $mysqli->prepare("insert into users (username, password) values (?, ?)");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }

                $stmt->bind_param('ss', $username, $hashed_password);
                
                $stmt->execute();

                $stmt->close();
            }
        }
    ?>



</body>
</html>