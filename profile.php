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
    //header("Location: /profile.php?user=$subscribedUserLogin");
}

$subscribers = getSubscribers($userData['id']);
$subscribersData = [];

if (!empty($subscribers) && $subscribers) {
    foreach($subscribers as $subscriberIndex => $subscriber) {
        $subscribersData[] = getUserDataById($subscriber['subscribed_user_id']);
    }
}

$subscribedUserId = getQueryParam('unactive_subscriber_id');
if (!empty($subscribedUserId) && checkSubscription($subscribedUserId, $userId)) {
    deleteSubscription($subscribedUserId, $userId);
    header("Location: /profile.php");
}

$content = include_template('content-profile.php', [
    'userData' => $userData, 
    'postsData' => $userPosts, 
    'postTypes' => $postTypes, 
    'subscribeNotice' => $subscribeNotice,
    'subscribersData' => $subscribersData,
    'userId' => $userId
]);     
$layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName]);
print($layout); 