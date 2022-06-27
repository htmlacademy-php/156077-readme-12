<?php
    require_once 'helpers.php';

    date_default_timezone_set('Europe/Moscow');
    $connectionDB = mysqli_connect("localhost", "user", "Winserus89","readme"); 
    if ($connectionDB == false) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }

    $sqlGetPostTypes = "SELECT * FROM post_types";
    $postTypes = getDBData($connectionDB, $sqlGetPostTypes, 'all');

    $sqlGetPosts = "SELECT posts.*, post_types.name as type_name, users.avatar FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id ORDER BY posts.views_count DESC;";
    $postsData = getDBData($connectionDB, $sqlGetPosts, 'all');

    $title = 'readme: блог, каким он должен быть';
    $isAuth = rand(0, 1);
    $userName = 'Виктор'; // укажите здесь ваше имя

?>

<?php
    if ($postsData && $postTypes) {
        $content = include_template( 'main.php', ['postsData' => $postsData, 'postTypes' => $postTypes] );
        $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName, 'isAuth' => $isAuth] );
        print($layout); 
    }  
?>
