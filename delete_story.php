<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Story</title>
</head>
<body>
<!--delete a story from database-->
    <?php  
    session_start();
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        die("Request forgery detected");
    }
    $story_id=(int)$_POST['story_to_delete'];
    require 'database.php';
    //first select all comments that are linked to the story
    $stmt=$mysqli->prepare("select comment_id from comments where story_id=$story_id");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();

    $stmt->bind_result($comment_id);

    $comment_ids = array();
    while($stmt->fetch()){
        array_push($comment_ids,(int)$comment_id);
    }
    $stmt->close();

    //delete comment from table likes
    foreach($comment_ids as $comment){
        
        $stmt3 = $mysqli->prepare("delete from likes where comment_id=?");
        if(!$stmt3){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        
        $stmt3->bind_param('i', $comment);
        
        $stmt3->execute();
        
        $stmt3->close();
    }
    
    //delete comment from table comments
    $stmt1 = $mysqli->prepare("delete from comments where story_id=?");
    if(!$stmt1){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt1->bind_param('i', $story_id);
    $stmt1->execute();
    $stmt1->close();

    //delete the story from table posts
    $stmt2 = $mysqli->prepare("delete from posts where story_id=?");
    if(!$stmt2){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->bind_param('i', $story_id);
    $stmt2->execute();
    $stmt2->close();
    
    header("Location: main.php");
    exit;

    ?>
</body>
</html>