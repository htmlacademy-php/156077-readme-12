<div class="form__invalid-block <?= (!isset($formFieldsError[$formType])) ? 'visually-hidden' : ''; ?>">
    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
    <ul class="form__invalid-list">
        <?php if ($formFieldsError[$formType]) : ?>
            <?php foreach ($formFieldsError[$formType] as $errorFieldName => $error) : ?>
                <?php if ($error == 'success') continue; ?>
                <li class="form__invalid-item"><?= $fieldsHeader[$errorFieldName] . ': ' . $error; ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>