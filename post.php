<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: /");
        exit();
    }

    require_once 'helpers.php';
    require_once 'functions.php';
    date_default_timezone_set('Europe/Moscow');
    define('COMMENT_MIN_LENGTH', 4);
    $title = 'readme: пост ' . $postData['header'];
    $authorRegisterDate = new DateTime($postData['register_date']);
    $userName = $_SESSION['user'];

    $postId = getQueryParam('post_id');

    if (!empty($postId)) {
        $postData = getPostData($postId);
        increasePostView($postId, $postData['views_count']);
    } else {
        http_response_code(404);
        $content = include_template( '404.php'); 
        $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName]);
        print($layout);
        exit();       
    }
 
    $comments = getPostComments($postData['id']);

    $requiredFields = ['comment-text'];
    // Обработка отправки комментария
    $formFieldsError = validateAddCommentForm($_POST, $requiredFields, COMMENT_MIN_LENGTH);
    $validateErrors = getFormValidateErrors($formFieldsError);

    if (count(array_unique($validateErrors)) === 1 && array_unique($validateErrors)[0] === 'success') {
        $commentUser = (int)getUserDataByLogin($userName)['id'];
        $postId = (isset($_POST['post-id'])) ? filter_var($_POST['post-id'], FILTER_SANITIZE_NUMBER_INT) : null;
        $commentText = (isset($_POST['comment-text'])) ? filter_var($_POST['comment-text'], FILTER_SANITIZE_STRING) : null;  

        $data = [
            $commentUser,
            $postId,
            $commentText    
        ];

        $commentInsertDBresult = insertNewComment($data);
        $postAuthor = $postData['login'];
        header("Location: /profile.php?user=$postAuthor");
    }

    $content = include_template( 'detail-post.php', [
        'postData' => $postData, 
        'userName' => $userName,
        'comments' => $comments,
        'formFieldsError' => $formFieldsError
        ]);     
    $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName] );
    print($layout); 
    

