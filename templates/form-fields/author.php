<label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
<div class="form__input-section <?= (isset($formFieldsError[$formType]) && isset($formFieldsError[$formType]['quote-author']) && $formFieldsError[$formType]['quote-author'] != 'success') ? 'form__input-section--error' : '';?>">
    <input value="<?= ($postRequestData['quote-author']) ? $postRequestData['quote-author'] : ''; ?>" class="adding-post__input form__input" id="quote-author" type="text" name="quote-author">
    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
    <div class="form__error-text">
    <h3 class="form__error-title">Ошибка!</h3>
    <p class="form__error-desc"><?= $formFieldsError[$formType]['quote-author']; ?></p>
    </div>
</div>