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
    
    $requiredFields = ['form-heading', 'post-text', 'quote-text', 'quote-author', 'form-link', 'video-link'];
    $fieldsHeader = [
        'form-heading' => 'Заголовок',
        'post-text' => 'Текст поста',
        'quote-text' => 'Текст цитаты',
        'quote-author' => 'Автор',
        'form-link' => 'Ссылка',
        'video-link' => 'Ссылка YouTube',
        'photo-link' => 'Ссылка из интернета',
        'form-tags' => 'Теги',
        'userpic-file-photo' => 'Изображение'
    ];
    $formFieldsError = [];
    
    // валидация полей
    foreach ($_POST as $fieldName => $fieldValue) {
        //валидация на заполненность
        if (in_array($fieldName, $requiredFields)) {
            if (validateEmptyFilled($fieldName)) {
                $formFieldsError[$_POST['active-content-type']][$fieldName] = 'success';
            } else {
                $formFieldsError[$_POST['active-content-type']][$fieldName] = 'Это поле должно быть заполнено';
            }
        }
        // валидация на корректность ссылки
        if ($fieldName == 'form-link' || $fieldName == 'video-link') {   
            if (validateLink($fieldName)) {
                $formFieldsError[$_POST['active-content-type']][$fieldName] = 'success';
            } else {
                $formFieldsError[$_POST['active-content-type']][$fieldName] = 'Укажите корректный формат ссылки';
            }
           
            if ($formFieldsError[$_POST['active-content-type']][$fieldName] == 'success' && $fieldName == 'video-link') {
                if (check_youtube_url($_POST[$fieldName])) {
                    $formFieldsError[$_POST['active-content-type']][$fieldName] = 'success';
                } else {
                    $formFieldsError[$_POST['active-content-type']][$fieldName] = 'Видео по ссылке не найдено';
                }  
    
            }
        }
        // валидация фото-ссылки
        if ($fieldName == 'photo-link' && !empty($fieldValue)) {
            if (validateLink($fieldName)) {
                $formFieldsError[$_POST['active-content-type']][$fieldName] = 'success';
            } else {
                $formFieldsError[$_POST['active-content-type']][$fieldName] = 'Укажите корректный формат ссылки';
            }
            
            if ($formFieldsError[$_POST['active-content-type']][$fieldName] == 'success' && $_FILES['userpic-file-photo']['size'] == 0) {
                if (downloadImageLink($fieldName)) {
                    $formFieldsError[$_POST['active-content-type']][$fieldName] = 'success';
                } else {
                    $formFieldsError[$_POST['active-content-type']][$fieldName] = 'Изображение по ссылке не найдено';
                }
            }              
        }
        // валидация тегов
        if ($fieldName == 'form-tags' && !empty($fieldValue)) {
            if (validateTags($fieldValue)) {
                $formFieldsError[$_POST['active-content-type']][$fieldName] = 'success';
            } else {
                $formFieldsError[$_POST['active-content-type']][$fieldName] = 'Некорректные теги. Допустимы только русские или английские строчные символы. Разделяйте теги пробелом';
            }
        }
    }
    // добавление загружаемого фото
    if (isset($_FILES['userpic-file-photo'])) {
        $validateFile = validateUploadedFile($_FILES['userpic-file-photo']);

        if ($validateFile != 'success' && empty($_POST['photo-link'])) {       
            $formFieldsError[$_POST['active-content-type']]['userpic-file-photo'] = $validateFile;
        } elseif ($validateFile == 'success') {
            moveUploadedImg($_FILES['userpic-file-photo']);
            $formFieldsError[$_POST['active-content-type']]['userpic-file-photo'] = $validateFile;
        }
    }
    // формируем список ошибок
    $validateErrors = getFormValidateErrors($formFieldsError);

    // если ошибок валидации нет, записываем данные в базу
    if (count(array_unique($validateErrors)) === 1 && array_unique($validateErrors)[0] === 'success') {
        
        $postTypeName = (isset($_POST['active-content-type'])) ? filter_var($_POST['active-content-type'], FILTER_SANITIZE_STRING) : null;
        $postHeader = (isset($_POST['form-heading'])) ? filter_var($_POST['form-heading'], FILTER_SANITIZE_STRING) : null;
        $postText = (isset($_POST['post-text'])) ? filter_var($_POST['post-text'], FILTER_SANITIZE_STRING) : null;
        if (isset($_POST['quote-text'])) {
            $postText = filter_var($_POST['quote-text'], FILTER_SANITIZE_STRING);
        }
        $postAuthor = (isset($_POST['quote-author'])) ? filter_var($_POST['quote-author'], FILTER_SANITIZE_STRING) : null;
        $postLink = (isset($_POST['form-link'])) ? filter_var($_POST['form-link'], FILTER_SANITIZE_URL) : null;
        $postVideo = (isset($_POST['video-link'])) ? filter_var($_POST['video-link'], FILTER_SANITIZE_URL) : null;
        $postImg = (!empty($_FILES['userpic-file-photo']['name'])) ? $_FILES['userpic-file-photo']['name'] : null;
        if (empty($_FILES['userpic-file-photo']['name']) && !empty($_POST['photo-link'])) {
            $postImg = 'link-images/' . downloadImageLink('photo-link', true);
        }
        $postTypeId = getPostTypeIdByName($postTypeName);
        $userId = (int)getUserDataByLogin($_SESSION['user'])['id'];

        $data = [
            $userId,
            $postTypeId,
            $postHeader,
            $postText,
            $postAuthor,
            $postImg,
            $postVideo,
            $postLink
        ];
            
        $postInsertDBresult = insertNewPost($data);
        if (!$postInsertDBresult) {
            $formFieldsError[$_POST['active-content-type']] = ['db-error' => 'Произошла ошибка при добавлении нового поста'];
        }
        // если пост добавлен в базу, добавляем хештеги при наличии
        if ($postInsertDBresult) {

            $tags = (isset($_POST['form-tags'])) ? explode(' ', filter_var($_POST['form-tags'], FILTER_SANITIZE_STRING)) : null;

            if (!empty($_POST['form-tags'])) {
                foreach ($tags as $tagIndex => $tag) {
                    $data = [$tag];
                    $tagInsertDBresult = insertPostTags($data, 'hashtags');
                    // добавляем новые теги или ищем в списке добавленных
                    if ($tagInsertDBresult) {
                        $data = [$postInsertDBresult, $tagInsertDBresult];
                        insertPostTags($data, 'hashtags_post');
                    } else {
                        $DBtagId = getHashtagIdByName($tag);
                        $data = [$postInsertDBresult, $DBtagId];
                        insertPostTags($data, 'hashtags_post');
                    }
                }
            }
            // перенаправялем на страницу нового поста
            header('HTTP/1.1 301 Moved Permanently');
            header("Location: http://156077-readme-12/post.php?post_id=" . $postInsertDBresult);
            exit();
        }
    }
    
    $content = include_template( 'add_post.php', [
        'postTypes' => $postTypes, 
        'postData' => $_POST, 
        'activeContentType' => $_POST['active-content-type'],
        'postRequestData' => $_POST,
        'formFieldsError' => $formFieldsError,
        'fieldsHeader' => $fieldsHeader
    ]);     
    $layout = include_template( 'layout.php', ['content' => $content]);
    print($layout); 
