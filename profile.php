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

if (!empty($subscribeUserId)) {
    $isSubscribed = subscribeUser($userId, $subscribeUserId);
    if (!$isSubscribed) {
        $subscribeNotice = 'Вы уже подписаны на этого пользователя';
    }
}

$content = include_template( 'content-profile.php', ['userData' => $userData, 'postsData' => $userPosts, 'postTypes' => $postTypes, 'subscribeNotice' => $subscribeNotice]);     
$layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName]);
print($layout); 