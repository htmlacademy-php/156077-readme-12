<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

require_once 'helpers.php';
require_once 'functions.php';
date_default_timezone_set('Europe/Moscow');

$userName = $_SESSION['user'];
$userId = getUserDataByLogin($userName)['id'];
$postId = getQueryParam('post_id');

if (!empty($postId) && getPostData($postId)) {

    $repostCount = getPostData($postId)['repost_count'];
    increaseReposts($postId, $repostCount);

    if (repostUserPost($postId, $userId)) {      
        header("Location: /profile.php");  
    }
} 

