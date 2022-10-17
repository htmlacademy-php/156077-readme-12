<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

require_once 'helpers.php';
require_once 'functions.php';
date_default_timezone_set('Europe/Moscow');

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
$formFieldsError = [];

foreach ($_POST as $fieldName => $fieldValue) {
    //валидация на заполненность
    if (in_array($fieldName, $requiredFields)) {
        if (validateEmptyFilled($fieldName)) {
            $formFieldsError['comment-form'][$fieldName] = 'success';
        } else {
            $formFieldsError['comment-form'][$fieldName] = 'Это поле должно быть заполнено';
        }
        
        if ($formFieldsError['comment-form'][$fieldName] == 'success') {

            if (!checkLength($fieldValue, 4)) {
                $formFieldsError['comment-form'][$fieldName] = 'success';
            } else {
                $formFieldsError['comment-form'][$fieldName] = 'Длина комментария должна быть больше четырех символов';
            }         
        } 

        if ($fieldName == 'post-id' && getPost($fieldValue)) {
            $formFieldsError['comment-form'][$fieldName] = 'success';
        } else {
            $formFieldsError['comment-form'][$fieldName] = 'Пост не доступен';
        }
    } 
}

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