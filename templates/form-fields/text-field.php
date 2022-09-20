<label class="adding-post__label form__label" for="<?= $fieldName; ?>"><?= $label; ?> <span class="form__input-required">*</span></label>
<div class="form__input-section <?= (isset($formFieldsError[$formType]) && isset($formFieldsError[$formType]['post-text']) && $formFieldsError[$formType]['post-text'] != 'success' || isset($formFieldsError[$formType]['quote-text']) && $formFieldsError[$formType]['quote'] != 'success') ? 'form__input-section--error' : '';?>">
    <textarea class="adding-post__textarea form__textarea form__input" name="<?= $fieldName; ?>" id="post-text" placeholder="<?= $placeholder; ?>"><?= ($postRequestData['post-text']) ? $postRequestData['post-text'] : ''; ?></textarea>
    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
    <div class="form__error-text">
    <h3 class="form__error-title">Ошибка!</h3>
    <p class="form__error-desc"><?= $formFieldsError[$formType]['post-text']; ?></p>
    </div>
</div>