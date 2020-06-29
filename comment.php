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
    <!--Sort by most recent added post button-->
    <div class="sort" id="sort_new_comment">
        <form method="POST" class="sort_buttons" action="comment.php">
            <input type="submit" value="sort by newest comment" name="sort_new_comment_button"/>
        </form> 
    </div>
    <!--Sort by most recent edited button-->
    <div class="sort" id="sort_edited_comment">
        <form method="POST" class="sort_buttons" action="comment.php">
            <input type="submit" value="sort comments by last edit" name="sort_edited_comment_button"/>
        </form> 
    </div>
    <!--undo sort button-->
    <div class="undo_sort">
        <?php
            if(isset($_POST["sort_new_comment_button"])){
                if(isset( $_SESSION['sort_edited_comment'])){
                    unset($_SESSION['sort_edited_comment']);
                }
                $_SESSION['sort_new_comment']=true;
                
            }
            if(isset($_POST["sort_edited_comment_button"])){
                if(isset($_SESSION['sort_new_comment'])){
                    unset($_SESSION['sort_new_comment']);
                }
                $_SESSION['sort_edited_comment']=true;
            }
            if(isset($_POST["undo_sort_comment_button1"])){
                unset($_SESSION['sort_new_comment']);
            }
            else if(isset($_POST["undo_sort_comment_button2"])){
                unset($_SESSION['sort_edited_comment']);
            }
            if(isset($_SESSION['sort_new_comment'])){
                ?>
                <form method="POST" class="sort_buttons" action="comment.php">
                <input type="submit" value="undo sort" name="undo_sort_comment_button1"/>
                </form> 
                <?php
            }
            if(isset($_SESSION['sort_edited_comment'])){
                ?>
                <form method="POST" class="sort_buttons" action="comment.php">
                <input type="submit" value="undo sort" name="undo_sort_comment_button2"/>
                </form> 
                <?php
            }
        ?>
    </div>
    <?php
        require 'database.php';
        if(isset($_POST['token'])&&isset($_SESSION['token'])){
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
            }
        }
        //prepare queries for stories
        $query_story=$mysqli->prepare("select story, username, link,added_time from posts where story_id='$story_id'");

        //prepare queries for comments under different sort conditions
        if(isset($_SESSION['sort_new_comment'])){
            $query_comments=$mysqli->prepare("select comment, username,comment_id,added_time from comments where story_id='$story_id' order by added_time DESC");
        }
        else if(isset($_SESSION['sort_edited_comment'])){
            $query_comments=$mysqli->prepare("select comment, username,comment_id,edited_time from comments where story_id='$story_id' order by edited_time DESC");
        }
        else{
            $query_comments=$mysqli->prepare("select comment, username,comment_id,added_time from comments where story_id='$story_id'");
        }
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
        $story_time=$row['added_time'];
        echo '<div id="comment_story"><span class="comment_story_username">';
        echo htmlentities($uploader);
        echo ": </span>";
        echo htmlentities($story);
        echo "\t" ;
        echo '<a href="'.htmlentities($story_link).'">Link</a>';
        echo '<span class="post_time">';
        echo htmlspecialchars($story_time);
        echo '</span></div>';
        $query_story->close();


        //query for comments
        $query_comments->execute();
        $query_comments->bind_result($current_comments, $commented_users,$comment_id,$comment_time);
        echo '<div id="comments">';
        while($query_comments->fetch()){
            //display each comment
            echo '<div>';
            echo '<span class="comment_username">';
            echo htmlspecialchars($commented_users);
            echo ': </span><span class="comment_content">';
            echo htmlspecialchars($current_comments);
            echo '</span><span class="post_time">';
            echo htmlspecialchars($comment_time);
            
            
            if(isset($_SESSION["username"])){
                //display like and unlike only to registered users
                ?>
               <form action="change_likes.php" method="POST" class="buttons">
                    <input type="submit" value="Like" name="like_button">
                    <input type="hidden" value="<?php echo $comment_id;?>" name="comment_to_change_like">
                    <input type="hidden" name="token" value="<?php echo $_SESSION["token"];?>" >
                </form>

                <?php

                //display delete button and edit button only to the user who posted the comment
                if($_SESSION["username"]==$commented_users){
                    
                    ?>
                    <form action ="delete_comment.php" method="POST" class="buttons">
                    <input type="submit" value="Delete" name="delete">
                    <input type="hidden" value="<?php echo $comment_id;?>" name="comment_to_delete">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
                    </form>

                    <form action ="edit_comment.php" method="POST" class="buttons">
                    <input type="submit" value="Edit" name="edit">
                    <input type="hidden" value="<?php echo $comment_id;?>" name="comment_to_edit">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
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
    //add new comment
    if(isset($_SESSION["username"])!=null){
        $username=(String)$_SESSION["username"];
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
    
    <a href="main.php">Back to Main Page</a>
</body>
</html>