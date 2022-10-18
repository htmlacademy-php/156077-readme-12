<?php
declare(strict_types = 1);

/**
 * Подключает к базе данных
 * @return {object} соединение с БД, либо выходит из скрипта
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
 *
 * @param $data Массив переменных
 * @return [string} строка типов переменных для подготовленного запроса
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
 * @param [$sql] [sql_query] [простой массив переменных]
 * @param [$data] [array] [простой массив переменных]
 * @param [$resultsType] [string] [формат возвращаемых данных]
 * @return {mixed} массив данных или false
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
        
        if ($stmt) {
            $stmt->bind_param($varTypes, ...$data);
            $stmt->execute();
            $result = $stmt->get_result();    
        } else {
            return null;
        }
        
        $mysqli->close();            
    }
    
    if ($resultsType == 'single') {     
        $result = $result->fetch_assoc();
    }
    if ($resultsType == 'all') {
        $result = $result->fetch_all(MYSQLI_ASSOC);
    }

    return $result;
    
}

/**
 * Удаляет данные из БД подготовкой sql выражения
 * @param [$sql] [sql_query] [простой массив переменных]
 * @param [$data] [array] [простой массив переменных]
 * @return {mixed} результат stmp или false
 */
function deleteDBDataFromArray(string $sql, array $data) : bool {
    $mysqli = dbConnection();
    
    if (!$data) {
        return false;
    } else {     
        $varTypes = getVarTypes($data);      
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param($varTypes, ...$data);
            $stmt->execute();
            $result = $stmt->get_result();    
        }   

        return true;  
    } 
}

/**
 * Делает запись в БД из массива переменных
 * @param [$data] [простой массив переменных]
 * @param [$sql] [sql string] [sql запрос]
 * @return {int} id добавленной записи
 */

function insertDBDataFromArray(string $sql, array $data) : int {

    if (!$data || !is_array($data)) {
        return false;
    } else {
        $varTypes = getVarTypes($data);
        $mysqli = dbConnection();
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($varTypes, ...$data);
            $stmt->execute();
            $lastId = $mysqli->insert_id;
        } else {
            return false;
        }
        
        $mysqli->close();  
        
        return $lastId;
    }
    
}

/**
 * Записывает в базу данных новый лайк поста
 * @param [$postId] [id поста]
 * @param [$userId] [id юзера]
 * @return {mixed} id добавленной записи или false
 */
function addPostLike(int $postId, int $userId) : int {
    $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
    return insertDBDataFromArray($sql, [$userId, $postId]);
}

/**
 * Записывает в базу данных новый пост
 * @param [$data] [простой массив переменных с данными поста]
 * @return {mixed} id добавленной записи или false
 */
function insertNewPost(array $data) : int {
    $sql = "INSERT INTO posts (user_id, type_id, header, post_text, quote_author, post_image, post_video, post_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    return insertDBDataFromArray($sql, $data);
}

/**
 * Записывает в базу данных новый коммент
 * @param [$data] [простой массив переменных с данными комментария]
 * @return {mixed} id добавленной записи или false
 */
function insertNewComment(array $data) : int {
    $sql = "INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)";
    return insertDBDataFromArray($sql, $data);
}

/**
 * Записывает в базу данных нового юзера
 * @param [$data] [простой массив переменных с данными юзера]
 * @return {mixed} id добавленной записи или false
 */
function insertNewUser(array $data) : int {
    $sql = "INSERT INTO users (email, login, password, avatar) VALUES (?, ?, ?, ?)";
    return insertDBDataFromArray($sql, $data);
}

/**
 * Записывает в базу теги поста
 * @param [$data] [array] [простой массив переменных с данными тегов]
 * @param [$insertTable] [string] [флаг в какую таблицу нужно сделать запись тэга - со списком тегов или с соответсвием тега посту]
 * @return {mixed} id добавленной записи или false
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
 * @param [$subscriberId] [int] [id подписывающегося юзера]
 * @param [$subscribedUserId] [int] [флаг в какую таблицу нужно сделать запись тэга - со списком тегов или с соответсвием тега посту]
 * @return {mixed} id добавленной записи или false
 */
function subscribeUser(int $subscriberId, int $subscribedUserId) : int {
    $sql = "INSERT INTO subscribes (subscriber_id, subscribed_user_id) VALUES (?, ?)";
    return insertDBDataFromArray($sql, [$subscriberId, $subscribedUserId]);
}

/**
 * Получает id типа поста по его имени
 * @param [$postTypeName] [string] [название типа поста]
 * @return {mixed} id типа поста или false
 */

function getPostTypeIdByName(string $postTypeName) : ?int {
    $sql = "SELECT id FROM post_types WHERE name = ?";
    return getDBDataFromArray($sql, [$postTypeName])['id'];
}

