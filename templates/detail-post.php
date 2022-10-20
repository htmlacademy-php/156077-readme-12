<main class="page__main page__main--publication">
  <div class="container">
    <h1 class="page__title page__title--publication"><?= $postData['header']; ?></h1>
    <section class="post-details">
      <h2 class="visually-hidden">Публикация</h2>
      <div class="post-details__wrapper post-photo">
        <div class="post-details__main-block post post--details">
           
            <?php if ($postData['type_name'] == 'post-quote') : ?>

                <div class="post-details__image-wrapper post-quote">
                    <div class="post__main">
                        <blockquote>
                        <p>
                            <?= htmlspecialchars($postData['post_text']); ?>
                        </p>
                        <cite><?= htmlspecialchars($postData['quote_author']); ?></cite>
                        </blockquote>
                    </div>
                </div>

            <?php elseif ($postData['type_name'] == 'post-text') : ?>
                <!-- пост-текст -->
                <div class="post-details__image-wrapper post-text">
                    <div class="post__main">
                        <p>
                            <?= htmlspecialchars($postData['post_text']); ?>
                        </p>
                    </div>
                </div>
            
            <?php elseif ($postData['type_name'] == 'post-link') : ?>
                <!-- пост-ссылка -->
                <div class="post__main">
                    <div class="post-link__wrapper">
                        <a class="post-link__external" href="http://<?= $postData['post_link']; ?>" title="Перейти по ссылке">
                            <div class="post-link__info-wrapper">
                                <div class="post-link__icon-wrapper">
                                  <img src="https://www.google.com/s2/favicons?domain=<?= htmlspecialchars($postData['post_link']); ?>" alt="Иконка">
                                </div>
                                <div class="post-link__info">
                                <h3><?= htmlspecialchars($postData['post_link']); ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            
            <?php elseif ($postData['type_name'] == 'post-photo') : ?>

                <!-- пост-изображение -->
                <div class="post-details__image-wrapper post-photo__image-wrapper">
                    <img src="<?= checkFilePath($postData['post_image']); ?>" alt="Фото от пользователя" width="760" height="507">
                </div>
            
            <?php elseif ($postData['type_name'] == 'post-video') : ?>
                <!-- пост-видео -->
                <div class="post-details__image-wrapper post-photo__image-wrapper">
                    <?=embed_youtube_video($postData['post_video']); ?>
                </div>
            
            <?php endif; ?>

          <div class="post__indicators">
            <div class="post__buttons">
              <a class="post__indicator post__indicator--likes button" href="/like.php?post_id=<?= $postData['id']; ?>" title="Лайк">
                <svg class="post__indicator-icon" width="20" height="17">
                  <use xlink:href="#icon-heart"></use>
                </svg>
                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                  <use xlink:href="#icon-heart-active"></use>
                </svg>
                <span><?= getDBDataCount($postData['id'], 'post_id', 'likes'); ?></span>
                <span class="visually-hidden">количество лайков</span>
              </a>
              <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                <svg class="post__indicator-icon" width="19" height="17">
                  <use xlink:href="#icon-comment"></use>
                </svg>
                <span><?= getDBDataCount($postData['id'], 'post_id', 'comments'); ?></span>
                <span class="visually-hidden">количество комментариев</span>
              </a>
              <a class="post__indicator post__indicator--repost button" href="/repost.php?post_id=<?= $postData['id'];?>" title="Репост">
                <svg class="post__indicator-icon" width="19" height="17">
                  <use xlink:href="#icon-repost"></use>
                </svg>
                <span><?= $postData['repost_count']; ?></span>
                <span class="visually-hidden">количество репостов</span>
              </a>
            </div>
            
            <span class="post__view"><?= $postData['views_count'];?> <?= ($postData['views_count']) ? get_noun_plural_form($postData['views_count'], 'просмотр', 'просмотра', 'просмотров') : ''; ?></span>
          </div>
          <ul class="post__tags">
            <?php if (!empty(getHashtags($postData['id']))) : ?>
                <ul class="post__tags">
                    <?php foreach (getHashtags($postData['id']) as $tagIndex => $tag) : ?>      
                        <li><a href="/search.php?search-phrase=<?= 'tag-' . $tag['hashtag'];?>">#<?= htmlspecialchars($tag['hashtag']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
          </ul>
          <div class="comments">
            <?php print(include_template('comment-form.php', [
              'userName' => $userName, 
              'formError' => $formFieldsError, 
              'postId' => $postData['id']])
            ); ?>
            <div class="comments__list-wrapper">
              <ul class="comments__list">
                <?php if (!empty($comments)) : ?>
                  <?php foreach($comments as $commentIndex => $comment) : ?>
                    <li class="comments__item user">
                      <div class="comments__avatar">
                        <a class="user__avatar-link" href="/profile.php?user=<?= $comment['login']; ?>">
                          <img class="comments__picture" src="<?= checkFilePath($comment['avatar']); ?>" alt="Аватар пользователя">
                        </a>
                      </div>
                      <div class="comments__info">
                        <div class="comments__name-wrapper">
                          <a class="comments__user-name" href="/profile.php?user=<?= $comment['login']; ?>">
                            <span><?= htmlspecialchars($comment['login']);?></span>
                          </a>
                          <time class="comments__time" datetime="<?= $comment['create_date']; ?>"><?= getRelativeDateDifference(new DateTime($comment['create_date']), 'назад'); ?></time>
                        </div>
                        <p class="comments__text">
                          <?= htmlspecialchars($comment['comment']); ?>
                        </p>
                      </div>
                    </li>
                  <?php endforeach; ?>
                <?php endif; ?>
              </ul>
              <a class="comments__more-link" href="#">
                <span>Показать все комментарии</span>
                <sup class="comments__amount">45</sup>
              </a>
            </div>
          </div>
        </div>
        <div class="post-details__user user">
          <div class="post-details__user-info user__info">
            <div class="post-details__avatar user__avatar">
              <a class="post-details__avatar-link user__avatar-link" href="#">
                <img class="post-details__picture user__picture" src="<?= checkFilePath($postData['avatar']); ?>" alt="Аватар пользователя">
              </a>
            </div>
            <div class="post-details__name-wrapper user__name-wrapper">
              <a class="post-details__name user__name" href="/profile.php?user=<?= $postData['login']; ?>">
                <span><?= htmlspecialchars($postData['login']); ?></span>
              </a>

              <time class="post-details__time user__time" datetime="<?= $postData['register_date']; ?>"><?= getRelativeDateDifference(new DateTime($postData['register_date']), 'на сайте'); ?></time>
            </div>
          </div>
          <div class="post-details__rating user__rating">
            <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
              <span class="post-details__rating-amount user__rating-amount"><?= getDBDataCount($postData['user_id'], 'subscribed_user_id', 'subscribes'); ?></span>
              <span class="post-details__rating-text user__rating-text"><?= get_noun_plural_form(getDBDataCount($postData['user_id'], 'subscribed_user_id', 'subscribes'), 'подписчик', 'подписчика', 'подписчиков'); ?></span>
            </p>
            <p class="post-details__rating-item user__rating-item user__rating-item--publications">
              <span class="post-details__rating-amount user__rating-amount"><?= getDBDataCount($postData['user_id'], 'user_id', 'posts'); ?></span>
              <span class="post-details__rating-text user__rating-text"><?= get_noun_plural_form(getDBDataCount($postData['user_id'], 'user_id', 'posts'), 'публикация', 'публикации', 'публикаций'); ?></span>
            </p>
          </div>
          <div class="post-details__user-buttons user__buttons">
            <a href="/profile.php?subscribe_user=<?= $postData['user_id']; ?>" class="user__button user__button--subscription button button--main" >Подписаться</a>
            <a class="user__button user__button--writing button button--green" href="#">Сообщение</a>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>