<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comment</title>
</head>
<body>
    
    <?php
        session_start();

        if(isset($_POST['edited_comment'])){
            
            if(isset($_POST['edit'])){
                require 'database.php';
                $edited_comment=$_POST['edited_comment'];
                $comment_id=$_POST['comment_to_edit'];
                
                $stmt = $mysqli->prepare("update comments set comment='$edited_comment' where comment_id=?");
                
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                
                $stmt->bind_param('i', $comment_id);
                
                $stmt->execute();
                
                $stmt->close();
                
            }
            header("location: edit_success.php");
            exit;
        }

        require 'database.php';
        $comment_id=$_POST['comment_to_edit'];
        $query_comment=$mysqli->prepare("select comment from comments where comment_id='$comment_id'");
        if(!$query_comment){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $query_comment->execute();
        $result=$query_comment->get_result();
        $row=$result->fetch_assoc();
        $comment=$row['comment'];
        
    ?>


    <form action="edit_comment.php" method="POST">
        <input type="hidden" value="<?php echo $comment_id;?>" name="comment_to_edit">
        <input type="text" value="<?php echo $comment;?>" name="edited_comment">
        <input type="submit" value="Edit" name="edit">
    </form>
</body>
</html>