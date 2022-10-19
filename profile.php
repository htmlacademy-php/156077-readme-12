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
$userProfileName = getQueryParam('user');
$userName = $_SESSION['user'];
$userId = getUserDataByLogin($userName)['id'];

$title = 'readme: профиль пользователя ' . $userName ;

if (!empty($userProfileName)) {
    $userData = getUserDataByLogin($userProfileName);
} else {
    $userData = getUserDataByLogin($userName);
}

$postTypes = getPostTypes();
$userPosts = getUserPosts($userData['id']);
foreach ($userPosts as $postIndex => $post) {
    $comments = getPostComments($post['id']);
    $userPosts[$postIndex]['comments'] = $comments;
}

$subscribeUserId = getQueryParam('subscribe_user');
$subscribeNotice = '';
if (!empty($subscribeUserId)) {
    $isSubscribed = subscribeUser($userId, $subscribeUserId);
    $subscribedUserLogin = $userData['login'];
    if (!$isSubscribed) {
        $subscribeNotice = 'Вы уже подписаны на этого пользователя';
    } else {
        $subscribeNotice = 'Вы подписались на пользователя';
        // Формирование сообщение для уведомления подписчиков
        $to = $userData['email'];
        $subject = 'У вас новый попдисчик';
        $text = 'Здравствуйте,' . $userData['login'] . '. На вас подписался новый пользователь ' . $userName . '. Вот ссылка на его профиль: http://156077-readme-12/profile.php?user=' . $userName;
        sendEmail($to, $subject, $text);
    }
    $prevUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $prevUrl");
}

$subscribedUserId = getQueryParam('unactive_subscriber_id');
if (!empty($subscribedUserId) && checkSubscription($subscribedUserId, $userId)) {
    deleteSubscription($subscribedUserId, $userId);
    header("Location: /profile.php");
}

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

    $postAuthor = $userData['login'];
    header("Location: /profile.php?user=$postAuthor");
}

$subscribers = getSubscribers($userData['id']);
$subscribersData = [];

if (!empty($subscribers) && $subscribers) {
    foreach($subscribers as $subscriberIndex => $subscriber) {
        $subscribersData[] = getUserDataById($subscriber['subscribed_user_id']);
    }
}

$likes = getLikedUserPosts($userData['id']);

$content = include_template('content-profile.php', [
    'userData' => $userData, 
    'postsData' => $userPosts, 
    'postTypes' => $postTypes, 
    'subscribeNotice' => $subscribeNotice,
    'subscribersData' => $subscribersData,
    'likesData' => $likes,
    'userId' => $userId,
    'formFieldsError' => $formFieldsError
]);     
$layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName]);
print($layout); 