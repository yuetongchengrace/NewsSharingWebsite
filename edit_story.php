<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Story</title>
</head>
<body>
    
    <?php
        session_start();

        //first check if the form to edit story in below is submitted
        if(isset($_POST['edited_story'])&&isset($_POST['edit'])){
            
                if(!hash_equals($_SESSION['token'], $_POST['token'])){
                    die("Request forgery detected");
                }
                
                require 'database.php';
                $edited_story=(String)$_POST['edited_story'];
                $story_id=$_POST['story_to_edit'];

                //update the table posts according to the edited version of story
                $stmt = $mysqli->prepare("update posts set story='$edited_story' where story_id=?");
                
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                
                $stmt->bind_param('i', $story_id);
                
                $stmt->execute();
                
                $stmt->close();
                
            
            header("location: edit_success.php");
            exit;
        }

        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
        require 'database.php';
        $story_id=$_POST['story_to_edit'];
        $query_story=$mysqli->prepare("select story from posts where story_id='$story_id'");
        if(!$query_story){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $query_story->execute();
        $result=$query_story->get_result();
        $row=$result->fetch_assoc();
        $story=$row['story'];
        
    ?>


    <form action="edit_story.php" method="POST" id="edit_story_form">
        <input type="hidden" value="<?php echo $story_id;?>" name="story_to_edit">
        <textarea name="edited_story" id="edited_story"><?php echo $story; ?></textarea>
        <input type="submit" value="Edit" name="edit">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
    </form>
</body>
</html>