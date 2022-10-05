<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: /");
        exit();
    }
    require_once 'helpers.php';
    require_once 'functions.php';
    date_default_timezone_set('Europe/Moscow');
   
    $postTypes = getPostTypes();

    $condition = '';
    $filterPostTypeId = getQueryParam('post_type_id');

    if (!empty($filterPostTypeId) && gettype((int)$filterPostTypeId) != 'string') {
        $postsData = getPosts($filterPostTypeId);
    } else {
        $postsData = getPosts();
    }

    $title = 'readme: блог, каким он должен быть';
    $userName = $_SESSION['user'];

    $content = include_template( 'main.php', ['postsData' => $postsData, 'postTypes' => $postTypes, 'filterPostTypeId' => $filterPostTypeId] );     
    $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName]);
    print($layout); 
    

    
?>
