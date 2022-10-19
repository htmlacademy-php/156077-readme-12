<main class="page__main page__main--profile">
  <h1 class="visually-hidden">Профиль</h1>
  <div class="profile profile--default">
    <div class="profile__user-wrapper">
      <div class="profile__user user container">
        <div class="profile__user-info user__info">
          <div class="profile__avatar user__avatar">
            <img class="profile__picture user__picture" src="<?= checkFilePath($userData['avatar']); ?>" alt="Аватар пользователя">
          </div>
          <div class="profile__name-wrapper user__name-wrapper">
            <span class="profile__name user__name"><?= $userData['login']; ?></span>
            <time class="profile__user-time user__time"><?= getRelativeDateDifference(new DateTime($userData['register_date']), 'на сайте'); ?></time>
          </div>
        </div>
        <div class="profile__rating user__rating">
          <p class="profile__rating-item user__rating-item user__rating-item--publications">
            <span class="user__rating-amount"><?= getDBDataCount($userData['id'], 'user_id', 'posts'); ?></span>
            <span class="profile__rating-text user__rating-text"><?= get_noun_plural_form((int)getDBDataCount($userData['id'], 'user_id', 'posts'), 'публикация', 'публикации', 'публикаций'); ?></span>
          </p>
          <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
            <span class="user__rating-amount"><?= getDBDataCount($userData['id'], 'subscribed_user_id', 'subscribes'); ?></span>
            <span class="profile__rating-text user__rating-text"><?= get_noun_plural_form((int)getDBDataCount($userData['id'], 'subscribed_user_id', 'subscribes'), 'подписчик', 'подписчика', 'подписчиков'); ?></span>
          </p>
        </div>
          <div class="profile__user-buttons user__buttons">
          <?php if (!empty(getQueryParam('user'))) : ?>
              <a href="/profile.php?subscribe_user=<?= $userData['id'];?><?= (!empty(getQueryParam('user'))) ? '&user=' . getQueryParam('user') : ''; ?>" class="profile__user-button user__button user__button--subscription button button--main">Подписаться</a>
                <?php if ($subscribeNotice) : ?>
                    <p><?= $subscribeNotice; ?></p>
                <?php endif; ?>
              <a class="profile__user-button user__button user__button--writing button button--green" href="#">Сообщение</a>
            <?php endif; ?>
          </div>
      </div>
    </div>
    <div class="profile__tabs-wrapper tabs">
      <div class="container">
        <div class="profile__tabs filters">
          <b class="profile__tabs-caption filters__caption">Показать:</b>
          <ul class="profile__tabs-list filters__list tabs__list">
            <li class="profile__tabs-item filters__item">
              <a data-type="post" class="profile__tabs-link filters__button filters__button--active tabs__item tabs__item--active button js-tabs-item">Посты</a>
            </li>
            <li class="profile__tabs-item filters__item">
              <a data-type="likes" class="profile__tabs-link filters__button tabs__item button js-tabs-item" href="#">Лайки</a>
            </li>
            <li class="profile__tabs-item filters__item">
              <a data-type="subscribes" class="profile__tabs-link filters__button tabs__item button js-tabs-item" href="#">Подписки</a>
            </li>
          </ul>
        </div>
        <div class="profile__tab-content">
          <section data-type="post" class="profile__posts tabs__content tabs__content--active js-profile-tab-content">
            <h2 class="visually-hidden">Публикации</h2>
            <?php foreach ($postsData as $postIndex => $post) : ?>
                <article class="profile__post post <?= $post['type_name']?>">
                    <header class="post__header">
                      <?php if ($post['is_repost']) : ?>
                        <div class="post__author">
                          <a class="post__author-link" href="/profile?user=<?= getUserDataById($post['origin_user_id'])['login']; ?>" title="Автор">
                            <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                              <img class="post__author-avatar" src="<?= checkFilePath(getUserDataById($post['origin_user_id'])['avatar']); ?>" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                              <b class="post__author-name">Репост: <?= getUserDataById($post['origin_user_id'])['login']; ?></b>
                              <time class="post__time" datetime="2019-03-30T14:31"><?= getRelativeDateDifference(new DateTime($post['create_date']), 'назад'); ?></time>
                            </div>
                          </a>
                        </div>
                      <?php endif; ?>
                      <h2><a href="/post.php?post_id=<?= $post['id']; ?>"><?= htmlspecialchars($post['header']); ?></a></h2>
                    </header>
                    <div class="post__main">
                        <?php if ($post['type_name'] == 'post-photo') : ?>
                            <div class="post-photo__image-wrapper"> <img src="<?= checkFilePath($post['post_image']); ?>" alt="Фото от пользователя" width="760" height="396"> </div>
                        <?php elseif ($post['type_name'] == 'post-text') : ?>
                            <p>
                                <?= cropText($post['post_text'], 200); ?>
                            </p>
                        <?php elseif ($post['type_name'] == 'post-video') : ?>
                        <div class="post-video__block">
                            <div class="post-video__preview">
                            <?=embed_youtube_cover($post['post_video']); ?> <img src="img/coast.jpg" alt="Превью к видео" width="760" height="396"> </div>
                            <div class="post-video__control">
                            <button class="post-video__play post-video__play--paused button button--video" type="button"><span class="visually-hidden">Запустить видео</span></button>
                            <div class="post-video__scale-wrapper">
                                <div class="post-video__scale">
                                <div class="post-video__bar">
                                    <div class="post-video__toggle"></div>
                                </div>
                                </div>
                            </div>
                            <button class="post-video__fullscreen post-video__fullscreen--inactive button button--video" type="button"><span class="visually-hidden">Полноэкранный режим</span></button>
                            </div>
                            <button class="post-video__play-big button" type="button">
                            <svg class="post-video__play-big-icon" width="27" height="28">
                                <use xlink:href="#icon-video-play-big"></use>
                            </svg> <span class="visually-hidden">Запустить проигрыватель</span> </button>
                        </div>
                        <?php elseif ($post['type_name'] == 'post-quote') : ?>
                            <blockquote>
                                <p>
                                <?= $post['post_text']; ?>
                                </p> <cite><?= $post['quote_author']; ?></cite> 
                            </blockquote>
                        <?php elseif ($post['type_name'] == 'post-link') : ?>
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="http://<?= $post['post_link']; ?>" title="Перейти по ссылке">
                                <div class="post-link__info">
                                    <h3><?= htmlspecialchars($post['header']); ?></h3> <span><?= htmlspecialchars($post['post_link']); ?></span> </div>
                                <svg class="post-link__arrow" width="11" height="16">
                                    <use xlink:href="#icon-arrow-right-ad"></use>
                                </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <footer class="post__footer">
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button" href="/like.php?post_id=<?= $post['id']; ?>" title="Лайк">
                                  <svg class="post__indicator-icon" width="20" height="17">
                                      <use xlink:href="#icon-heart"></use>
                                  </svg>
                                  <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                      <use xlink:href="#icon-heart-active"></use>
                                  </svg>
                                  <span><?= getDBDataCount($post['id'], 'post_id', 'likes'); ?></span>
                                  <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--repost button" href="/repost.php?post_id=<?= $post['id']; ?>" title="Репост">
                                  <svg class="post__indicator-icon" width="19" height="17">
                                      <use xlink:href="#icon-repost"></use>
                                  </svg>
                                  <span><?= $post['repost_count'];?></span>
                                  <span class="visually-hidden">количество репостов</span>
                                </a>
                            </div>
                            <time class="post__time"><?= getRelativeDateDifference(new DateTime($post['create_date']), 'назад'); ?></time>
                        </div>
                        <?php if (!empty(getHashtags($post['id']))) : ?>
                            <ul class="post__tags">
                                <?php foreach (getHashtags($post['id']) as $tagIndex => $tag) : ?>      
                                    <li><a href="#">#<?= $tag['hashtag']; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </footer>
                    <div class="comments">
                        <div class="comments__list-wrapper">
                        <ul class="comments__list">
                          <?php if (!empty($post['comments'])) : ?>
                            <?php foreach($post['comments'] as $commentIndex => $comment) : ?>
                              <li class="comments__item user">
                                <div class="comments__avatar">
                                  <a class="user__avatar-link" href="/profile.php?user=<?= $comment['login']; ?>">
                                    <img class="comments__picture" src="<?= checkFilePath($comment['avatar']); ?>" alt="Аватар пользователя">
                                  </a>
                                </div>
                                <div class="comments__info">
                                  <div class="comments__name-wrapper">
                                    <a class="comments__user-name" href="/profile.php?user=<?= $comment['login']; ?>">
                                      <span><?= $comment['login'];?></span>
                                    </a>
                                    <time class="comments__time" datetime="2019-03-20"><?= getRelativeDateDifference(new DateTime($comment['create_date']), 'назад'); ?></time>
                                  </div>
                                  <p class="comments__text">
                                    <?= $comment['comment'];?>
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
                    <?php print(include_template('comment-form.php', [
                      'userName' => getUserDataById($userId)['login'], 
                      'formError' => $formFieldsError, 
                      'postId' => $post['id']])
                    ); ?>
                </article>
                
            <?php endforeach; ?>
            
            </article>
          </section>

          <section data-type="likes" class="profile__likes tabs__content js-profile-tab-content">
            <h2 class="visually-hidden">Лайки</h2>
            <ul class="profile__likes-list">
              <?php if ($likesData && !empty($likesData)) : ?>
                <?php foreach ($likesData as $likesIndex => $like) : ?>
                  <li class="post-mini post-mini--text post user">
                    <div class="post-mini__user-info user__info">
                      <div class="post-mini__avatar user__avatar">
                        <a class="user__avatar-link" href="/profile.php?user=<?= $like['login'];?>">
                          <img class="post-mini__picture user__picture" src="<?= checkFilePath($like['avatar']); ?>" alt="Аватар пользователя">
                        </a>
                      </div>
                      <div class="post-mini__name-wrapper user__name-wrapper">
                        <a class="post-mini__name user__name" href="/profile.php?user=<?= $like['login'];?>">
                          <span><?= $like['login'];?></span>
                        </a>
                        <div class="post-mini__action">
                          <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                          <time class="post-mini__time user__additional" datetime="<?= $like['like_date']; ?>"><?= getRelativeDateDifference(new DateTime($like['like_date']), 'назад'); ?></time>
                        </div>
                      </div>
                    </div>
                    <div class="post-mini__preview">
                      <a class="post-mini__link" href="/post.php?post_id=<?= $like['id']; ?>" title="Перейти на публикацию">
                        <span class="visually-hidden">Текст</span>
                        <svg class="post-mini__preview-icon" width="20" height="21">
                          <use xlink:href="#icon-filter-text"></use>
                        </svg>
                      </a>
                    </div>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </section>

          <section data-type="subscribes" class="profile__subscriptions tabs__content js-profile-tab-content">
            <h2 class="visually-hidden">Подписки</h2>
            <ul class="profile__subscriptions-list">
              <?php if (!empty($subscribersData)) : ?>
                <?php foreach($subscribersData as $subscriberIndex => $subscriber) : ?>
                  <li class="post-mini post-mini--photo post user">
                    <div class="post-mini__user-info user__info">
                      <div class="post-mini__avatar user__avatar">
                        <a class="user__avatar-link" href="/profile.php?user=<?= $subscriber['login']; ?>">
                          <img class="post-mini__picture user__picture" src="<?= checkFilePath($subscriber['avatar']); ?>" alt="Аватар пользователя">
                        </a>
                      </div>
                      <div class="post-mini__name-wrapper user__name-wrapper">
                        <a class="post-mini__name user__name" href="/profile.php?user=<?= $subscriber['login']; ?>">
                          <span><?= $subscriber['login']; ?> <?= ($subscriber['id'] === $userId) ? ' (Это вы)' : ''; ?></span>
                        </a>
                        <time class="post-mini__time user__additional" datetime="<?= $subscriber['register_date']; ?>"><?= getRelativeDateDifference(new DateTime($subscriber['register_date']), 'на сайте'); ?></time>
                      </div>
                    </div>
                    <div class="post-mini__rating user__rating">
                      <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                        <span class="post-mini__rating-amount user__rating-amount"><?= getDBDataCount($subscriber['id'], 'user_id', 'posts'); ?></span>
                        <span class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form((int)getDBDataCount($subscriber['id'], 'user_id', 'posts'), 'публикация', 'публикации', 'публикаций'); ?></span>
                      </p>
                      <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="post-mini__rating-amount user__rating-amount"><?= getDBDataCount($subscriber['id'], 'subscribed_user_id', 'subscribes'); ?></span>
                        <span class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form((int)getDBDataCount($subscriber['id'], 'subscribed_user_id', 'subscribes'), 'подписчик', 'подписчика', 'подписчиков'); ?></span>
                      </p>
                    </div>
                    <div class="post-mini__user-buttons user__buttons">
                      <?php if (empty(getQueryParam('user'))) : ?>                   
                          <a href="/profile.php?unactive_subscriber_id=<?= $subscriber['id'];?>" class="post-mini__user-button user__button user__button--subscription button button--quartz">Отписаться</a>
                      <?php elseif ($subscriber['id'] != $userId) : ?>
                          <a href="/profile.php?subscribe_user=<?= $subscriber['id'];?>" class="post-mini__user-button user__button user__button--subscription button button--quartz">Подписаться</a>
                      <?php endif; ?>
                    </div>
                  </li>
                <?php endforeach; ?>
              <?php else : ?>
                <li class="post-mini post-mini--photo user">У вас еще нет подписчиков</li>
              <?php endif; ?>
            </ul>
          </section>
        </div>
      </div>
    </div>
  </div>
</main>