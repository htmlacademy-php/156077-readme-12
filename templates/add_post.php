<main class="page__main page__main--adding-post">
      <div class="page__main-section">
        <div class="container">
          <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
          <div class="adding-post__tabs-wrapper tabs">
            <div class="adding-post__tabs filters">
              <ul class="adding-post__tabs-list filters__list tabs__list">
                <?php if ($postTypes) : ?>
                    <?php foreach ($postTypes as $postType) : ?>
                        <?php if ($postType['name'] == 'post-photo') : ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a data-type="adding-post-photo" class="js-adding-post-tab adding-post__tabs-link filters__button filters__button--photo <?= !isset($activeContentType) ? 'filters__button--active' : ''; ?> <?= ($activeContentType == 'post-photo') ? 'filters__button--active' : '';?> tabs__item tabs__item--active button">
                                    <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-photo"></use>
                                    </svg>
                                    <span><?= $postType['alter_name']; ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($postType['name'] == 'post-video') : ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a data-type="adding-post-video" class="js-adding-post-tab adding-post__tabs-link filters__button filters__button--video <?= ($activeContentType == 'post-video') ? 'filters__button--active' : '';?> tabs__item button" href="#">
                                    <svg class="filters__icon" width="24" height="16">
                                    <use xlink:href="#icon-filter-video"></use>
                                    </svg>
                                    <span><?= $postType['alter_name']; ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($postType['name'] == 'post-text') : ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a data-type="adding-post-text" class="js-adding-post-tab adding-post__tabs-link filters__button filters__button--text <?= ($activeContentType == 'post-text') ? 'filters__button--active' : '';?> tabs__item button" href="#">
                                    <svg class="filters__icon" width="20" height="21">
                                    <use xlink:href="#icon-filter-text"></use>
                                    </svg>
                                    <span><?= $postType['alter_name']; ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($postType['name'] == 'post-quote') : ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a data-type="adding-post-quote" class="js-adding-post-tab adding-post__tabs-link filters__button filters__button--quote <?= ($activeContentType == 'post-quote') ? 'filters__button--active' : '';?> tabs__item button" href="#">
                                    <svg class="filters__icon" width="21" height="20">
                                    <use xlink:href="#icon-filter-quote"></use>
                                    </svg>
                                    <span><?= $postType['alter_name']; ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($postType['name'] == 'post-link') : ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a data-type="adding-post-link" class="js-adding-post-tab adding-post__tabs-link filters__button filters__button--link <?= ($activeContentType == 'post-link') ? 'filters__button--active' : '';?> tabs__item button" href="#">
                                    <svg class="filters__icon" width="21" height="18">
                                    <use xlink:href="#icon-filter-link"></use>
                                    </svg>
                                    <span><?= $postType['alter_name']; ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
              </ul>
            </div>
            <div class="adding-post__tab-content">
              <section data-type="adding-post-photo" class="js-adding-post-form adding-post__photo tabs__content <?= !isset($activeContentType) ? 'tabs__content--active' : ''; ?> <?= ($activeContentType == 'post-photo') ? 'tabs__content--active' : '';?>">
                <h2 class="visually-hidden">Форма добавления фото</h2>
                <form class="adding-post__form form" action="add.php" method="post" enctype="multipart/form-data">
                  <div class="form__text-inputs-wrapper">
                    <div class="form__text-inputs">
                    <input type="hidden" value="post-photo" name="active-content-type">
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/heading-field.php', ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-photo'])); ?>
                      </div>
                      <div class="adding-post__input-wrapper form__input-wrapper">
                       <?php print(include_template('form-fields/link-field.php', ['label' => 'Ссылка из интернета', 'fieldName' => 'photo-link', 'postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-photo'])); ?>
                      </div>
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/tags-field.php', ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-photo'])); ?>
                      </div>
                    </div>
                    <?php print(include_template('form-fields/error-block.php', ['formFieldsError' => $formFieldsError, 'formType' => 'post-photo', 'fieldsHeader' => $fieldsHeader])); ?>
                  </div>
                  <div class="adding-post__input-file-container form__input-container form__input-container--file">
                    <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                      <input class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button" id="userpic-file-photo" type="file" name="userpic-file-photo" title=" ">
                    </div>
                    <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">

                    </div>
                  </div>
                  <?php print(include_template('form-fields/submit-button.php')); ?>
                </form>
              </section>

              <section data-type="adding-post-video" class="js-adding-post-form adding-post__video tabs__content <?= ($activeContentType == 'post-video') ? 'tabs__content--active' : '';?>">
                <h2 class="visually-hidden">Форма добавления видео</h2>
                <form class="adding-post__form form" action="add.php" method="post" enctype="multipart/form-data">
                  <div class="form__text-inputs-wrapper">
                    <div class="form__text-inputs">
                      <input type="hidden" value="post-video" name="active-content-type">
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/heading-field.php', ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-video'])); ?>
                      </div>
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/link-field.php', ['label' => 'Ссылка YouTube', 'required' => true, 'fieldName' => 'video-link', 'postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-video'])); ?>
                      </div>
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/tags-field.php',  ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-video'])); ?>
                      </div>
                    </div>
                    <?php print(include_template('form-fields/error-block.php', ['formFieldsError' => $formFieldsError, 'formType' => 'post-video', 'fieldsHeader' => $fieldsHeader])); ?>
                  </div>

                  <?php print(include_template('form-fields/submit-button.php')); ?>
                </form>
              </section>

              <section data-type="adding-post-text" class="js-adding-post-form adding-post__text tabs__content <?= ($activeContentType == 'post-text') ? 'tabs__content--active' : '';?>">
                <h2 class="visually-hidden">Форма добавления текста</h2>
                <form class="adding-post__form form" action="add.php" method="post">
                  <div class="form__text-inputs-wrapper">
                    <div class="form__text-inputs">
                    <input type="hidden" value="post-text" name="active-content-type">
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/heading-field.php',  ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-text'])); ?>
                      </div>
                      <div class="adding-post__textarea-wrapper form__textarea-wrapper">
                        <?php print(include_template('form-fields/text-field.php', ['label' => 'Текст поста', 'fieldName' => 'post-text', 'placeholder' => 'Введите текст публикации', 'postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-text'])); ?>
                      </div>
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/tags-field.php', ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-text'])); ?>
                      </div>
                    </div>
                    <?php print(include_template('form-fields/error-block.php', ['formFieldsError' => $formFieldsError, 'formType' => 'post-text', 'fieldsHeader' => $fieldsHeader])); ?>
                  </div>
                  <?php print(include_template('form-fields/submit-button.php')); ?>
                </form>
              </section>

              <section data-type="adding-post-quote" class="js-adding-post-form adding-post__quote tabs__content <?= ($activeContentType == 'post-quote') ? 'tabs__content--active' : '';?>">
                <h2 class="visually-hidden">Форма добавления цитаты</h2>
                <form class="adding-post__form form" action="add.php" method="post">
                  <div class="form__text-inputs-wrapper">
                    <div class="form__text-inputs">
                    <input type="hidden" value="post-quote" name="active-content-type">
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/heading-field.php', ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-quote'])); ?>
                      </div>
                      <div class="adding-post__input-wrapper form__textarea-wrapper">
                        <?php print(include_template('form-fields/text-field.php', ['label' => 'Текст цитаты', 'fieldName' => 'quote-text', 'placeholder' => 'Текст цитаты', 'postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-quote'])); ?>
                      </div>
                      <div class="adding-post__textarea-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/author.php', ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-quote'])); ?>
                      </div>
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/tags-field.php', ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-quote'])); ?>
                      </div>
                    </div>
                    <?php print(include_template('form-fields/error-block.php', ['formFieldsError' => $formFieldsError, 'formType' => 'post-quote', 'fieldsHeader' => $fieldsHeader])); ?>
                  </div>
                  <?php print(include_template('form-fields/submit-button.php')); ?>
                </form>
              </section>

              <section data-type="adding-post-link" class="js-adding-post-form adding-post__link tabs__content <?= ($activeContentType == 'post-link') ? 'tabs__content--active' : '';?>">
                <h2 class="visually-hidden">Форма добавления ссылки</h2>
                <form class="adding-post__form form" action="add.php" method="post">
                  <div class="form__text-inputs-wrapper">
                    <div class="form__text-inputs">
                      <input type="hidden" value="post-link" name="active-content-type">
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/heading-field.php', ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-link'])); ?>
                      </div>
                      <div class="adding-post__textarea-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/link-field.php', ['label' => 'Ссылка', 'required' => true, 'fieldName' => 'form-link', 'postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-link'])); ?>
                      </div>
                      <div class="adding-post__input-wrapper form__input-wrapper">
                        <?php print(include_template('form-fields/tags-field.php', ['postRequestData' => $postRequestData, 'formFieldsError' => $formFieldsError, 'formType' => 'post-link'])); ?>
                      </div>
                    </div>
                    <?php print(include_template('form-fields/error-block.php', ['formFieldsError' => $formFieldsError, 'formType' => 'post-link', 'fieldsHeader' => $fieldsHeader])); ?>
                  </div>
                  <?php print(include_template('form-fields/submit-button.php')); ?>
                </form>
              </section>
            </div>
          </div>
        </div>
      </div>
    </main>