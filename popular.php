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
    $filterPostTypeId = getQueryParam('post_type_id');

    if (!empty($filterPostTypeId) && gettype((int)$filterPostTypeId) != 'string') {
        $postsData = getPosts($filterPostTypeId);
    } else {
        $postsData = getPosts();
    }

    $postsCount = count($postsData);
    $postsToShow = 3;
    $postPagesCount = ceil($postsCount / $postsToShow);
    $currentPage = (getQueryParam('pagen')) ? (int)getQueryParam('pagen') : 1;
    $nextPage = ($postPagesCount != $currentPage) ? $currentPage + 1 : -1;
    $previousPage = $currentPage - 1;
    $offset = ($currentPage - 1) * $postsToShow;

    if (!empty($filterPostTypeId)) {
        $paginationData = [
            $filterPostTypeId,
            $postsToShow,
            $offset
        ];
    } else {
        $paginationData = [
            $postsToShow,
            $offset
        ];
    }

    // Определяем нужна ли пагинация и фильтрация по типу поста
    if ($postsCount > $postsToShow && !empty($filterPostTypeId)) {     
        $postsData = getPaginationPosts($paginationData, true);
    } elseif ($postsCount > $postsToShow && empty($filterPostTypeId)){
        $postsData = getPaginationPosts($paginationData);
    }
    // Передаем данные для формирования ссылок на следующую и предыдущую страницу
    $pagesData = [
        'next' =>  $nextPage,
        'previous' => $previousPage
    ];

    $title = 'readme: блог, каким он должен быть';
    $userName = $_SESSION['user'];
    
    $content = include_template( 'content-popular.php', ['postsData' => $postsData, 'postTypes' => $postTypes, 'filterPostTypeId' => $filterPostTypeId, 'pagesData' => $pagesData] );     
    $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName]);
    print($layout); 
    

    
?>
