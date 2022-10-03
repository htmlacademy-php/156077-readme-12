<?php
    require_once 'helpers.php';
    require_once 'functions.php';
    date_default_timezone_set('Europe/Moscow');
    $requiredFields = ['login', 'password'];
    $formFieldsError = [];

    foreach ($_POST as $fieldName => $fieldValue) {
        //валидация на заполненность
        if (in_array($fieldName, $requiredFields)) {
            if (validateEmptyFilled($fieldName)) {
                $formFieldsError['authentification'][$fieldName] = 'success';
            } else {
                $formFieldsError['authentification'][$fieldName] = 'Это поле должно быть заполнено';
            }
        }

        if ($fieldName == 'login' && $formFieldsError['authentification'][$fieldName] == 'success') {
            if (checkDBUserData('login', $fieldValue)['result'] == 'success') {
                $formFieldsError['authentification'][$fieldName] = 'Неверный логин';
            } elseif (validateLogin($fieldValue)['result'] == 'value-exist') {
                $formFieldsError['authentification'][$fieldName] = 'success';
            }     
        }

        if ($fieldName == 'password' && $formFieldsError['authentification'][$fieldName] == 'success') {
            if (getUserPassword($_POST['login'])) {
                if (!password_verify($fieldValue, getUserPassword($_POST['login']))) {
                    $formFieldsError['authentification'][$fieldName] = 'Пароли не совпадают';
                } else {
                    $formFieldsError['authentification'][$fieldName] = 'success';
                }   
            } else {
                $formFieldsError['authentification'][$fieldName] = 'Пароль не найден для пользователя ' . $_POST['login'];
            }
             
        }
    }
    var_dump($formFieldsError);

    // формируем список ошибок
    $validateErrors = getFormValidateErrors($formFieldsError);

    // если ошибок нет,открываем сессию
    if (count(array_unique($validateErrors)) == 1 && array_unique($validateErrors)[0] == 'success') {
        session_start();
        $_SESSION['user'] = $_POST['login'];
        header("Location: http://156077-readme-12/feed.php");
    }
    
    $layout = include_template( 'login.php', [
        'postRequestData' => $_POST,
        'formFieldsError' => $formFieldsError,
    ]);
    print($layout); 
?>
