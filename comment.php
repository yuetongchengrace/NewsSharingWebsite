<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
</head>
<body>
    <?php
    session_start();
    if(isset($_POST['each_story_id'])){
        $_SESSION["story_id"]=$_POST['each_story_id'];
        }
        $story_id= $_SESSION["story_id"];
    ?>
    <!--Logout button-->
    <div>
        <form method="POST" action="logout.php" class="logout">
            <?php
                echo "User: ";
                if(isset($_SESSION["username"])!=null){
                    echo htmlentities($_SESSION["username"]);
                }
                else{
                    echo "Visitor";
                }
            ?>
            <input type="submit" value="Logout"/>
        </form> 
    </div>

    <?php
        require 'database.php';
        if(isset($_POST['token'])&&isset($_SESSION['token'])){
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
            }
        }
        $query_story=$mysqli->prepare("select story, username, link from posts where story_id='$story_id'");
        $query_comments=$mysqli->prepare("select comment, username,comment_id from comments where story_id='$story_id'");
        if(!$query_story || !$query_comments){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        //query for story
        $query_story->execute();
        $result = $query_story->get_result();
        $row = $result->fetch_assoc();
        $story = $row['story'];
        $uploader = $row['username'];
        $story_link = $row['link'];
        echo '<div id="comment_story"><span class="comment_story_username">';
        echo htmlentities($uploader);
        echo ": </span>";
        echo htmlentities($story);
        echo "\t" ;
        echo '<a href="'.htmlentities($story_link).'">Link</a>';
        echo '</div>';
        $query_story->close();


        //query for comments
        $query_comments->execute();
        $query_comments->bind_result($current_comments, $commented_users,$comment_id);
        echo '<div id="comments">';
        while($query_comments->fetch()){
            //display each comment
            echo '<div>';
            echo '<span class="comment_username">';
            echo htmlspecialchars($commented_users);
            echo ': </span><span class="comment_content">';
            echo htmlspecialchars($current_comments);
            echo '</span>';
            
            //display delete button only to the user who posted the story
            if(isset($_SESSION["username"])){
                if($_SESSION["username"]==$commented_users){
                    
                    ?>
                    <form action ="delete_comment.php" method="POST" class="buttons">
                    <input type="submit" value="Delete" name="delete">
                    <input type="hidden" value="<?php echo $comment_id;?>" name="comment_to_delete">
                    </form>

                    <form action ="edit_comment.php" method="POST" class="buttons">
                    <input type="submit" value="Edit" name="edit">
                    <input type="hidden" value="<?php echo $comment_id;?>" name="comment_to_edit">
                    </form>

                    <?php
                    
                }
            }
            ?>

            </div>
            <?php
        }
        echo '</div>';
        
       

        $query_comments->close();
    ?>

    <?php
    //$story_id=$_SESSION["story_id"];
   
    if(isset($_SESSION["username"])!=null){
        $username=$_SESSION["username"];
        ?>
        <form action="addcomment.php" method="POST" id="add_comment_form">
            <div>Add new comment:</div>
            <textarea name="comment_text" id="new_comment_input"></textarea>
            <input type="hidden" name="commented_user" value="<?php echo $username;?>">
            <input type="hidden" name="token2" value="<?php echo $_SESSION['token'];?>" >
            <input type="hidden" value="<?php echo $story_id;?>" name="story_id">
            <input type="submit" value="Comment" name="comment_button" id="comment_button">
        </form>
        <?php
    }
   
    ?>
    <!--comment success-->
    
    <a href="main.php">Back to Main Page</a>
</body>
</html>