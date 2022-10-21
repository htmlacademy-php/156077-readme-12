<?php
declare(strict_types = 1);

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
require 'vendor/autoload.php';

/**
 * Подключает к базе данных
 * @return object соединение с БД, либо выходит из скрипта
 */
function dbConnection() : mysqli {
    $connectionMysql = new mysqli("localhost", "user", "Winserus89","readme"); 
    if ($connectionMysql == false) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }

    return $connectionMysql;
}

/**
 * Формироует строку типов переменных для передачи в подготовленный запрос
 * @param array $data Массив переменных
 * @return string строка типов переменных для подготовленного запроса
 */
function getVarTypes(array $data) : string {
    if (!$data) {
        return false;
    } else {
        
        $types = '';

        foreach ($data as $value) {
            if (is_int($value)) {
                $type = 'i';
            } elseif (is_string($value)) {
                $type = 's';
            } elseif (is_double($value)) {
                $type = 'd';
            }   
            
            $types .= $type;
        }
    }  

    return $types;
}

/**
 * Получает записи БД из массива переменных с подготовкой sql выражения или без
 * @param string $sql sql запрос
 * @param array $data простой массив переменных
 * @param string $resultsType формат возвращаемых данных
 * @return mixed массив данных или null
 */

function getDBDataFromArray(string $sql, array $data = null, string $resultsType = 'single') : ?array {
    $mysqli = dbConnection();
    
    if (!$data) {
        $result = $mysqli->query($sql);
    } elseif (!is_array($data)) {
        return null;
    } else {     
        $varTypes = getVarTypes($data);      
        $stmt = $mysqli->prepare($sql);
        
        $stmt->bind_param($varTypes, ...$data);
        $stmt->execute();
        $result = $stmt->get_result();    
        
        $mysqli->close();            
    }
    
    if ($resultsType == 'single') {     
        $result = $result->fetch_assoc();
    } elseif ($resultsType == 'all') {
        $result = $result->fetch_all(MYSQLI_ASSOC);
    }

    return $result;
    
}

/**
 * Удаляет данные из БД подготовкой sql выражения
 * @param string $sql sql запрос
 * @param array $data простой массив переменных
 * @return bool результат stmp или false
 */
function deleteDBDataFromArray(string $sql, array $data) : bool {
    $mysqli = dbConnection();
    
    if (!$data) {
        $mysqli->close();
        return false;
    } else {     
        $varTypes = getVarTypes($data);      
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param($varTypes, ...$data);
            $stmt->execute();
        }   
        $mysqli->close();
        return true;  
    } 
}

/**
 * Обновляет данные в таблицах
 * @param string $sql sql запрос
 * @param array $data простой массив переменных
 * @return bool результат stmp или false
 */
function updateDBDataFromArray(string $sql, array $data) : bool {
    $mysqli = dbConnection();
    
    if (!$data) {
        return false;
    } else {     
        $varTypes = getVarTypes($data);      
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param($varTypes, ...$data);
            $stmt->execute(); 
        }   

        $mysqli->close();
        return true;  
    } 
}

/**
 * Делает запись в БД из массива переменных
 * @param string $sql sql запрос
 * @param array $data простой массив переменных
 * @return int id добавленной записи или null
 */

function insertDBDataFromArray(string $sql, array $data) : ?int {
    $mysqli = dbConnection();

    if (!$data) {
        $mysqli->query($sql);
    } else {
        $varTypes = getVarTypes($data);
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($varTypes, ...$data);
            $stmt->execute();       
        }   
    }

    $result = $mysqli->insert_id;

    $mysqli->close();
    return $result; 
}

/**
 * Записывает в базу данных новый лайк поста
 * @param int $postId id поста
 * @param int $userId id юзера
 * @return {mixed} id добавленной записи
 */
function addPostLike(int $postId, int $userId) : int {
    $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
    return insertDBDataFromArray($sql, [$userId, $postId]);
}

/**
 * Записывает в базу данных новый пост
 * @param array $data простой массив переменных с данными поста
 * @return int id добавленной записи 
 */
function insertNewPost(array $data) : int {
    $sql = "INSERT INTO posts (user_id, type_id, header, post_text, quote_author, post_image, post_video, post_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    return insertDBDataFromArray($sql, $data);
}

