<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php  
    session_start();
    $story_id=$_POST['story_to_delete'];
    require 'database.php';
    $stmt = $mysqli->prepare("delete from posts where story_id=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    
    $stmt->bind_param('i', $story_id);
    
    $stmt->execute();
    
    $stmt->close();
    
    header("Location: main.php");
    exit;
    ?>
</body>
</html>