/**
 * Получает id хештэга по его имени
 * @param [$tag] [string] [название хештега]
 * @return {mixed} id хештега или false
 */
function getHashtagIdByName(string $tag)  : ?int {
    $sql = "SELECT id FROM hashtags WHERE hashtag = ?";
    return getDBDataFromArray($sql, [$tag])['id'];
}

/**
 * Получает данные поста по его id
 * @param [$postId] [int] [id поста]
 * @return {mixed} массив данных поста или false
 */
function getPostData(int $postId) : ?array {
    $sql= "SELECT posts.*, post_types.name as type_name, users.avatar, users.register_date, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE posts.id = ?";
    return getDBDataFromArray($sql, [$postId]);
}

/**
 * Получает комментарии к посту
 * @param [$postId] [int] [id поста]
 * @return {mixed} массив комментариев или false
 */
function getPostComments(int $postId) : ?array {
    $sql = "SELECT comments.*, users.avatar, users.login FROM comments LEFT JOIN users ON users.id = comments.user_id WHERE comments.post_id = ?";
    return getDBDataFromArray($sql, [$postId], 'all');
}

/**
 * Получает данные всех постов
 * @param [$postsTypeID] [string] [id типа поста для фильтрации]
 * @return {mixed} массив данных постов или false
 */
function getPosts(string $postsTypeId = '') : ?array {
    if (!empty($postsTypeId)) {
        $condition = "WHERE posts.type_id = ?";
        $sql = "SELECT posts.*, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id $condition ORDER BY posts.views_count DESC";
        return getDBDataFromArray($sql, [$postsTypeId], 'all');
    } else {
        $sql = "SELECT posts.*, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id ORDER BY posts.views_count DESC";
        return getDBDataFromArray($sql, null, 'all');
    }
}

/**
 * Получает посты юзера
 * @param [$userId ] [string] [id типа поста для фильтрации]
 * @param [$postsTypeID] [string] [id типа поста для фильтрации]
 * @return {mixed} массив данных постов или false
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
 * @param [$postsTypeID] [int] [id типа поста для фильтрации]
 * @param [$needFilter] [bool] [определяет нужен ли фильтр постов по типу]
 * @return {mixed} массив данных постов или false
 */
function getPaginationPosts(array $data, bool $needFilter = false) : ?array {
    if (!$needFilter) {
        $sql = "SELECT posts.*, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id ORDER BY posts.views_count DESC LIMIT ? OFFSET ?"; 
        return getDBDataFromArray($sql, $data, 'all');     
    } else {
        $condition = 'WHERE posts.type_id = ?';
        $sql = "SELECT posts.*, post_types.name as type_name, users.avatar, users.login FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id $condition ORDER BY posts.views_count DESC LIMIT ? OFFSET ?";
        return getDBDataFromArray($sql, $data, 'all');
    } 
}

/**
 * Получает данные постов согласно критерию поиска
 * @param [$searchQuery] [string] [Поисковая фраза]
 * @return {mixed} массив данных постов или false
 */
function getSearchPosts(string $searchQuery) : ?array {
    $sql = "SELECT posts.*, post_types.name as type_name, users.login, users.avatar FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE MATCH(header, post_text) AGAINST(?)";
    return getDBDataFromArray($sql, [$searchQuery], 'all'); 
}

/**
 * Подсчитывает количество записей в переданной таблице по переданному столбцу
 * @param [$dataCount] [all] [значение столбца таблицы для подсчета]
 * @param [$dataCol] [string] [столбец таблицы]
 * @param [$table] [string] [название таблицы]
 * @return {string} количество записей с [$dataCount]
 */
function getDBDataCount(string $dataCount, string $dataCol, string $table) : string {

    $sql = "SELECT COUNT(*) as count FROM $table WHERE $dataCol = $dataCount";
    $result = getDBDataFromArray($sql, null, 'all');

    return $result[0]['count'];
}

 /**
 * Получает массив id подписок
 * @param [$subscribedUserId] [int] [id юзера, подписчиков, которого нужно получить]
 * @return {miixed} массив id подписанных юзеров или false
 */
function getSubscribers(int $subscriber_id) : ?array {
    $sql = "SELECT subscribed_user_id FROM subscribes WHERE subscriber_id = ?";
    return getDBDataFromArray($sql, [$subscriber_id], 'all'); 
}

 /**
 * Получает массив постов пользователя, которым ставили лайк
 * @param [$userId] [int] [id юзера, лаки постов, которого нужно получить]
 * @return {miixed} массив id подписанных юзеров или false
 */
