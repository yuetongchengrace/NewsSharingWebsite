<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php  
    session_start();
    $comment_id=$_POST['comment_to_delete'];
    echo $comment_id;
    require 'database.php';
    $stmt = $mysqli->prepare("delete from comments where comment_id=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    
    $stmt->bind_param('i', $comment_id);
    
    $stmt->execute();
    
    $stmt->close();
    
    header("Location: comment.php");
    exit;
    ?>
</body>
</html>