/**
 * Записывает в базу данных новый коммент
 * @param array $data простой массив переменных с данными комментария
 * @return int id добавленной записи или false
 */
function insertNewComment(array $data) : int {
    $sql = "INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)";
    return insertDBDataFromArray($sql, $data);
}

/**
 * Записывает в базу данных нового юзера
 * @param array $data простой массив переменных с данными комментария
 * @return int id добавленной записи или false
 */
function insertNewUser(array $data) : int {
    $sql = "INSERT INTO users (email, login, password, avatar) VALUES (?, ?, ?, ?)";
    return insertDBDataFromArray($sql, $data);
}

/**
 * Записывает в базу теги поста
 * @param array $data простой массив переменных с данными тегов
 * @param string $insertTable флаг в какую таблицу нужно сделать запись тэга - со списком тегов или с соответсвием тега посту
 * @return int id добавленной записи или false
 */
function insertPostTags(array $data, string $insertTable) : int {
    $sqlTagsPostsPrepare = "INSERT INTO hashtags_posts (post_id, hashtag_id) VALUES (?, ?)";
    $sqlTagsPrepare = "INSERT INTO hashtags (hashtag) VALUES (?)";

    if ($insertTable == 'hashtags') {
        return insertDBDataFromArray($sqlTagsPrepare, $data);
    } elseif ($insertTable == 'hashtags_post') {
        return insertDBDataFromArray($sqlTagsPostsPrepare, $data);
    }
      
}

/**
 * Произовдит подписку на пользователя
 * @param int $subscriberId id подписывающегося юзера
 * @param int $subscribedUserId int флаг в какую таблицу нужно сделать запись тэга - со списком тегов или с соответсвием тега посту
 * @return int id добавленной записи или false
 */
function subscribeUser(int $subscriberId, int $subscribedUserId) : int {
    $sql = "INSERT INTO subscribes (subscriber_id, subscribed_user_id) VALUES (?, ?)";
    return insertDBDataFromArray($sql, [$subscriberId, $subscribedUserId]);
}

/**
 * Получает id типа поста по его имени
 * @param string $postTypeName название типа поста
 * @return mixed id типа поста или null
 */

function getPostTypeIdByName(string $postTypeName) : ?int {
    $sql = "SELECT id FROM post_types WHERE name = ?";
    return getDBDataFromArray($sql, [$postTypeName])['id'];
}

/**
 * Получает id хештэга по его имени
 * @param string $tag название хештега
 * @return mixed id хештега или false
 */
function getHashtagIdByName(string $tag)  : ?int {
    $sql = "SELECT id FROM hashtags WHERE hashtag = ?";
    return getDBDataFromArray($sql, [$tag])['id'];
}

/**
 * Получает данные поста по его id
 * @param int $postId] id поста
 * @return mixed массив данных поста или null
 */
function getPostData(int $postId) : ?array {
    $sql= "SELECT posts.*, post_types.name as type_name, users.avatar, users.register_date, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE posts.id = ?";
    return getDBDataFromArray($sql, [$postId]);
}

/**
 * Получает комментарии к посту
 * @param int $postId id поста
 * @return mixed массив комментариев или null
 */
function getPostComments(int $postId) : ?array {
    $sql = "SELECT comments.*, users.avatar, users.login FROM comments LEFT JOIN users ON users.id = comments.user_id WHERE comments.post_id = ?";
    return getDBDataFromArray($sql, [$postId], 'all');
}

/**
 * Получает данные всех постов
 * @param string $postsTypeID id типа поста для фильтрации
 * @return mixed массив данных постов или null
 */
function getPosts(string $postsTypeId = '', string $sortType) : ?array {
    $sortTypeCondition = "ORDER BY posts.views_count DESC";
    if ($sortType === 'date') {
        $sortTypeCondition = "ORDER BY post_create_date DESC";
    } elseif ($sortType === 'likes') {
        $sortTypeCondition = "ORDER BY likes_count DESC";
    }
    
    if (!empty($postsTypeId)) {
        $condition = "WHERE posts.type_id = ?";
        $sql = "SELECT posts.*, DATE(posts.create_date) AS post_create_date, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes_count, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id $condition $sortTypeCondition";
        return getDBDataFromArray($sql, [$postsTypeId], 'all');
    } else {
        $sql = "SELECT posts.*, DATE(posts.create_date) AS post_create_date, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes_count, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id $sortTypeCondition";
        return getDBDataFromArray($sql, null, 'all');
    }
}

