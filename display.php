<?php
    require 'database.php';
    $limit=5;

    if(isset($_POST["sort_new_button"])){
        if(isset($_POST["show_all_button"])){
            echo "Your clicked sort new and show all";
            $stmt = $mysqli->prepare("select story_id,username, story, link, added_time from posts order by added_time DESC");
        
        }else{
            $stmt = $mysqli->prepare("select story_id,username, story, link,added_time from posts order by added_time DESC LIMIT $limit;");
        }

    }
    else if(isset($_POST["sort_edited_button"])){
        if(isset($_POST["show_all_button"])){
            $stmt = $mysqli->prepare("select story_id,username, story, link, added_time from posts order by edited_time DESC");
            
        }
        else{
        $stmt = $mysqli->prepare("select story_id,username, story, link,edited_time from posts order by edited_time DESC LIMIT $limit");
        }
    }
    else{
        if(isset($_POST["show_all_button"])){
            $stmt = $mysqli->prepare("select story_id,username, story, link, added_time from posts");
        }
        else{
        $stmt = $mysqli->prepare("select story_id,username, story, link, added_time from posts LIMIT $limit");
        }
    }
    
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->execute();

    $stmt->bind_result($story_id, $username, $story, $link, $time);

    echo '<div id="stories">';
    while($stmt->fetch()){
        //display each story
        echo '<div class="story">';
        echo '<span class="post_username">';
        echo htmlspecialchars((String)$username);
        echo ':</span><span class="post_story">';
        echo htmlspecialchars((String)$story); 
        echo '</span><span class="post_link">';
        echo '<a href="'.htmlspecialchars($link).'">Link</a>';
        echo '</span><span class="post_time">';
        echo htmlspecialchars($time);
        echo '</span>';
        
        ?>
        <!--Display comment button-->
        <form action ="comment.php" method="POST" class="buttons">
        <input type="submit" value="Comment" name="comment">
        <input type="hidden" value="<?php echo $username;?>" name="each_post_user"/>
        <input type="hidden" value="<?php echo $story;?>" name="each_post_story"/>
        <input type="hidden" value="<?php echo $link;?>" name="each_post_link"/>
        <input type="hidden" value="<?php echo $story_id;?>" name="each_story_id"/>
        <!--<input type="hidden" value="story_id"-->
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
        </form>
        <?php
        //display delete an edit button only to the user who posted the story
        if(isset($_SESSION["username"])){
            if($_SESSION["username"]==$username){
                ?>
                <form action ="delete_story.php" method="POST" class="buttons">
                <input type="submit" value="Delete" name="delete">
                <input type="hidden" value="<?php echo $story_id;?>" name="story_to_delete">
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
                </form>
                <form action ="edit_story.php" method="POST" class="buttons">
                <input type="submit" value="Edit" name="edit">
                <input type="hidden" value="<?php echo $story_id;?>" name="story_to_edit">
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
                </form>
                <?php
                
            }
        }
        
        ?>

        </div>
        <?php
    }   
    $stmt->close();
    if(!isset($_POST["show_all_button"])){
    ?>
    
    <div id="show_all">
        <form action="main.php" method="POST">
        <input type="submit" value="show all stories" name="show_all_button"></button>
        </form>
        
    </div>
    <?php
    echo '</div>';
    }
    ?>