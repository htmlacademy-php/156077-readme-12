<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: /");
        exit();
    }
    require_once 'helpers.php';
    require_once 'functions.php';
    date_default_timezone_set('Europe/Moscow');
    define('POSTS_TO_SHOW', 6);
    $postTypes = getPostTypes();
    $filterPostTypeId = getQueryParam('post_type_id');
    $sortType = getQueryParam('sort');
    
    $postsData = getPosts($filterPostTypeId, $sortType);
    $postsCount = count($postsData);

    $postPagesCount = ceil($postsCount / POSTS_TO_SHOW);
    $currentPage = (getQueryParam('pagen')) ? (int)getQueryParam('pagen') : 1;
    $nextPage = ($postPagesCount != $currentPage) ? $currentPage + 1 : -1;
    $previousPage = $currentPage - 1;
    $offset = ($currentPage - 1) * POSTS_TO_SHOW;

    if (!empty($filterPostTypeId)) {
        $paginationData = [
            $filterPostTypeId,
            POSTS_TO_SHOW,
            $offset
        ];
    } else {
        $paginationData = [
            POSTS_TO_SHOW,
            $offset
        ];
    }

    // Определяем нужна ли пагинация и фильтрация по типу поста
    if ($postsCount > POSTS_TO_SHOW && !empty($filterPostTypeId)) {     
        $postsData = getPaginationPosts($paginationData, $sortType, true);
    } elseif ($postsCount > POSTS_TO_SHOW && empty($filterPostTypeId)){
        $postsData = getPaginationPosts($paginationData, $sortType);
    }
    // Передаем данные для формирования ссылок на следующую и предыдущую страницу
    $pagesData = [
        'next' =>  $nextPage,
        'previous' => $previousPage
    ];

    $title = 'readme: блог, каким он должен быть';
    $userName = $_SESSION['user'];
    
    $content = include_template( 'content-popular.php', [
        'postsData' => $postsData, 
        'postTypes' => $postTypes, 
        'filterPostTypeId' => $filterPostTypeId, 
        'pagesData' => $pagesData,
        'sortType' => $sortType
        ] );     
    $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName]);
    print($layout); 
