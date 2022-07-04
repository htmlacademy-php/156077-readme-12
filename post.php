<?php
    require_once 'helpers.php';

    date_default_timezone_set('Europe/Moscow');
    $connectionDB = dbConnect();

    $postId = getQueryParam('post_id');

    if ($postId != NULL) {
        $sqlGetPostData = "SELECT posts.*, post_types.name as type_name, users.avatar, users.register_date FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE posts.id = $postId";
        $postData = getDBData($connectionDB, $sqlGetPostData, 'single');
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
