INSERT INTO post_types (name, alter_name, icon) VALUES 
('post-quote', 'Цитата', 'quote'), 
('post-text', 'Текст', 'text'), 
('post-photo', 'Фото', 'photo'), 
('post-link', 'Ссылка', 'link'),
('post-video', 'Видео', 'video');

INSERT INTO users (email, login, password, avatar) VALUES 
('vasilich@readmy.ru', 'Лариса', 'safe_password', 'userpic-larisa-small.jpg'), 
('petrocvich@readmy.ru', 'Владик', 'mega_safe_password', 'userpic.jpg'),
('vitusha@readmy.ru', 'Виктор', 'super_safe_password', 'userpic-mark.jpg');

INSERT INTO posts (user_id, type_id, header, post_text, post_image, post_link, quote_author, views_count) VALUES 
(9, 26, 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', '', '', 'Лариса', 3), 
(10, 27, 'Игра престолов', 'Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Если алфавит образ правилами рот родного заголовок речью собрал страну ты семантика повстречался раз большого предупреждал, обеспечивает на берегу текстов продолжил маленький своего своих за заглавных предупредила, великий грустный, страна? Которое всеми над заголовок назад страну ты переулка, заглавных пояс своего.', '', '', 'Владик', 1), 
(11, 28, 'Наконец, обработал фотки!', '','rock-medium.jpg', '', 'Виктор', 10), 
(9, 28, 'Моя мечта', '', 'coast-medium.jpg', '', 'Лариса', 4), 
(10, 29, 'Лучшие курсы', '', '', 'www.htmlacademy.ru', 'Владик', 2);

INSERT INTO comments (user_id, post_id, comment) VALUES 
(9, 12, 'Это просто праздник какой-то!!!'), 
(11, 14, 'Отличный пост, я даже всплакнул');

# получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента
SELECT posts.*, post_types.name, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id ORDER BY posts.views_count DESC;

# получить список постов для конкретного пользователя
SELECT * FROM posts WHERE user_id = 9;

# получить список комментариев для одного поста, в комментариях должен быть логин пользователя
SELECT comments.*, users.login FROM comments LEFT JOIN users ON users.id = comments.user_id WHERE post_id = 12;

# добавить лайк к посту
INSERT INTO likes (user_id, post_id) VALUES (10, 13);

# подписаться на пользователя
INSERT INTO subscribes (subscriber_id, subscribed_user_id) VALUES (9, 11);
