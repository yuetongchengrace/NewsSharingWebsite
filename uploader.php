<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploader</title>
</head>
<body>
    <div id="uploader">
    <?php 
    session_start();
    if(!hash_equals($_SESSION['token'], $_POST['token2'])){
        die("Request forgery detected");
    }
    if(isset($_POST['post_button'])){
        if(isset($_SESSION["username"])==null){
            echo "You need to be logged in to post anything!<br>";
            echo '<a href="login.php">Log in now</a><br>';
            echo '<a href="main.php">Continue visiting as guest</a>';
        }
        else if($_POST['new_story_input']==null){
            echo "Please enter something for your story";
            echo '<a href="main.php">Back to post page</a>';
        }
        else{           
            $current_user=(String)$_SESSION["username"];  
            require 'database.php';
            $new_user=(String)$_POST['new_story_user'];
            $new_story=(String)$_POST['new_story_input'];
            $new_link=(String)$_POST['new_link_input'];
            $stmt = $mysqli->prepare("insert into posts (username, story, link) values (?, ?, ?)");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }

            $stmt->bind_param('sss', $new_user, $new_story, $new_link);

            $stmt->execute();

            $stmt->close();
            header("Location:main.php");
        }
    }
    ?>
     </div>
</body>
</html>