/**
 * Получает посты юзера
 * @param string $userId id типа поста для фильтрации
 * @param string $postsTypeID id типа поста для фильтрации
 * @return mixed массив данных постов или null
 */
function getUserPosts(string $userId = '', string $postsTypeId = '') : ?array {
    if (!empty($postsTypeId)) { 
        $sql = "SELECT posts.*, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE posts.user_id = ? AND posts.type_id = ? ORDER BY posts.views_count DESC";
        return getDBDataFromArray($sql, [$userId, $postsTypeId], 'all');
    } else {
        $sql = "SELECT posts.*, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE posts.user_id = ? ORDER BY posts.views_count DESC";
        return getDBDataFromArray($sql, [$userId], 'all');
    }
   
}

/**
 * Получает посты для пагинаций
 * @param int $postsTypeID id типа поста для фильтрации
 * @param bool $needFilter определяет нужен ли фильтр постов по типу
 * @return mixed массив данных постов или null
 */
function getPaginationPosts(array $data, string $sortType, bool $needFilter = false) : ?array {
    $sortTypeCondition = "ORDER BY posts.views_count DESC";
    if ($sortType === 'date') {
        $sortTypeCondition = "ORDER BY post_create_date DESC";
    } elseif ($sortType === 'likes') {
        $sortTypeCondition = "ORDER BY likes_count DESC";
    }

    if (!$needFilter) {
        $sql = "SELECT posts.*, DATE(posts.create_date) AS post_create_date, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes_count, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE posts.is_repost != 1 $sortTypeCondition LIMIT ? OFFSET ?"; 
        return getDBDataFromArray($sql, $data, 'all');     
    } else {
        $condition = 'WHERE posts.type_id = ? AND posts.is_repost != 1';
        $sql = "SELECT posts.*, DATE(posts.create_date) AS post_create_date, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes_count, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id $condition $sortTypeCondition LIMIT ? OFFSET ?";
        return getDBDataFromArray($sql, $data, 'all');
    } 
}

/**
 * Получает данные постов согласно критерию поиска
 * @param string $searchQuery string поисковая фраза
 * @return mixed массив данных постов или null
 */
function getSearchPosts(string $searchQuery) : ?array {
    $sql = "SELECT posts.*, post_types.name as type_name, users.login, users.avatar FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE MATCH(header, post_text) AGAINST(?)";
    return getDBDataFromArray($sql, [$searchQuery], 'all'); 
}

/**
 * Получает данные постов по соответствию тегу
 * @param string $searchQuery string поисковая фраза
 * @return mixed массив данных постов или null
 */
function getPostsByTag(string $searchQuery) : ?array {
    $sql = "SELECT posts.*, post_types.name as type_name, users.login, users.avatar FROM posts JOIN hashtags_posts JOIN hashtags LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE posts.id = hashtags_posts.post_id AND hashtags.id = hashtags_posts.hashtag_id AND hashtags.hashtag = ?";
    return getDBDataFromArray($sql, [$searchQuery], 'all'); 
}

/**
 * Подсчитывает количество записей в переданной таблице по переданному столбцу
 * @param string $dataCount значение столбца таблицы для подсчета
 * @param string string столбец таблицы
 * @param string $table название таблицы
 * @return string количество записей с $dataCount
 */
function getDBDataCount(string $dataCount, string $dataCol, string $table) : string {

    $sql = "SELECT COUNT(*) as count FROM $table WHERE $dataCol = $dataCount";
    $result = getDBDataFromArray($sql, null, 'all');

    return $result[0]['count'];
}

 /**
 * Получает массив id подписок
 * @param int $subscribedUserId id юзера, подписчиков, которого нужно получить
 * @return mixed массив id подписанных юзеров или null
 */
