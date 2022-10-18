<form class="comments__form form" action="#" method="post">
    <div class="comments__my-avatar">
    <img class="comments__picture" src="<?= checkFilePath(getUserDataByLogin($userName)['avatar']); ?>" alt="Аватар пользователя">
    </div>
    <div class="form__input-section <?= (!empty($formError) && $formError['comment-form']['comment-text'] != 'success') ? 'form__input-section--error' : ''; ?>">
    <textarea name="comment-text" class="comments__textarea form__textarea form__input" placeholder="Ваш комментарий"></textarea>
    <label class="visually-hidden">Ваш комментарий</label>
    <?php if (!empty($formError) && $formError['comment-form']['comment-text'] != 'success') : ?>
        <button class="form__error-button button" type="button">!</button>
        <div class="form__error-text">
            <h3 class="form__error-title">Ошибка валидации</h3>
            <p class="form__error-desc"><?= $formError['comment-form']['comment-text']; ?></p>
        </div>
    <?php endif; ?>
    </div>
    <input hidden type="text" name="post-id" value="<?= $postId; ?>">
    <button class="comments__submit button button--green" type="submit">Отправить</button>
</form>