<?php
require_once 'helpers.php';

$title = 'readme: блог, каким он должен быть';
$isAuth = rand(0, 1);
$userName = 'Виктор'; // укажите здесь ваше имя
$postsData = [
    [
        'header' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'user-name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
    ],
    [
        'header' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Если алфавит образ правилами рот родного заголовок речью собрал страну ты семантика повстречался раз большого предупреждал, обеспечивает на берегу текстов продолжил маленький своего своих за заглавных предупредила, великий грустный, страна? Которое всеми над заголовок назад страну ты переулка, заглавных пояс своего.',
        'user-name' => 'Владик',
        'avatar' => 'userpic.jpg',
    ],
    [
        'header' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'user-name' => 'Виктор',
        'avatar' => 'userpic-mark.jpg',
    ],
    [
        'header' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'user-name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
    ],
    [
        'header' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'user-name' => 'Владик',
        'avatar' => 'userpic.jpg',
    ],
];

?>

<?php
    $content = include_template( 'main.php', ['postsData' => $postsData] );
    $layout = include_template( 'layout.php', ['content' => $content, 'title' => $title, 'userName' => $userName, 'isAuth' => $isAuth] );
    print($layout); 
?>
