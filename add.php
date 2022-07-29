<?php
    require_once 'helpers.php';
    date_default_timezone_set('Europe/Moscow');
    $connectionDB = dbConnect();

    $sqlGetPostTypes = "SELECT * FROM post_types";
    $postTypes = getDBData($connectionDB, $sqlGetPostTypes, 'all');
    
    var_dump($_POST);
    var_dump($_FILES);
    
    $content = include_template( 'add_post.php', ['postTypes' => $postTypes]);     
    $layout = include_template( 'layout.php', ['content' => $content]);
    print($layout); 
?>