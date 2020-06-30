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
        //first check if the form to edit comment in below is submitted
        if(isset($_POST['edited_comment'])&&isset($_POST['edit'])){
            
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
                die("Request forgery detected");
            }

            require 'database.php';
            $edited_comment=(String)$_POST['edited_comment'];
            $comment_id=(int)$_POST['comment_to_edit'];

            //update the table comments according to the edited version of comment
            $stmt = $mysqli->prepare("update comments set comment='$edited_comment' where comment_id=?");
            
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            
            $stmt->bind_param('i', $comment_id);
            
            $stmt->execute();
            
            $stmt->close();
                
            header("location: edit_comment_success.php");
            exit;
        }

        require 'database.php';
        
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }

        $comment_id=(int)$_POST['comment_to_edit'];
        $query_comment=$mysqli->prepare("select comment from comments where comment_id='$comment_id'");
        if(!$query_comment){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $query_comment->execute();
        $result=$query_comment->get_result();
        $row=$result->fetch_assoc();
        $comment=(String)$row['comment'];
        
    ?>

<!--text field to edit the comment-->
    <form action="edit_comment.php" method="POST" id="edit_comment_form">
        <input type="hidden" value="<?php echo $comment_id;?>" name="comment_to_edit">
        <textarea name="edited_comment" id="edited_comment"><?php echo $comment; ?></textarea>
        <input type="submit" value="Edit" name="edit">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
    </form>
</body>
</html>