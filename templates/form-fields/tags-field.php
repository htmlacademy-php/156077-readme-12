<label class="adding-post__label form__label" for="form-tags">Теги</label>
<div class="form__input-section <?= (isset($formFieldsError[$formType]) && isset($formFieldsError[$formType]['form-tags']) && $formFieldsError[$formType]['form-tags'] != 'success') ? 'form__input-section--error' : '';?>">
    <input value="<?= ($postRequestData['form-tags']) ? $postRequestData['form-tags'] : ''; ?>" class="adding-post__input form__input" id="form-tags" type="text" name="form-tags" placeholder="Введите теги">
    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
    <div class="form__error-text">
    <h3 class="form__error-title">Ошибка!</h3>
    <p class="form__error-desc"><?= $formFieldsError[$formType]['form-tags']; ?></p>
    </div>
</div>