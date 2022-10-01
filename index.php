<?php
    require_once 'helpers.php';
    require_once 'functions.php';
    date_default_timezone_set('Europe/Moscow');

    $postTypes = getPostTypes();

    $condition = '';
    $filterPostTypeId = getQueryParam('post_type_id');

    if ($filterPostTypeId != NULL && gettype($filterPostTypeId) != 'string') {
        $postsData = getPosts($filterPostTypeId);
    } else {
        $postsData = getPosts();
    }

    $title = 'readme: блог, каким он должен быть';
    $isAuth = rand(0, 1);
    $userName = 'Виктор';

    $content = include_template( 'main.php', ['postsData' => $postsData, 'postTypes' => $postTypes, 'filterPostTypeId' => $filterPostTypeId] );     
    $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName, 'isAuth' => $isAuth] );
    print($layout); 
?>
