<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change like</title>
</head>
<body>
    <div id="change_likes">
    <?php

    session_start();
    if(isset($_POST['like_button'])){
        require 'database.php';
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
        $liked_user = (String)$_SESSION['username'];
        $liked_comment = (int)$_POST['comment_to_change_like'];
        $exist = $mysqli->prepare("select * from likes where username='$liked_user' and comment_id=$liked_comment");
        if(!$exist){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $exist->execute();
        $result = $exist->get_result();
        $row = $result->fetch_assoc();

        if($row!=null){
            echo "Oops, you can't like it twice! ";
        }

        //insert into likes according to user and the specific comment
        else{
            $stmt1 = $mysqli->prepare("insert into likes (comment_id, username) values (?, ?)");
            if(!$stmt1){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt1->bind_param('is', $liked_comment, $liked_user);
            $stmt1->execute();
            $stmt1->close();

            //fetch number of likes from table comments
            $query = $mysqli->prepare("select likes from comments where comment_id=$liked_comment");
            $query->execute();
            $result = $query->get_result();
            $row = $result->fetch_assoc();
            if($row==null){
                $n=0;
            }
            else{
                $n = $row['likes'];
            }
            $query->close();

            //update table comments (plus one)
            $stmt2 = $mysqli->prepare("update comments set likes=$n+1 where comment_id=$liked_comment");
            if(!$stmt2){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt2->execute();
            $stmt2->close();
            echo 'You have liked a comment!';
        }
        
    }
        


    elseif(isset($_POST['unlike_button'])){
        require 'database.php';

        //delete from likes according to user and the specific comment
        $unliked_user = (String)$_SESSION['username'];
        $unliked_comment = (int)$_POST['comment_to_change_like'];

        $stmt1 = $mysqli->prepare("delete from likes where username='$unliked_user' AND comment_id=$unliked_comment");
        if(!$stmt1){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt1->execute();
        $stmt1->close();

        //fetch number of likes from table comments
        $query = $mysqli->prepare("select likes from comments where comment_id=$unliked_comment");
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        if($row==null){
            $n=0;
        }
        else{
            $n = $row['likes'];
        }
        $query->close();

        //update table comments (minus one)
        $stmt2 = $mysqli->prepare("update comments set likes=$n-1 where comment_id=$unliked_comment");
        if(!$stmt2){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt2->execute();
        $stmt2->close();
        echo "You have successfully unliked a comment! ";

    }

    echo '<a href="comment.php">Go Back to Comment Page</a>';


    ?></div>
</body>
</html>

