<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}
require_once 'helpers.php';
require_once 'functions.php';
date_default_timezone_set('Europe/Moscow');

$postTypes = getPostTypes();
$searchQuery = filter_var($_GET['search-phrase'], FILTER_SANITIZE_STRING);
$previousPage = parse_url($_SERVER['HTTP_REFERER'])['path'];

$postsData = getSearchPosts($searchQuery);

$title = 'readme: поиск по блогу';
$userName = $_SESSION['user'];

if ($postsData) {
    $content = include_template( 'search-content.php', ['postsData' => $postsData, 'postTypes' => $postTypes, 'searchQuery' => $searchQuery] );    
} else {
    $content = include_template( 'search-no-results.php', ['searchQuery' => $searchQuery, 'previousPage' => $previousPage] );    
}
 
$layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName]);
print($layout); 