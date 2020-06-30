<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete comment</title>
</head>
<body>
<!--delete a comment from database-->
    <?php 
    session_start();
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        die("Request forgery detected");
    }
    $comment_id=$_POST['comment_to_delete'];
    echo $comment_id;
    require 'database.php';
    //prepare to delete from table likes
    $stmt = $mysqli->prepare("delete from likes where comment_id=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    
    $stmt->bind_param('i', $comment_id);
    
    $stmt->execute();
    
    $stmt->close();
    //prepare to delete from table comments
    $stmt2 = $mysqli->prepare("delete from comments where comment_id=?");
    if(!$stmt2){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    
    $stmt2->bind_param('i', $comment_id);
    
    $stmt2->execute();
    
    $stmt2->close();
    
    header("Location: comment.php");
    exit;
    ?>
</body>
</html>