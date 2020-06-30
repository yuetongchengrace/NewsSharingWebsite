<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add comment</title>
</head>
<body>
<?php
session_start();
//insert comment
if(isset($_POST['comment_button'])){
    if(!hash_equals($_SESSION['token'], $_POST['token2'])){
        die("Request forgery detected");
    }
    if($_POST['comment_text']==null){
        header("Location: comment.php");
        exit;
    }
    require 'database.php';
    //FIEO
    $username = (String)$_SESSION["username"];
    $comment = (String)$_POST['comment_text'];
    $story_id = (int)$_POST["story_id"];
    
    //insert into table comments 
    $stmt = $mysqli->prepare("insert into comments (username, comment, story_id) values (?, ?, ?) ");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    
    $stmt->bind_param('ssi', $username, $comment, $story_id);
    
    $stmt->execute();

    $stmt->close();
    header("Location: comment.php");
}
?>
</body>
</html>