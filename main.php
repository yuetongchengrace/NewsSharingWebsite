<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Articles</title>
</head>
<body> 
    <?php
    session_start();
    ?>
    <!--Logout button-->
    <div>
        <form method="POST" action="logout.php" class="logout">
            <?php
                echo "User: ";
                if(isset($_SESSION["username"])!=null){
                    echo htmlentities($_SESSION["username"]);
                    echo '<input type="submit" value="Logout"/>';
                }
                else{
                    echo "Visitor";
                    echo '<input type="submit" value="Login as user"/>';
                }
            ?>
            
        </form> 
    </div>
    <!--Sort by most recent added post button-->
    <div class="sort" id="sort_new">
        <form method="POST" class="sort_buttons" action="main.php">
            <input type="submit" value="sort by newest post" name="sort_new_button"/>
        </form> 
    </div>
    <!--Sort by most recent edited button-->
    <div class="sort" id="sort_edited">
        <form method="POST" class="sort_buttons" action="main.php">
            <input type="submit" value="sort by last edit" name="sort_edited_button"/>
        </form> 
    </div>
    <!--undo sort button-->
    <div id="undo_sort">
        <?php
            if(isset($_POST["sort_new_button"])){
                if(isset( $_SESSION['sort_edited'])){
                    unset($_SESSION['sort_edited']);
                }
                $_SESSION['sort_new']=true;
                
            }
            if(isset($_POST["sort_edited_button"])){
                if(isset($_SESSION['sort_new'])){
                    unset($_SESSION['sort_new']);
                }
                $_SESSION['sort_edited']=true;
            }
            if(isset($_POST["undo_sort_button1"])){
                unset($_SESSION['sort_new']);
            }
            else if(isset($_POST["undo_sort_button2"])){
                unset($_SESSION['sort_edited']);
            }
            if(isset($_SESSION['sort_new'])){
                ?>
                <form method="POST" class="sort_buttons" action="main.php">
                <input type="submit" value="undo sort" name="undo_sort_button1"/>
                </form> 
                <?php
            }
            if(isset($_SESSION['sort_edited'])){
                ?>
                <form method="POST" class="sort_buttons" action="main.php">
                <input type="submit" value="undo sort" name="undo_sort_button2"/>
                </form> 
                <?php
            }
        ?>
    </div>
    <!--New Post-->
    <div>
        <form action="uploader.php" method="POST" id="new_post">
            <div class="post">
                <div>Add new post:</div>
                <textarea name="new_story_input" id="new_story_input"></textarea>
                <input type="hidden" name="new_story_user" value="<?php echo $_SESSION["username"];?>"/>
                <div class="post" id="post_button"><input type="submit" value="Post" name="post_button"></div>
                <div id="new_link">Add link:
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
                <input type="text" name="new_link_input" id="new_link_input">
                <!--Add new post to database-->

                </div>
            </div>
            
        </form>
    </div>
    <!--Display all stories from database-->
    <?php
    require 'database.php';
    $limit=5;
    
    if(isset($_SESSION['sort_new'])){
        if(isset($_POST["show_all_button"])){
            $stmt = $mysqli->prepare("select story_id,username, story, link, added_time from posts order by added_time DESC");
        
        }else{
            $stmt = $mysqli->prepare("select story_id,username, story, link,added_time from posts order by added_time DESC LIMIT $limit;");
        }

    }
    else if(isset($_SESSION['sort_edited'])){
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
    </div>
</body>
</html>