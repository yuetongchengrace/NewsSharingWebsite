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
    require 'database.php';
    $stmt = $mysqli->prepare("select username, story, link from posts");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->execute();

    $stmt->bind_result($username, $story, $link);

    
    while($stmt->fetch()){
        echo '<div><span>';
        echo htmlspecialchars($username);
        echo '</span><span>';
        echo htmlspecialchars($story);
        echo '</span><span>';
        echo '<a href="'.htmlspecialchars($link).'">Link</a>';
        echo '</span></div>';
    }
    $stmt->close();
    ?>

    <div>
        <form>

        </form>
    </div>
</body>
</html>