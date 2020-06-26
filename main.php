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

    echo "<ul>\n";
    while($stmt->fetch()){
        printf("\t<li>%s %s %s</li>\n",
            htmlspecialchars($username),
            htmlspecialchars($story),
            '<a href="'.htmlspecialchars($link).'">Link</a>'
        );
    }
    echo "</ul>\n";

    $stmt->close();
    ?>

</body>
</html>