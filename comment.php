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
    $username=$_SESSION["username"];
    //$story_id=$_SESSION["story_id"];
    $story_id=$_POST['story_id'];
    ?>
    
    <form action="comment.php" method="POST">
        <input type="text" name="comment_text" id="new_comment_input">
        <input type="hidden" name="commented_user" value="<?php echo $username;?>">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" >
        <input type="hidden" value="<?php echo $story_id;?>" name="story_id">
        <input type="submit" value="Comment" name="comment_button">
    </form>

    <?php
        require 'database.php';
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
        $query_story=$mysqli->prepare("select story, username, link from posts where story_id='$story_id'");
        $query_comments=$mysqli->prepare("select comment, username from comments where story_id='$story_id'");
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
        echo htmlentities($story);
        echo "\t" ;
        echo htmlentities($uploader);
        echo "\t" ;
        echo htmlentities($story_link);
        echo "<ul>\n";
        $query_story->close();


        //query for comments
        $query_comments->execute();
        $query_comments->bind_result($current_comments, $commented_users);
        while($query_comments->fetch()){
            printf("\t<li>%s %s</li>\n",
                htmlspecialchars($current_comments),
                htmlspecialchars($commented_users)
            );
        }
        echo "</ul>\n";

        $query_comments->close();

        //insert comment
        if(isset($_POST['comment_button'])){
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
                die("Request forgery detected");
            }
            require 'database.php';
            $username = (String)$_SESSION["username"];
            $comment = (String)$_POST['comment_text'];
            $story_id = $_POST["story_id"];
            echo $story_id;

            $stmt = $mysqli->prepare("insert into comments (username, comment, story_id) values (?, ?, ?) ");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            
            $stmt->bind_param('ssi', $username, $comment, $story_id);
            
            echo htmlspecialchars($story_id);
            $stmt->execute();

            $stmt->close();
        }
    ?>

    <!--comment success-->
    
    <a href="main.php">Back to Main Page</a>
</body>
</html>