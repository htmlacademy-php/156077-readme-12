<?php

/**
 * Подключает к базе данных
 * @return {object} соединение с БД, либо выходит из скрипта
 */
function dbConnection() {
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
function getVarTypes($data) {
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
 * @return {array} массив данных
 */

function getDBDataFromArray($sql, $data = NULL, $resultsType = 'single') {
    $mysqli = dbConnection();
    
    if (!$data) {
        $result = $mysqli->query($sql);
    } elseif (!is_array($data)) {
        return false;
    } else {     
        $varTypes = getVarTypes($data);      
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param($varTypes, ...$data);
            $stmt->execute();
            $result = $stmt->get_result();    
        } else {
            return false;
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
 * Делает запись в БД из массива переменных
 * @param [$data] [простой массив переменных]
 * @param [$sql] [sql string] [sql запрос]
 * @return {int} id добавленной записи
 */

function insertDBDataFromArray($sql, $data) {

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
 * Записывает в базу данных новый пост
 * @param [$data [простой массив переменных с данными поста]
 * @return {int} id добавленной записи
 */
function insertNewPost($data) {
    $sqlPostPrepare = "INSERT INTO posts (user_id, type_id, header, post_text, quote_author, post_image, post_video, post_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    return insertDBDataFromArray($sqlPostPrepare, $data);
}

/**
 * Записывает в базу данных нового юзера
 * @param [$data] [простой массив переменных с данными юзера]
 * @return {int} id добавленной записи
 */
function insertNewUser($data) {
    $sqlUserPrepare = "INSERT INTO users (email, login, password, avatar) VALUES (?, ?, ?, ?)";
    return insertDBDataFromArray($sqlUserPrepare, $data);
}

/**
 * Записывает в базу теги поста
 * @param [$data [простой массив переменных с данными тегов]
 * @param [$insertTable] [string] [флаг в какую таблицу нужно сделать запись тэга - со списком тегов или с соответсвием тега посту]
 * @return {int} id добавленной записи
 */
function insertPostTags($data, $insertTable) {
    $sqlTagsPostsPrepare = "INSERT INTO hashtags_posts (post_id, hashtag_id) VALUES (?, ?)";
    $sqlTagsPrepare = "INSERT INTO hashtags (hashtag) VALUES (?)";

    if ($insertTable == 'hashtags') {
        return insertDBDataFromArray($sqlTagsPrepare, $data);
    } elseif ($insertTable == 'hashtags_post') {
        return insertDBDataFromArray($sqlTagsPostsPrepare, $data);
    }
      
}

/**
 * Получает id типа поста по его имени
 * @param [$postTypeName] [string] [название типа поста]
 * @return {int} id типа поста
 */

function getPostTypeIdByName($postTypeName) {
    $sql = "SELECT id FROM post_types WHERE name = ?";
    return getDBDataFromArray($sql, [$postTypeName])['id'];
}

/**
 * Получает id хештэга по его имени
 * @param [$tag] [string] [название хештега]
 * @return {int} id хештега
 */
function getHashtagIdByName($tag) {
    $sql = "SELECT id FROM hashtags WHERE hashtag = ?";
    return getDBDataFromArray($sql, [$tag])['id'];
}

/**
 * Получает данные поста по его id
 * @param [$postId] [int] [id поста]
 * @return {array} массив данных поста
 */
function getPostData($postId) {
    $sql= "SELECT posts.*, post_types.name as type_name, users.avatar, users.register_date FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE posts.id = ?";

    return getDBDataFromArray($sql, [$postId]);
}

/**
 * Получает данные всех постов
 * @param [$postsTypeID] [int] [id типа поста для фильтрации]
 * @return {array} массив данных постов
 */
function getPosts($postsTypeID = '') {
    if ($postsTypeID != '') {
        $condition = 'WHERE posts.type_id = ?';
        $sql = "SELECT posts.*, post_types.name as type_name, users.avatar FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id $condition ORDER BY posts.views_count DESC";
        return getDBDataFromArray($sql, [$postsTypeID], 'all');
    } else {
        $sql = "SELECT posts.*, post_types.name as type_name, users.avatar FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id ORDER BY posts.views_count DESC";
        return getDBDataFromArray($sql, false, 'all');
    }
}

/**
 * Подсчитывает количество записей в переданной таблице по переданному столбцу
 * @param [$dataCount] [all] [значение столбца таблицы для подсчета]
 * @param [$dataCol] [string] [столбец таблицы]
 * @param [$table] [string] [название таблицы]
 * @return {int} количество записей с [$dataCount]
 */

function getDBDataCount($dataCount, $dataCol, $table) {

    $sql = "SELECT COUNT(*) as count FROM $table WHERE $dataCol = $dataCount";
    $result = getDBDataFromArray($sql, false, 'all');
    
    return $result[0]['count'];
}

/**
 * Получает список типов постов
 * @return {array} массив типов постов
 */

function getPostTypes() {
    $sql = "SELECT * FROM post_types";
    $postTypes = getDBDataFromArray($sql, false, 'all');
    
    return $postTypes;
}

/**
 * Получает значение параметра GET запроса
 * @param [$paramName] [string] [название параметра]
 * @return {all} значение переданного параметра или 'none', если параметра нет
 */

function getQueryParam($paramName) {

    $paramValue = NULL;

    if (!empty($_GET[$paramName])) {

        if ((int)$_GET[$paramName] != 0) {           
            $paramValue = (int)$_GET[$paramName];                 
        }

        if ((int)$_GET[$paramName] == 0) {
            $paramValue = htmlspecialchars($_GET[$paramName]);
        }
        
    }

    return $paramValue;
}

/**
 * Перемещает загруженный файл в папку
 * @param [$file] [file_object] [массив данных о файле из $_FILES]
 * @param [$subDirectory] string] [дполнительная директива, отвечающая за подпапку для сохранения файла]
 * @return {none}
 */
function moveUploadedImg($file, $subDirectory = '') {

    $file_name = $file['name'];
    $file_path = __DIR__ . '/uploads/' . $subDirectory;
    $file_url = '/uploads/' . $subDirectory . $file_name;

    move_uploaded_file($file['tmp_name'], $file_path . $file_name);
}

/**
 * Валидация на пустоту
 * @param [$name] [string] [имя поля]
 * @return {string} Результат проверки
 */

function validateEmptyFilled($name) {
    if ($_POST[$name] == '') {
        return false;
    } else {
        return true;
    }
}

/**
 * Валидация на корректность ссылки
 * @param [$name] [string] [имя поля]
 * @return {string} Результат проверки
 */
function validateLink($name) {
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
function downloadImageLink($name, $getImgName = false) {
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
function validateUploadedFile($file) {
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
 * @return {string} Результат проверки
 */
function validateTags($tags) {
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
 * @return {string} Результат проверки
 */
function validateEmail($mail) {
    $emailValid = filter_var($mail, FILTER_VALIDATE_EMAIL);
    if ($emailValid) {
        return checkDBUserData('email', $mail);
    }  else {
        return false;
    }   
}

/**
 * Проверка наличия данных пользователя в БД
 * @param [$value] [string] [столбец БД для проверки]
 * @param [$value] [string] [значение для проверки]
 * @return {string} строка результата проверки
 */
function checkDBUserData($fieldToCheck, $value) {

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
 * @return {string} хеш пароля или false
 */
function getUserPassword($login) {
    $sql = "SELECT password FROM users WHERE login = ?";
    $password = getDBDataFromArray($sql, [$login])['password'];

    if ($password) {
        return $password;
    } else {
        return false;
    }
}

/**
 * Валидация логина
 * @param [$login] [string] [логин]
 * @return {string} Результат проверки
 */
function validateLogin($login) {
    $loginValid = checkLoginLength($login);
    if ($loginValid) {
        return checkDBUserData('login', $login);
    }  else {
        return false;
    }   
}

/**
 * Проверка логина пользователя на предмет существования в БД
 * @param [$login] [string] [логин]
 * @return {string} строка результата проверки
 */
function checkLoginLength($login) {
 
    if (strlen($login) > 20) {
        return false;
    } else {
        return true;
    }

}

/**
 * Валидация пароля
 * @param [$mail] [string] [пароль]
 * @param [$passwordRepeat] [string] [повтор пароля]
 * @return {string} Результат проверки
 */
function validatePassword($password, $passwordRepeat) {
    if ($_POST[$password] == $_POST[$passwordRepeat]) {
       return true;
    }  else {
        return false;
    }   
}

/**
 * Валидация пароля
 * @param [$errorsList] [array] [многомерный массив с результатами проверки форм]
 * @return {array} Массив ошибок без учета к какой форме они относятся
 */
function getFormValidateErrors($errorsList) {
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
function checkFilePath($fileName) {

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
 * @param [number] [$cropSybmols] [до какого кол-ва символов обрезать]
 * @return {string} обрезанная строка со ссылкой Читать далее
 */

function cropText($text, $cropSybmols = 300) {
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

function getRelativeDateDifference ($date, $words) {

    $currentDate = new DateTime();
    $dateDiff = $date->diff($currentDate);

    $relativeDate = '';
    switch (true) {
        case ($dateDiff->days / 7 >= 5 ) :
            return $relativeDate = $dateDiff->m  . ' ' . get_noun_plural_form($dateDiff->m, 'месяц', 'месяца', 'месяцев') . ' ' . $words; 
        
        case ($dateDiff->days / 7 >= 1 && $dateDiff->days / 7 < 5) :
            return $relativeDate = floor($dateDiff->days / 7) . ' ' . get_noun_plural_form(floor($dateDiff->days / 7), 'неделя', 'недели', 'недель') . ' ' . $words;

        case ($dateDiff->d >= 1 && $dateDiff->d < 7) :
            return $relativeDate = $dateDiff->d . ' ' . get_noun_plural_form($dateDiff->d, 'день', 'дня', 'дней') . ' ' . $words;
        
        case ($dateDiff->h >= 1 && $dateDiff->h < 24) :
            return $relativeDate = $dateDiff->h . ' ' . get_noun_plural_form($dateDiff->h, 'час', 'часа', 'часов') . ' ' . $words;

        case ($dateDiff->i > 0 && $dateDiff->i < 60) :
            return $relativeDate = $dateDiff->i . ' ' . get_noun_plural_form($dateDiff->i, 'минута', 'минуты', 'минут') . ' ' . $words;

        default:
            return $relativeDate = $date->format('d.m.Y H:i');
    }
}