<?php
/**
 * Подключает к базе данных
 * @return string соединение с БД, либо false и выходит из скрипта
 */
function dbConnect() {
    $connectionDB = mysqli_connect("localhost", "user", "Winserus89","readme"); 
    if ($connectionDB  == false) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }

    return $connectionDB;
}

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Формироует строку типов переменных для передачи в подготовленный запрос
 *
 * @param $data Массив переменных
 * @return string строка типов переменных для подготовленного запроса
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
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
    
    $name = 'templates/' . $name;

    $result = '';
    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
 
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Функция проверяет доступно ли видео по ссылке на youtube
 * @param string $url ссылка на видео
 *
 * @return string Ошибку если валидация не прошла
 */
function check_youtube_url($url)
{
    $id = extract_youtube_id($url);

    set_error_handler(function () {}, E_WARNING);
    $headers = get_headers('https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . $id);
    restore_error_handler();

    if (!is_array($headers)) {
        return "Видео по такой ссылке не найдено. Проверьте ссылку на видео";
    }

    $err_flag = strpos($headers[0], '200') ? 200 : 404;

    if ($err_flag !== 200) {
        return "Видео по такой ссылке не найдено. Проверьте ссылку на видео";
    }

    return 'success';
}

/**
 * Возвращает код iframe для вставки youtube видео на страницу
 * @param string $youtube_url Ссылка на youtube видео
 * @return string
 */
function embed_youtube_video($youtube_url)
{
    $res = "";
    $id = extract_youtube_id($youtube_url);

    if ($id) {
        $src = "https://www.youtube.com/embed/" . $id;
        $res = '<iframe width="760" height="400" src="' . $src . '" frameborder="0"></iframe>';
    }

    return $res;
}

/**
 * Возвращает img-тег с обложкой видео для вставки на страницу
 * @param string|null $youtube_url Ссылка на youtube видео
 * @return string
 */
function embed_youtube_cover(string $youtube_url = null)
{
    $res = "";
    $id = extract_youtube_id($youtube_url);

    if ($id) {
        $src = sprintf("https://img.youtube.com/vi/%s/mqdefault.jpg", $id);
        $res = '<img alt="youtube cover" width="320" height="120" src="' . $src . '" />';
    }

    return $res;
}

/**
 * Извлекает из ссылки на youtube видео его уникальный ID
 * @param string $youtube_url Ссылка на youtube видео
 * @return array
 */
function extract_youtube_id($youtube_url)
{
    $id = false;

    $parts = parse_url($youtube_url);

    if ($parts) {
        if ($parts['path'] == '/watch') {
            parse_str($parts['query'], $vars);
            $id = $vars['v'] ?? null;
        } else {
            if ($parts['host'] == 'youtu.be') {
                $id = substr($parts['path'], 1);
            }
        }
    }

    return $id;
}

/**
 * @param $index
 * @return false|string
 */
function generate_random_date($index)
{
    $deltas = [['minutes' => 59], ['hours' => 23], ['days' => 6], ['weeks' => 4], ['months' => 11]];
    $dcnt = count($deltas);

    if ($index < 0) {
        $index = 0;
    }

    if ($index >= $dcnt) {
        $index = $dcnt - 1;
    }

    $delta = $deltas[$index];
    $timeval = rand(1, current($delta));
    $timename = key($delta);

    $ts = strtotime("$timeval $timename ago");
    $dt = date('Y-m-d H:i:s', $ts);

    return $dt;
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

    $readMoreElement = '
        <div class="post-text__more-link-wrapper">
            <a class="post-text__more-link" href="#">Читать далее</a>
        </div>
    ';

    foreach ($words as $word) {
        $symbolsCount += strlen($word);   

        if ($symbolsCount > $cropSybmols) {            
            return htmlspecialchars(implode(' ', $cropWords)) . '...' . $readMoreElement; 
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

/**
 * Делает запись в БД из массива переменных
 * @param [$data [простой массив переменных]
 * @param [$sql] [sql string] [sql запрос]
 * @return {int} id добавленной записи
 */

function insertDBDataFromArray($sql, $data) {

    if (!$data || !is_array($data)) {
        return false;
    } else {
        $varTypes = getVarTypes($data);
        $mysqli = new mysqli("localhost", "user", "Winserus89","readme");
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
 * Записывает в базу данные нового поста
 * @param [$data [простой массив переменных с данными поста]
 * @return {int} id добавленной записи
 */
function insertNewPost($data) {
    $sqlPostPrepare = "INSERT INTO posts (user_id, type_id, header, post_text, quote_author, post_image, post_video, post_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    return insertDBDataFromArray($sqlPostPrepare, $data);
}

/**
 * Записывает в базу данные нового поста
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
 * Получает данные из БД
 * @param [$connection] [запрос на подключение к БД]
 * @param [$sql] [sql string] [sql запрос]
 * @param [$resultsType] [string] [тип данных для возврата]
 * @return {array} массив полученных данных
 */

function getDBData ($connection, $sql, $resultsType) {

    $data = mysqli_query($connection, $sql);
    if ($data) {
        if ($resultsType == 'all') {
            $data = mysqli_fetch_all($data, MYSQLI_ASSOC);
        } elseif ($resultsType == 'single') {
            $data = mysqli_fetch_assoc($data);
        }
    }
    
    return $data;      
}

/**
 * Получает id типа поста по его имени
 * @param [$postTypeName] [string] [название типа поста]
 * @return {int} id типа поста
 */

function getPostTypeIdByName($postTypeName) {
    $connectionDB = dbConnect();

    $sql = "SELECT id FROM post_types WHERE name = '$postTypeName'";
    return getDBData($connectionDB, $sql, 'single')['id'];
}

/**
 * Получает id хештэга по его имени
 * @param [$tag] [string] [название хештега]
 * @return {int} id хештега
 */
function getHashtagIdByName($tag) {
    $connectionDB = dbConnect();

    $sqlTagId = "SELECT id FROM hashtags WHERE hashtag = '$tag'";
    return getDBData($connectionDB, $sqlTagId, 'single')['id'];
}

/**
 * Получает данные поста по его id
 * @param [$postId] [int] [id поста]
 * @return {array} массив данных поста
 */
function getPostData($postId) {
    $connectionDB = dbConnect();

    $sqlGetPostData = "SELECT posts.*, post_types.name as type_name, users.avatar, users.register_date FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id WHERE posts.id = $postId";
    return getDBData($connectionDB, $sqlGetPostData, 'single');
}

/**
 * Получает данные всех постов
 * @param [$postsTypeID] [int] [id типа поста для фильтрации]
 * @return {array} массив данных постов
 */
function getPosts($postsTypeID = NULL) {
    $connectionDB = dbConnect();
    if ($postsTypeID != NULL) {
        $condition = 'WHERE posts.type_id =' . $postsTypeID;
    } else {
        $condition = '';
    }
    $sqlGetPosts = "SELECT posts.*, post_types.name as type_name, users.avatar FROM posts LEFT JOIN post_types ON post_types.id = posts.type_id LEFT JOIN users ON users.id = posts.user_id $condition ORDER BY posts.views_count DESC";
    return getDBData($connectionDB, $sqlGetPosts, 'all');
}

/**
 * Подсчитывает количество записей в переданной таблице по переданному столбцу
 * @param [$dataCount] [all] [значение столбца таблицы для подсчета]
 * @param [$dataCol] [string] [столбец таблицы]
 * @param [$table] [string] [название таблицы]
 * @return {int} количество записей с [$dataCount]
 */

function getDBDataCount ($dataCount, $dataCol, $table) {
    $connectionDB = dbConnect(); 

    $sql = "SELECT COUNT(*) as count FROM $table WHERE $dataCol = $dataCount";
    $result = getDBData($connectionDB, $sql, 'single');

    return $result['count'];
}

/**
 * Получает список типов постов
 * @return {array} массив типов постов
 */

function getPostTypes() {
    $sqlGetPostTypes = "SELECT * FROM post_types";
    $postTypes = getDBData(dbConnect(), $sqlGetPostTypes, 'all');

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
 * @return {none}
 */
function moveUploadedImg($file) {

    $file_name = $file['name'];
    $file_path = __DIR__ . '/uploads/';
    $file_url = '/uploads/' . $file_name;

    move_uploaded_file($file['tmp_name'], $file_path . $file_name);
}

/**
 * Валидация на пустоту
 * @param [$name] [string] [имя поля]
 * @return {string} Результат проверки
 */

function validateEmptyFilled($name) {
    if ($_POST[$name] == '') {
        return 'Это поле должно быть заполнено';
    } else {
        return 'success';
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
        return 'Укажите корректную ссылку';
    } else {
        return 'success';
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
    $fileType = $_FILES['userpic-file-photo']['type'];
    $fileSize = $_FILES['userpic-file-photo']['size'];

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
            return $tag . '- некорректный тег. Допустимы только русские или английские строчные символы. Разделяйте теги пробелом';
        }         
    }

    return 'success';
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

    return htmlspecialchars($imgPath);
}
