<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: /");
        exit();
    }

    require_once 'helpers.php';
    require_once 'functions.php';
    date_default_timezone_set('Europe/Moscow');

    $postId = getQueryParam('post_id');

    if ($postId != null) {
        $postData = getPostData($postId);
    } 
    $title = 'readme: пост ' . $postData['header'];
    $authorRegisterDate = new DateTime($postData['register_date']);
    $userName = $_SESSION['user'];
?>

<?php
    if ($postData) {
        $content = include_template( 'detail-post.php', ['postData' => $postData, 'registerDate' => $authorRegisterDate] );     
        $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName] );
        print($layout); 
    }  else {
        http_response_code(404);
        $content = include_template( '404.php'); 
        $layout = include_template( 'layout.php', ['content' => $content] );
        print($layout);       
    }

