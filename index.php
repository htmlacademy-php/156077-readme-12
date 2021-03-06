<?php
    require_once 'helpers.php';

    date_default_timezone_set('Europe/Moscow');
    $connectionDB = dbConnect();

    $sqlGetPostTypes = "SELECT * FROM post_types";
    $postTypes = getDBData($connectionDB, $sqlGetPostTypes, 'all');

    $condition = '';
    $filterPostTypeId = getQueryParam('post_type_id');

    if ($filterPostTypeId != NULL && gettype($filterPostTypeId) != 'string') {
        $condition = 'WHERE posts.type_id =' . $filterPostTypeId;
    }

    $sqlGetPosts = "SELECT posts.*, post_types.name as type_name, users.avatar FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id $condition ORDER BY posts.views_count DESC";
    $postsData = getDBData($connectionDB, $sqlGetPosts, 'all');

    $title = 'readme: блог, каким он должен быть';
    $isAuth = rand(0, 1);
    $userName = 'Виктор';

    $content = include_template( 'main.php', ['postsData' => $postsData, 'postTypes' => $postTypes, 'filterPostTypeId' => $filterPostTypeId] );     
    $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName, 'isAuth' => $isAuth] );
    print($layout); 
?>
