<?php
    session_start();
    if (isset($_SESSION['user'])) {
        header("Location: /feed.php");
        exit();
    }
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
            if (!verifyUserAuthPassword($_POST['login'], $_POST['password'])) {
                $formFieldsError['authentification'][$fieldName] = 'Пароли не совпадают';
            } else {
                $formFieldsError['authentification'][$fieldName] = 'success';
            }                      
        }
    }

    // формируем список ошибок
    $validateErrors = getFormValidateErrors($formFieldsError);

    // если ошибок нет,открываем сессию
    if (count(array_unique($validateErrors)) === 1 && array_unique($validateErrors)[0] === 'success') {
        session_start();
        $_SESSION['user'] = $_POST['login'];
        header("Location: /feed.php");
    }
    
    $layout = include_template( 'login.php', [
        'postRequestData' => $_POST,
        'formFieldsError' => $formFieldsError,
    ]);
    print($layout); 

