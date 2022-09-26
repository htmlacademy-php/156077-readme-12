<?php
    require_once 'helpers.php';
    date_default_timezone_set('Europe/Moscow');
    $connectionDB = dbConnect();

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
            $formFieldsError['registration'][$fieldName] = validateEmptyFilled($fieldName);
        }

        if ($fieldName == 'email' && $formFieldsError['registration'][$fieldName] == 'success') {
            $formFieldsError['registration'][$fieldName] = validateEmail($fieldName); 
            
            if ($formFieldsError['registration'][$fieldName] == 'success') {
                $formFieldsError['registration'][$fieldName] = checkUserByEmail($fieldValue); 
            }
        }

        if ($fieldName == 'login' && $formFieldsError['registration'][$fieldName] == 'success') {
            $formFieldsError['registration'][$fieldName] = checkUserLogin($fieldValue);           
        }

        if ($fieldName == 'password' && $formFieldsError['registration'][$fieldName] == 'success') {
            $formFieldsError['registration'][$fieldName] = validatePassword($fieldName, 'password-repeat');            
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
    if (count(array_unique($validateErrors)) == 1 && array_unique($validateErrors)[0] == 'success') {
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
            header("Location: http://156077-readme-12");
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