function getSubscribers(int $subscriber_id) : ?array {
    $sql = "SELECT subscribed_user_id FROM subscribes WHERE subscriber_id = ?";
    return getDBDataFromArray($sql, [$subscriber_id], 'all'); 
}

 /**
 * Получает массив постов пользователя, которым ставили лайк
 * @param int $userId id юзера, лаки постов, которого нужно получить
 * @return mixed массив id подписанных юзеров или null
 */
function getLikedUserPosts(int $userId) : ?array {
    $sql = "SELECT posts.*, likes.user_id as like_from_user, likes.like_date, users.login, users.avatar FROM posts LEFT JOIN likes ON likes.post_id = posts.id LEFT JOIN users ON likes.user_id = users.id WHERE posts.user_id = ? AND posts.id = likes.post_id;";
    return getDBDataFromArray($sql, [$userId], 'all'); 
}

/**
 * Получает id пользователя по логину
 * @param int $subscribeUserId id юзера, на которого подписка
 * @param int $userId id подписчика
 * @return mixed id записи или null
 */
function checkSubscription(int $subscribeUserId, int $userId ) : ?array {
    $sql = "SELECT id FROM subscribes WHERE subscriber_id = ? AND subscribed_user_id = ?";
    return getDBDataFromArray($sql, [$userId, $subscribeUserId], 'single');
}

/**
 * Удаляет запись о подписке
 * @param int $subscribedUserId id юзера, на которого подписка
 * @param int $userId id подписчика
 * @return bool
 */
function deleteSubscription(int $subscribedUserId, int $userId) : bool {
    $sql =  "DELETE FROM subscribes WHERE subscriber_id = ? AND subscribed_user_id = ?";
    return deleteDBDataFromArray($sql, [$userId, $subscribedUserId]);
}

/**
 * Получает список типов постов
 * @return mixed массив типов постов или null
 */

function getPostTypes() : ?array {
    $sql = "SELECT * FROM post_types";
    $postTypes = getDBDataFromArray($sql, null, 'all');
    
    return $postTypes;
}

/**
 * Получает список хештегов поста
 * @param int $postId id поста
 * @return mixed массив тегов или null
 */
function getHashtags(int $postId) : ?array {
    $sql = "SELECT hashtag FROM hashtags LEFT JOIN hashtags_posts ON hashtags.id = hashtags_posts.hashtag_id WHERE hashtags_posts.post_id = '$postId'";
    $hashtags = getDBDataFromArray($sql, null, 'all');
    
    return $hashtags;
}

/**
 * Получает значение параметра GET запроса
 * @param string $paramName название параметра
 * @return string значение переданного параметра или null, если параметра нет
 */

function getQueryParam(string $paramName) : string {

    $paramValue = '';

    if (!empty($_GET[$paramName])) {

        if ((int)$_GET[$paramName] != 0) {           
            $paramValue = $_GET[$paramName];                 
        }

        if ((int)$_GET[$paramName] == 0) {
            $paramValue = htmlspecialchars($_GET[$paramName]);
        }
        
    }

    return $paramValue;
}

/**
 * Получает данные пользователя по логину
 * @param string $login логин пользователя
 * @return array массив данных пользователя или null
 */
function getUserDataByLogin(string $login) : ?array {
    $sql = "SELECT * FROM users WHERE login = '$login'";
    return getDBDataFromArray($sql, null, 'single');
}

/**
 * Получает данные пользователя по id
 * @param string $id id пользователя
 * @return array массив данных пользователя или null
 */
function getUserDataById(int $id) : ?array {
    $sql = "SELECT * FROM users WHERE id = '$id'";
    return getDBDataFromArray($sql, null, 'single');
}

/**
 * Перемещает загруженный файл в папку
 * @param array [$file] массив данных о файле из $_FILES
 * @param string $subDirectory дполнительная директива, отвечающая за подпапку для сохранения файла
 * @return void
 */
function moveUploadedImg(array $file, string $subDirectory = '') : void {

    $file_name = $file['name'];
    $file_path = __DIR__ . '/uploads/' . $subDirectory;
    $file_url = '/uploads/' . $subDirectory . $file_name;

    move_uploaded_file($file['tmp_name'], $file_path . $file_name);
}

/**
 * Валидация на пустоту
 * @param string $name имя поля
 * @return bool Результат проверки
 */

function validateEmptyFilled(string $name) : bool {
    
    return !($_POST[$name] == '');
    
}

