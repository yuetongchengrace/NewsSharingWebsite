<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Out</title>
</head>
<body> 
    <?php
    //destroy user's session and head to login
    session_start();
    session_destroy(); 
    header("Location: login.php");
    ?>
</body>
</html>
 
