<?php
    require_once 'helpers.php';
    require_once 'functions.php';
    date_default_timezone_set('Europe/Moscow');

    $requiredFields = ['email', 'login', 'password', 'password-repeat'];
    $fieldsHeader = [
        'email' => 'Электронная почта',
        'login' => 'Логин',
        'password' => 'Пароль',
        'password-repeat' => 'Повтор пароля',
        'avatar' => 'Аватар'
    ];
    $formFieldsError = [];

    foreach ($_POST as $fieldName => $fieldValue) {
        if (in_array($fieldName, $requiredFields)) {
            if (validateEmptyFilled($fieldName)) {
                $formFieldsError['registration'][$fieldName] = 'success';
            } else {
                $formFieldsError['registration'][$fieldName] = 'Это поле должно быть заполнено';
            }
            
        }

        if ($fieldName == 'email' && $formFieldsError['registration'][$fieldName] == 'success') {
            if (validateEmail($fieldValue)['result'] == 'success') {
                $formFieldsError['registration'][$fieldName] = 'success';
            } elseif (validateEmail($fieldValue)['result'] == 'db-column-error') {
                $formFieldsError['registration'][$fieldName] = 'Ошибка проверки существования ' . $fieldName;
            } elseif (!empty(validateEmail($fieldValue)['result'] == 'value-exist')) {
                $formFieldsError['registration'][$fieldName] = 'Пользователь с таким email уже зарегистрирован';
            } elseif(!validateEmail($fieldValue)) {
                $formFieldsError['registration'][$fieldName] = 'Укажите корректный формат почты';
            }
        }

        if ($fieldName == 'login' && $formFieldsError['registration'][$fieldName] == 'success') {
            if (validateLogin($fieldValue)['result'] == 'success') {
                $formFieldsError['registration'][$fieldName] = 'success';
            } elseif (validateLogin($fieldValue)['result'] == 'db-column-error') {
                $formFieldsError['registration'][$fieldName] = 'Ошибка проверки существования ' . $fieldName;
            } elseif (validateLogin($fieldValue)['result'] == 'value-exist') {
                $formFieldsError['registration'][$fieldName] = 'занят';  
            } elseif (!validateLogin($fieldValue)['result']) {
                $formFieldsError['registration'][$fieldName] = 'Длина логина должна быть менее 20 символов';
            }         
        }

        if ($fieldName == 'password' && $formFieldsError['registration'][$fieldName] == 'success') {
            if (validatePassword($fieldName, 'password-repeat')) {
                $formFieldsError['registration'][$fieldName] = 'success';
            } else {
                $formFieldsError['registration'][$fieldName] = 'Пароль и его повтор не совпадают';
            }      
        }
    }

    if (isset($_FILES['userpic-file'])) {
        $validateFile = validateUploadedFile($_FILES['userpic-file']);

        if ($validateFile != 'success') {       
            $formFieldsError['registration']['avatar'] = $validateFile;
        } else {
            moveUploadedImg($_FILES['userpic-file'], 'users-avatar/');
        }
    }
    // формируем список ошибок
    $validateErrors = getFormValidateErrors($formFieldsError);

    
    // если ошибок валидации нет, записываем данные в базу
    if (count(array_unique($validateErrors)) === 1 && array_unique($validateErrors)[0] === 'success') {
        $userEmail = (isset($_POST['email'])) ? filter_var($_POST['email'], FILTER_SANITIZE_STRING) : NULL;
        $userLogin = (isset($_POST['login'])) ? filter_var($_POST['login'], FILTER_SANITIZE_STRING) : NULL;
        $userPassword = (isset($_POST['password'])) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : NULL; 
        $userAvatar = (!empty($_FILES['userpic-file']['name'])) ? $_FILES['userpic-file']['name'] : NULL;
  
        $data = [
            $userEmail,
            $userLogin,
            $userPassword,
            $userAvatar
        ];

        $userInsertDBresult = insertNewUser($data);

        if (!$userInsertDBresult) {
            $formFieldsError['registration'] = ['db-error' => 'Произошла ошибка регистрации'];
        } else {
            header('HTTP/1.1 301 Moved Permanently');
            header("Location: /feed.php");
            exit();
        }
    }

    $content = include_template( 'registration-form.php', [
        'postRequestData' => $_POST,
        'formFieldsError' => $formFieldsError,
        'fieldsHeader' => $fieldsHeader
    ]);     
    $layout = include_template( 'layout.php', ['content' => $content]);
    print($layout); 
?>