/**
 * Валидация на корректность ссылки
 * @param string $name имя поля
 * @return bool Результат проверки
 */
function validateLink(string $name) : bool {
    $validateLink = filter_var($_POST[$name], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
    if (!$validateLink) {
        return false;
    } else {
        return true;
    }
}

/**
 * Сохранение изображения по ссылке
 * @param string $name имя поля
 * @param bool $name если true, функция просто возрвразает название файла
 * @return string скачивает фото, перемещает в папку и возвращает результат в текстовом формате
 */
function downloadImageLink(string $name, bool $getImgName = false) : string {
    $imgUrl = $_POST[$name];
    $imgName = array_pop(explode('/', $imgUrl));

    if ($getImgName) {
        return $imgName;
    }

    $headers = get_headers($imgUrl);
    if(preg_match("|200|", $headers[0])) {
        $image = file_get_contents($imgUrl);
    }

    if ($image) {
        file_put_contents(__DIR__ . '/uploads/link-images/' . $imgName, $image);
        return 'success';
    } else {
        return 'Изображение по ссылке не найдено';
    }   
}

/**
 * Валидация загруженного файла
 * @param array $file массив данных файла из $_FILES
 * @return string результат проверки
 */
function validateUploadedFile(array $file) : string {
    $fileType = $file['type'];
    $fileSize = $file['size'];

    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if ($fileSize > 0 && in_array($fileType, $allowedFileTypes)) {
        return 'success';
    } else {
        return 'Недопустимый формат фото. Разрещены: gif, png, jpeg, либо файл не загружен';
    }
}

/**
 * Валидация тегов
 * @param string $tags строка тегов
 * @return int результат проверки
 */
function validateTags(string $tags) : int {
    $tags = explode(' ', $tags);
    foreach ($tags as $tagIndex => $tag) {
        return preg_match('/^[a-zа-яё]+$/iu', $tag);          
    } 
}

/**
 * Валидация почты
 * @param string $mail почта
 * @return mixed результат проверки или null
 */
function validateEmail(string $mail) : ?array {
    $emailValid = filter_var($mail, FILTER_VALIDATE_EMAIL);
    if ($emailValid) {
        return checkDBUserData('email', $mail);
    }  else {
        return null;
    }   
}

/**
 * Проверка наличия данных пользователя в БД
 * @param [$fieldToCheck] [string] [столбец БД для проверки]
 * @param [$value] [string] [значение для проверки]
 * @return {array} массив с ключом result и значением результата отработки функции
 */
function checkDBUserData(string $fieldToCheck, string $value) : array {

    $userTableColumns = [
        'email',
        'login',
    ];

    if (!in_array($fieldToCheck, $userTableColumns)) {
        return ['result' => 'db-column-error'];
    } else {
        if ($fieldToCheck == 'email') {
            $sql = "SELECT id FROM users WHERE email = ?";
        } elseif ($fieldToCheck == 'login') {
            $sql = "SELECT id FROM users WHERE login = ?";
        }
        
        $valueExist = getDBDataFromArray($sql, [$value])['id'];
        if(!$valueExist) {
            return ['result' => 'success']; 
        } else {
            return ['result' => 'value-exist'];
        }
    }  
}

/**
 * Получает хеш пароля пользователя из БД
 * @param string $login логин
 * @return bool результат проверки
 */
function verifyUserAuthPassword(string $login, string $password) : bool {
    $sql = "SELECT password FROM users WHERE login = ?";
    $DBpassword = getDBDataFromArray($sql, [$login])['password'];

    return password_verify($password, $DBpassword);    
}

/**
 * Проверяет записан ли пароль для переданного логина
 * @param string $login логин
 * @return bool результат проверки
 */
function checkUserPassword(string $login) : bool {
    $sql = "SELECT password FROM users WHERE login = ?";
    $DBpassword = getDBDataFromArray($sql, [$login])['password'];

    return (bool)$DBpassword;
}

/**
 * Валидация логина
 * @param string $login логин
 * @return array Результат проверки или false
 */
function validateLogin(string $login) : array {
    $loginValid = checkLength($login, 20);
    if ($loginValid) {
        return checkDBUserData('login', $login);
    }  else {
        return ['result' => false];
    }   
}

/**
 * Проверка логина пользователя на предмет существования в БД
 * @param string $login логин
 * @return bool результат проверки
 */
function checkLength(string $text, int $length) : bool {
    return !(strlen($text) > $length);
}

/**
 * Валидация пароля
 * @param string $password пароль
 * @param string $passwordRepeat повтор пароля
 * @return bool результат проверки
 */
function validatePassword(string $password, string $passwordRepeat) : bool {
    return ($_POST[$password] == $_POST[$passwordRepeat]);
}

/**
 * Преобразования массива ошибок из нескольких форм в единый список разных ошибок
 * @param array $errorsList многомерный массив с результатами проверки форм
 * @return array массив ошибок без учета к какой форме они относятся
 */
function getFormValidateErrors(array $errorsList) : array {
    $validateErrors = [];

    foreach ($errorsList as $errorForm => $errorResultValues) {
        foreach($errorResultValues as $errorResultValue) {
            array_push($validateErrors, $errorResultValue);
        }
    }

    return $validateErrors;
}

/**
 * Проверяет существование фото в папках
 * @param string $fileName название файла
 * @return string путь к файлу
 */
function checkFilePath(string $fileName) : string {

    if (file_exists('img/' . $fileName)) {    
        $imgPath = '/img/' . $fileName;
    }

    if (file_exists('uploads/' . $fileName)) {
        $imgPath = '/uploads/' . $fileName;
    }

    if (file_exists('uploads/link-images/' . $fileName)) {
        $imgPath = '/uploads/link-images/' . $fileName;
    }

    if (file_exists('uploads/users-avatar/' . $fileName)) {
        $imgPath = '/uploads/users-avatar/' . $fileName;
    }

    return htmlspecialchars($imgPath);
}

/**
 * Обрезает $text до $cropSybmols
 * @param string $text текст для обрезки
 * @param int $cropSybmols до какого кол-ва символов обрезать
 * @return string обрезанная строка со ссылкой Читать далее
 */

function cropText(string $text, int $cropSybmols = 300) : string {
    $words = explode(' ', $text);
    
    $symbolsCount = 0;
    $cropWords = [];

    $redMoreElement = '
        <div class="post-text__more-link-wrapper">
            <a class="post-text__more-link" href="#">Читать далее</a>
        </div>
    ';

    foreach ($words as $word) {
        $symbolsCount += strlen($word);   

        if ($symbolsCount > $cropSybmols) {            
            return implode(' ', $cropWords) . '...' . $redMoreElement; 
        } 
        
        array_push($cropWords, $word);
    }

    return $text;            
}
 
/**
 * Сравнивает дату $date c текущей
 * @param object $date объект произовлаьной даты
 * @param string $words добавочное слово после вывода временного периода
 * @return string количество времени прошедшего с $date до текущей даты в относительном формате
 */

function getRelativeDateDifference (DateTime $date, string $words) : string {

    $currentDate = new DateTime();
    $dateDiff = $date->diff($currentDate);

    switch (true) {
        case ($dateDiff->days / 7 >= 5 ) :
            return $dateDiff->m  . ' ' . get_noun_plural_form((int)floor($dateDiff->m), 'месяц', 'месяца', 'месяцев') . ' ' . $words; 
        
        case ($dateDiff->days / 7 >= 1 && $dateDiff->days / 7 < 5) :
            return floor($dateDiff->days / 7) . ' ' . get_noun_plural_form((int)floor($dateDiff->days / 7), 'неделя', 'недели', 'недель') . ' ' . $words;

        case ($dateDiff->d >= 1 && $dateDiff->d < 7) :
            return $dateDiff->d . ' ' . get_noun_plural_form((int)floor($dateDiff->d), 'день', 'дня', 'дней') . ' ' . $words;
        
        case ($dateDiff->h >= 1 && $dateDiff->h < 24) :
            return $dateDiff->h . ' ' . get_noun_plural_form((int)floor($dateDiff->h), 'час', 'часа', 'часов') . ' ' . $words;

        case ($dateDiff->i > 0 && $dateDiff->i < 60) :
            return $dateDiff->i . ' ' . get_noun_plural_form((int)floor($dateDiff->i), 'минута', 'минуты', 'минут') . ' ' . $words;

        default:
            return $date->format('d.m.Y H:i');
    }
}

/**
 * Проводит валидацию полей формы добавления комментария
 * @param array $postRequest массив отправленных данных
 * @param array $requiredFields массив обязательных полей
 * @return array результирующий массив проверки
 */
function validateAddCommentForm(array $postRequest, array $requiredFields, int $commentLength) : array {

    $validateResult = [];
    foreach ($postRequest as $fieldName => $fieldValue) {
        //валидация на заполненность
        if (in_array($fieldName, $requiredFields)) {

            if (validateEmptyFilled($fieldName)) {
                $validateResult['comment-form'][$fieldName] = 'success';
            } else {
                $validateResult['comment-form'][$fieldName] = 'Это поле должно быть заполнено';
            }

            if (!checkLength($fieldValue, $commentLength)) {
                $validateResult['comment-form'][$fieldName] = 'success';
            }  else {
                $validateResult['comment-form'][$fieldName] = 'Длина комментария должна быть больше четырех символов';
            }        
        }
    }
    
    if (isset($postRequest['post-id']) &&  $validateResult['comment-form']['comment-text'] == 'success') {
        if (getPostData((int)filter_var($postRequest['post-id'], FILTER_SANITIZE_NUMBER_INT))) {
            $validateResult['comment-form']['comment-text'] = 'success';
        } else {
            $validateResult['comment-form']['comment-text'] = 'Не существует поста с id: ' . $postRequest['post-id'];
        }
    }

    return $validateResult;
}

/**
 * Устанавливает количество просмотров поста
 * @param int $postId id поста
 * @param int $currentViewCount текущее количество просмотров
 * @return bool
 */
function increasePostView(int $postId, int $currentViewCount) : bool {
    
    $newCount = $currentViewCount + 1;
    $sql = "UPDATE posts SET views_count = '$newCount' WHERE id = ?";
    if (getPostData($postId)) {
        return updateDBDataFromArray($sql, [$postId]);
    }
}

/**
 * Устанавливает количество репостов
 * @param int $postId id поста
 * @param int $currentRepostCount текущее количество репостов
 * @return bool
 */
function increaseReposts(int $postId, int $currentRepostCount) : bool {

    $newCount = $currentRepostCount + 1; 
    $sql = "UPDATE posts SET repost_count = '$newCount' WHERE id = ?";
    
    return updateDBDataFromArray($sql, [$postId]);
    
}

/**
 * Копирует пост и подменяет id юзера, и устанавливает признак репоста
 * @param int $postId id поста
 * @param int $userId id юзера, делающего репост
 * @return bool
 */
function repostUserPost(int $postId, int $userId) : bool {
    $sanitizedId = (int)filter_var($postId, FILTER_SANITIZE_NUMBER_INT);
    $sql = "INSERT INTO posts (user_id, type_id, header, post_text, quote_author, post_image, post_video, post_link, origin_user_id, origin_post_id) SELECT user_id, type_id, header, post_text, quote_author, post_image, post_video, post_link, user_id, id FROM posts WHERE id = ?";
    $newPostId = insertDBDataFromArray($sql, [$postId]);

    if ($newPostId) {
        $sql = "UPDATE posts SET is_repost = true, user_id = ? WHERE id = ?";
        return updateDBDataFromArray($sql, [$userId, $newPostId]);
    } else {
        return false;
    }   
}

/**
 * Отправляет письмо на почту
 * @param string $to почта получателя
 * @param string $subject тема сообщения
 * @param string $text текст сообщения
 * @return void
 */
function sendEmail(string $to, string $subject, string $text) : void {
    // Конфигурация траспорта
    $dsn = 'smtp://rush89@list.ru:Pe23htFpg9ugYgSymqsK@smtp.mail.ru:465';
    $transport = Transport::fromDsn($dsn);
    
    $message = new Email();
    $message->to($to);
    $message->Sender('rush89@list.ru');
    $message->subject($subject);
    $message->text($text);
    // Отправка сообщения
    $mailer = new Mailer($transport);
    try {
        $mailer->send($message);
    } catch (Exception $e) {
        $e->getMessage();
    }
}
