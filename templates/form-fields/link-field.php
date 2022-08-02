<label class="adding-post__label form__label" for="<?= $fieldName; ?>"><?= $label?> <?= $required ? '<span class="form__input-required">*</span>' : ''; ?></label>
<div class="form__input-section <?= (isset($formFieldsError[$formType]) && isset($formFieldsError[$formType][$fieldName]) && $formFieldsError[$formType][$fieldName] != 'success') ? 'form__input-section--error' : '';?>">
    <input value="<?= ($postRequestData[$fieldName]) ? $postRequestData[$fieldName] : $postRequestData['form-link']; ?>" class="adding-post__input form__input" id="link-url" type="text" name="<?= $fieldName; ?>" placeholder="Введите ссылку">
    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
    <div class="form__error-text">
    <h3 class="form__error-title">Ошибка!</h3>
    <p class="form__error-desc"><?= $formFieldsError[$formType][$fieldName]; ?></p>
    </div>
</div>