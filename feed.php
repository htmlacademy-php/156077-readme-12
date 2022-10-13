<?php

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: http://156077-readme-12/");
    exit();
}

require_once 'helpers.php';
require_once 'functions.php';
date_default_timezone_set('Europe/Moscow');
$title = 'Моя лента';
$userName = $_SESSION['user'];

$userData = getUserDataByLogin($userName);


$postTypes = getPostTypes();
$filterPostTypeId = getQueryParam('post_type_id');

$userPosts = getUserPosts($userData['id'], $filterPostTypeId);

$content = include_template( 'user-feed.php', ['postsData' => $userPosts, 'postTypes' => $postTypes, 'filterPostTypeId' => $filterPostTypeId]);     
$layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName]);
print($layout); 