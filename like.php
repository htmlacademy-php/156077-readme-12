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
$prevUrl = $_SERVER['HTTP_REFERER'];

$postId = getQueryParam('post_id');
if (!empty($postId) && getPostData($postId)) {
    if (addPostLike($postId, $userId)) {      
        header("Location: $prevUrl");
    } else {
        header("Location: $prevUrl&liked=true");
    }
} 

