<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Files</title>
</head>
<body> 
    <?php
    session_start();
    ?>
    <!--Logout button-->
    <div>
        <form method="POST" action="logout.php" id="logout">
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
    <!--New Post-->
    <div>
        <form action="main.php" method="POST" id="new_post">
            <div class="post">
                <div>Add new post:</div>
                <textarea name="new_story_input" id="new_story_input"></textarea>
                <input type="hidden" name="new_story_user" value="<?php echo $_SESSION["username"];?>"/>
                <div class="post" id="post_button"><input type="submit" value="Post" name="post_button"></div>
                <div id="new_link">Add link:
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>"/>
                <input type="text" name="new_link_input" id="new_link_input">
                <!--Add new post to database-->
                <?php
                
                if(isset($_POST['post_button'])){
                    if(isset($_SESSION["username"])==null){
                        echo "You need to be logged in to post anything!<br>";
                        echo '<a href="login.php">Log in now</a>';
                    }
                    else if($_POST['new_story_input']==null){
                        echo "Please enter something for your story";
                    }
                    else{
                        $current_user=$_SESSION["username"];
                        echo "nice you are logged in!";
                        require 'database.php';
                        $new_user=$_POST['new_story_user'];
                        $new_story=$_POST['new_story_input'];
                        $new_link=$_POST['new_link_input'];
                        $stmt = $mysqli->prepare("insert into posts (username, story, link) values (?, ?, ?)");
                        if(!$stmt){
                            printf("Query Prep Failed: %s\n", $mysqli->error);
                            exit;
                        }

                        $stmt->bind_param('sss', $new_user, $new_story, $new_link);

                        $stmt->execute();

                        $stmt->close();
                        
                        header("Location: main.php");

                    }
                }
                ?>
                </div>
            </div>
            
        </form>
    </div>

    

    <!--Display all stories from database-->
    <?php
    require 'database.php';
    $stmt = $mysqli->prepare("select story_id,username, story, link from posts");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->execute();

    $stmt->bind_result($story_id,$username, $story, $link);

    echo '<div id="stories">';
    while($stmt->fetch()){
        //display each story
        echo '<div class="story">';
        echo '<span class="post_username">';
        echo htmlspecialchars($username);
        echo ':</span><span class="post_story">';
        echo htmlspecialchars($story);
        echo '</span><span class="post_link">';
        echo '<a href="'.htmlspecialchars($link).'">Link</a>';
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
        //display delete button only to the user who posted the story
        if(isset($_SESSION["username"])){
            if($_SESSION["username"]==$username){
                //$_SESSION["story_id"]=$story_id;
                ?>
                <form action ="delete_story.php" method="POST" class="buttons">
                <input type="submit" value="Delete" name="delete">
                <input type="hidden" value="<?php echo $story_id;?>" name="story_to_delete">
                </form>
                <?php
                
            }
        }
        
        ?>

        </div>
        <?php
    }   
    $stmt->close();
    echo '</div>';
    ?>
</body>
</html>