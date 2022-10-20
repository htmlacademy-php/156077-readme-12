<label class="adding-post__label form__label" for="form-heading">Заголовок <span class="form__input-required">*</span></label>
<div class="form__input-section <?= (isset($formFieldsError[$formType]) && isset($formFieldsError[$formType]['form-heading']) && $formFieldsError[$formType]['form-heading'] != 'success') ? 'form__input-section--error' : '';?>">
    <input class="adding-post__input form__input" id="form-heading" type="text" name="form-heading" placeholder="Введите заголовок" value="<?= ($postRequestData['form-heading']) ?: ''; ?>">
    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
    <div class="form__error-text">
    <h3 class="form__error-title">Ошибка!</h3>
    <p class="form__error-desc"><?= $formFieldsError[$formType]['form-heading']; ?></p>
    </div>
</div>