function getLikedUserPosts(int $userId) : ?array {
    $sql = "SELECT posts.*, likes.user_id as like_from_user, likes.like_date, users.login, users.avatar FROM posts LEFT JOIN likes ON likes.post_id = posts.id LEFT JOIN users ON likes.user_id = users.id WHERE posts.user_id = ? AND posts.id = likes.post_id;";
    return getDBDataFromArray($sql, [$userId], 'all'); 
}

/**
 * Получает id пользователя по логину
 * @param [$subscribeUserId] [int] [id юзера, на которого подписка]
 * @param [$userId] [int] [id подписчика]
 * @return {mixed} id записи или false
 */
function checkSubscription(int $subscribeUserId, int $userId ) : ?array {
    $sql = "SELECT id FROM subscribes WHERE subscriber_id = ? AND subscribed_user_id = ?";
    return getDBDataFromArray($sql, [$userId, $subscribeUserId], 'single');
}

/**
 * Удаляет запись о подписке
 * @param [$subscribedUserId] [int] [id юзера, на которого подписка]
 * @param [$userId] [int] [id подписчика]
 * @return {mixed} id записи или false
 */
function deleteSubscription(int $subscribedUserId, int $userId) : bool {
    $sql =  "DELETE FROM subscribes WHERE subscriber_id = ? AND subscribed_user_id = ?";
    return deleteDBDataFromArray($sql, [$userId, $subscribedUserId]);
}

/**
 * Получает список типов постов
 * @return {mixed} массив типов постов или false
 */

function getPostTypes() : ?array {
    $sql = "SELECT * FROM post_types";
    $postTypes = getDBDataFromArray($sql, null, 'all');
    
    return $postTypes;
}

/**
 * Получает списокхештегов поста
 * @param [$postId] [int] [id поста]
 * @return {mixed} массив тегов или false
 */
function getHashtags(int $postId) : ?array {
    $sql = "SELECT hashtag FROM hashtags LEFT JOIN hashtags_posts ON hashtags.id = hashtags_posts.hashtag_id WHERE hashtags_posts.post_id = '$postId'";
    $hashtags = getDBDataFromArray($sql, null, 'all');
    
    return $hashtags;
}

/**
 * Получает значение параметра GET запроса
 * @param [$paramName] [string] [название параметра]
 * @return {string} значение переданного параметра или null, если параметра нет
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
 * Получает id пользователя по логину
 * @param [$login] [sring] [логин пользователя]
 * @return {array} массив данных пользователя
 */
function getUserDataByLogin(string $login) : ?array {
    $sql = "SELECT * FROM users WHERE login = '$login'";
    return getDBDataFromArray($sql, null, 'single');
}

/**
 * Получает id пользователя по логину
 * @param [$login] [sring] [id пользователя]
 * @return {array} массив данных пользователя
 */
function getUserDataById(int $id) : ?array {
    $sql = "SELECT * FROM users WHERE id = '$id'";
    return getDBDataFromArray($sql, null, 'single');
}

/**
 * Перемещает загруженный файл в папку
 * @param [$file] [file_object] [массив данных о файле из $_FILES]
 * @param [$subDirectory] string] [дполнительная директива, отвечающая за подпапку для сохранения файла]
 * @return {none}
 */
function moveUploadedImg(array $file, string $subDirectory = '') {

    $file_name = $file['name'];
    $file_path = __DIR__ . '/uploads/' . $subDirectory;
    $file_url = '/uploads/' . $subDirectory . $file_name;

    move_uploaded_file($file['tmp_name'], $file_path . $file_name);
}

/**
 * Валидация на пустоту
 * @param [$name] [string] [имя поля]
 * @return {bool} Результат проверки
 */

function validateEmptyFilled(string $name) : bool {
    if ($_POST[$name] == '') {
        return false;
    } else {
        return true;
    }
}

/**
 * Валидация на корректность ссылки
 * @param [$name] [string] [имя поля]
 * @return {bool} Результат проверки
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
 * @param [$name] [string] [имя поля]
 * @param [$name] [bool] [если true, функция просто возрвразает название файла]
 * @return {string} скачивает фото, перемещает в папку и возвращает результат в текстовом формате
 */
