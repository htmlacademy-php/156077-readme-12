<?php
    require_once 'helpers.php';
    require_once 'functions.php';
    date_default_timezone_set('Europe/Moscow');

    $postId = getQueryParam('post_id');

    if ($postId != NULL) {
        $postData = getPostData($postId);
    } 

    $title = 'readme: пост ' . $postData['header'];
    $authorRegisterDate = new DateTime($postData['register_date']);
    $isAuth = rand(0, 1);
    $userName = 'Виктор';

?>

<?php
    if ($postData) {
        $content = include_template( 'detail-post.php', ['postData' => $postData, 'registerDate' => $authorRegisterDate] );     
        $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName, 'isAuth' => $isAuth] );
        print($layout); 

    }  else {

        http_response_code(404);
        $content = include_template( '404.php'); 
        $layout = include_template( 'layout.php', ['content' => $content] );
        print($layout); 
        
    }
?>
