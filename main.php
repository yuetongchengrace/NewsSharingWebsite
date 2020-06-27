<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&family=Press+Start+2P&family=Quantico:ital@1&display=swap" rel="stylesheet">
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
            <button class="button" type="submit">Logout</button>
        </form> 
    </div>
    <!--New Post-->
    <div>
        <form action="main.php" method="POST" id="new_post">
            <div class="post">
            <textarea name="new_story_input" id="new_story_input"></textarea>
            <input type="hidden" name="new_story_user" value="<?php echo $_SESSION["username"];?>">
                <div class="post" id="post_button"><input type="submit" value="Post" name="post_button"></div>
                </div>
                <div id="new_link">
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
        echo '<div class="story"><span class="post_id">';
        echo htmlspecialchars($story_id);
        echo '</span><span class="post_username">';
        echo htmlspecialchars($username);
        echo '</span><span class="post_story">';
        echo htmlspecialchars($story);
        echo '</span><span class="post_link">';
        echo '<a href="'.htmlspecialchars($link).'">Link</a>';
        echo '</span>';
        ?>
        <form action ="comment.php" method="POST" >
        <input type="submit" value="Comment" name="comment">
        <input type="hidden" value="<?php echo $username;?>" name="each_post_user"/>
        <input type="hidden" value="<?php echo $story;?>" name="each_post_story"/>
        <input type="hidden" value="<?php echo $link;?>" name="each_post_link"/>
        </form>
        </div>
        <?php
    }   
    $stmt->close();
    echo '</div>';
    ?>
</body>
</html>