function downloadImageLink(string $name, bool $getImgName = false) : string {
    $imgUrl = $_POST[$name];
    $imgName = array_pop(explode('/', $imgUrl));

    if ($getImgName) return $imgName;

    $headers = @get_headers($imgUrl);
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
 * @param [$file] [file_data] [массив данных файла из $_FILES]
 * @return {string} Результат проверки
 */
function validateUploadedFile(array $file) : string {
    $fileType = $file['type'];
    $fileSize = $file['size'];

    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if ($fileSize > 0) {
        if (in_array($fileType, $allowedFileTypes)) {       
            return 'success';
        } else {
            return 'Недопустимый формат фото. Разрещены: gif, png, jpeg';
        }
        
    } else {
        return 'Размер файла 0 байт или изображение не загружено';
    }
}

/**
 * Валидация тегов
 * @param [$tags] [string] [строка тегов]
 * @return {bool} Результат проверки
 */
function validateTags(string $tags) : bool {
    $tags = explode(' ', $tags);
    foreach ($tags as $tagIndex => $tag) {
        if(!preg_match('/^[a-zа-яё]+$/iu', $tag)) {
            return false;
        }         
    }

    return true;
}

/**
 * Валидация почты
 * @param [$mail] [string] [почта]
 * @return {mixed} Результат проверки или false
 */
function validateEmail(string $mail) {
    $emailValid = filter_var($mail, FILTER_VALIDATE_EMAIL);
    if ($emailValid) {
        return checkDBUserData('email', $mail);
    }  else {
        return false;
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
 * @param [$login] [string] [логин]
 * @return {bool} результат проверки
 */
function verifyUserAuthPassword(string $login, string $password) : bool {
    $sql = "SELECT password FROM users WHERE login = ?";
    $DBpassword = getDBDataFromArray($sql, [$login])['password'];

    return password_verify($password, $DBpassword);    
}

/**
 * Проверяет записан ли пароль для переданного логина
 * @param [$login] [string] [логин]
 * @return {bool} результат проверки
 */
function checkUserPassword(string $login) : bool {
    $sql = "SELECT password FROM users WHERE login = ?";
    $DBpassword = getDBDataFromArray($sql, [$login])['password'];

    if ($DBpassword) {
        return true;
    } else {
        return false;
    }
}

/**
 * Валидация логина
 * @param [$login] [string] [логин]
 * @return {mixed} Результат проверки или false
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
 * @param [$login] [string] [логин]
 * @return {bool} результат проверки
 */
function checkLength(string $text, int $length) : bool {
 
    if (strlen($text) > $length) {
        return false;
    } else {
        return true;
    }

}

/**
 * Валидация пароля
 * @param [$password] [string] [пароль]
 * @param [$passwordRepeat] [string] [повтор пароля]
 * @return {bool} Результат проверки
 */
function validatePassword(string $password, string $passwordRepeat) : bool {
    if ($_POST[$password] == $_POST[$passwordRepeat]) {
       return true;
    }  else {
        return false;
    }   
}

/**
 * Преобразования массива ошибок из нескольких форм в единый список разных ошибок
 * @param [$errorsList] [array] [многомерный массив с результатами проверки форм]
 * @return {array} Массив ошибок без учета к какой форме они относятся
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
 * @param [$fileName] [string] [название файла]
 * @return {string} Путь к файлу
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
 * @param [string] [$text] [текст для обрезки]
 * @param [int] [$cropSybmols] [до какого кол-ва символов обрезать]
 * @return {string} обрезанная строка со ссылкой Читать далее
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
 * @param [date object] [$date] [объект произовлаьной даты]
 * @param [string] [$words] [добавочное слово после вывода временного периода]
 * @return {string} количество времени прошедшего с $date до текущей даты в относительном формате
 */

function getRelativeDateDifference (DateTime $date, string $words) : string {

    $currentDate = new DateTime();
    $dateDiff = $date->diff($currentDate);

    $relativeDate = '';
    switch (true) {
        case ($dateDiff->days / 7 >= 5 ) :
            return $relativeDate = $dateDiff->m  . ' ' . get_noun_plural_form((int)floor($dateDiff->m), 'месяц', 'месяца', 'месяцев') . ' ' . $words; 
        
        case ($dateDiff->days / 7 >= 1 && $dateDiff->days / 7 < 5) :
            return $relativeDate = floor($dateDiff->days / 7) . ' ' . get_noun_plural_form((int)floor($dateDiff->days / 7), 'неделя', 'недели', 'недель') . ' ' . $words;

        case ($dateDiff->d >= 1 && $dateDiff->d < 7) :
            return $relativeDate = $dateDiff->d . ' ' . get_noun_plural_form((int)floor($dateDiff->d), 'день', 'дня', 'дней') . ' ' . $words;
        
        case ($dateDiff->h >= 1 && $dateDiff->h < 24) :
            return $relativeDate = $dateDiff->h . ' ' . get_noun_plural_form((int)floor($dateDiff->h), 'час', 'часа', 'часов') . ' ' . $words;

        case ($dateDiff->i > 0 && $dateDiff->i < 60) :
            return $relativeDate = $dateDiff->i . ' ' . get_noun_plural_form((int)floor($dateDiff->i), 'минута', 'минуты', 'минут') . ' ' . $words;

        default:
            return $relativeDate = $date->format('d.m.Y H:i');
    }
}

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