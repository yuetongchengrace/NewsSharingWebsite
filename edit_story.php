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

        if(isset($_POST['edited_story'])){
            
            if(isset($_POST['edit'])){
                require 'database.php';
                $edited_story=$_POST['edited_story'];
                $story_id=$_POST['story_to_edit'];
                
                $stmt = $mysqli->prepare("update posts set story='$edited_story' where story_id=?");
                
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                
                $stmt->bind_param('i', $story_id);
                
                $stmt->execute();
                
                $stmt->close();
                
            }
            header("location: edit_success.php");
            exit;
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


    <form action="edit_story.php" method="POST">
        <input type="hidden" value="<?php echo $story_id;?>" name="story_to_edit">
        <input type="text" value="<?php echo $story;?>" name="edited_story">
        <input type="submit" value="Edit" name="edit">
    </form>
</body>
</html>