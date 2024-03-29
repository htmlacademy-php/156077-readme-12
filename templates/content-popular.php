<div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>

    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link <?= (!$sortType || $sortType == 'popularity') ? 'sorting__link--active' : ''; ?>" href="/popular.php?sort=popularity<?= (!empty($filterPostTypeId)) ? '&post_type_id=' . $filterPostTypeId : ''; ?>">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= ($sortType == 'likes') ? 'sorting__link--active' : ''; ?>" href="/popular.php?sort=likes<?= (!empty($filterPostTypeId)) ? '&post_type_id=' . $filterPostTypeId : ''; ?>">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= ($sortType == 'date') ? 'sorting__link--active' : ''; ?>" href="/popular.php?sort=date<?= (!empty($filterPostTypeId)) ? '&post_type_id=' . $filterPostTypeId : ''; ?>">
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all <?= (empty($filterPostTypeId)) ? 'filters__button--active' : ''; ?>" href="/popular.php">
                            <span>Все</span>
                        </a>
                    </li>
                    <?php foreach ($postTypes as $postType) : ?>
                        <?php if ($postType['name'] == 'post-photo') : ?>
                            <li class="popular__filters-item filters__item">
                                <a class="filters__button filters__button--photo button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/popular.php?post_type_id=<?= $postType['id']; ?>">
                                    <span class="visually-hidden">Фото</span>
                                    <svg class="filters__icon" width="22" height="18">
                                        <use xlink:href="#icon-filter-photo"></use>
                                    </svg>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($postType['name'] == 'post-video') : ?>
                            <li class="popular__filters-item filters__item">
                                <a class="filters__button filters__button--video button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/popular.php?post_type_id=<?= $postType['id']; ?>">
                                    <span class="visually-hidden">Видео</span>
                                    <svg class="filters__icon" width="24" height="16">
                                        <use xlink:href="#icon-filter-video"></use>
                                    </svg>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($postType['name'] == 'post-text') : ?>
                            <li class="popular__filters-item filters__item">
                                <a class="filters__button filters__button--text button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/popular.php?post_type_id=<?= $postType['id']; ?>">
                                    <span class="visually-hidden">Текст</span>
                                    <svg class="filters__icon" width="20" height="21">
                                        <use xlink:href="#icon-filter-text"></use>
                                    </svg>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($postType['name'] == 'post-quote') : ?>
                            <li class="popular__filters-item filters__item">
                                <a class="filters__button filters__button--quote button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/popular.php?post_type_id=<?= $postType['id']; ?>">
                                    <span class="visually-hidden">Цитата</span>
                                    <svg class="filters__icon" width="21" height="20">
                                        <use xlink:href="#icon-filter-quote"></use>
                                    </svg>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($postType['name'] == 'post-link') : ?>
                            <li class="popular__filters-item filters__item">
                                <a class="filters__button filters__button--link button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/popular.php?post_type_id=<?= $postType['id']; ?>">
                                    <span class="visually-hidden">Ссылка</span>
                                    <svg class="filters__icon" width="21" height="18">
                                        <use xlink:href="#icon-filter-link"></use>
                                    </svg>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
       
        <div class="popular__posts">
            <?php if ($postsData && is_array($postsData)) : ?>
                <?php foreach ($postsData as $postIndex => $post) : ?>
                    <?php if ($post['is_repost']) continue; ?>
                    <article class="popular__post post <?= $post['type_name']; ?>">
                        <header class="post__header">
                            <h2><a href="/post.php?post_id=<?= $post['id']; ?>"><?= htmlspecialchars($post['header']); ?></a></h2>
                        </header>
                        <div class="post__main">
                            <?php if ($post['type_name'] == 'post-quote') : ?>
                                <blockquote>
                                    <p><?= $post['post_text']; ?></p>
                                    <cite><?= $post['quote_author']; ?></cite>
                                </blockquote>
                            <?php elseif ($post['type_name'] == 'post-text') : ?>
                                 <p><?= htmlspecialchars(cropText($post['post_text'], 200)); ?></p>
                            <?php elseif ($post['type_name'] == 'post-photo') : ?>
                                <div class="post-photo__image-wrapper">
                                    <img src="<?= checkFilePath($post['post_image']); ?>" alt="Фото от пользователя" width="360" height="240">
                                </div>
                            <?php elseif ($post['type_name'] == 'post-link') : ?>
                                <div class="post-link__wrapper">
                                    <a class="post-link__external" href="http://<?= $post['post_link']; ?>" title="Перейти по ссылке">
                                        <div class="post-link__info-wrapper">
                                            <div class="post-link__icon-wrapper">
                                                <img src="https://www.google.com/s2/favicons?domain=vitadental.ru" alt="Иконка">
                                            </div>
                                            <div class="post-link__info">
                                                <h3><?= htmlspecialchars($post['header']); ?></h3>
                                            </div>
                                        </div>
                                        <span><?= htmlspecialchars($post['post_link']); ?></span>
                                    </a>
                                </div>
                            <?php elseif ($post['type_name'] == 'post-video') : ?>
                                <div class="post-video__block">
                                    <div class="post-video__preview">
                                        <?=embed_youtube_cover($post['post_video']); ?>
                                    </div>
                                    <a href="/post.php?post_id=<?= $post['id'];?>" class="post-video__play-big button">
                                        <svg class="post-video__play-big-icon" width="14" height="14">
                                            <use xlink:href="#icon-video-play-big"></use>
                                        </svg>
                                        <span class="visually-hidden">Запустить проигрыватель</span>
                                    </a>
                                </div>
                            <?php else : ?>
                                <p><?= htmlspecialchars($post['post_text']); ?></p>
                            <?php endif; ?>
                        </div>
                        <footer class="post__footer">
                            <div class="post__author">
                                <a class="post__author-link" href="/profile.php?user=<?= $post['login'];?>" title="Автор">
                                    <div class="post__avatar-wrapper">
                                        <img class="post__author-avatar" src="<?= checkFilePath($post['avatar']); ?>" alt="Аватар пользователя">
                                    </div>
                                    <div class="post__info">
                                        <b class="post__author-name"><?= htmlspecialchars($post['login']); ?></b>
                                        <?php $date = new DateTime($post['create_date']); ?>                                
                                        <time class="post__time" title="<?= $date->format('d.m.Y H:i'); ?>" datetime="<?= $date->format('Y-m-d H:i:s'); ?>"><?= getRelativeDateDifference($date, 'назад'); ?></time>
                                    </div>
                                </a>
                            </div>
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
                                    <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-comment"></use>
                                        </svg>
                                        <span><?= getDBDataCount($post['id'], 'post_id', 'comments'); ?></span>
                                        <span class="visually-hidden">количество комментариев</span>
                                    </a>
                                </div>
                            </div>
                        </footer>
                    </article>
                <?php endforeach; ?>
                <div class="popular__page-links">
                <?php if ($pagesData['previous'] !== 0) : ?>
                    <a class="popular__page-link popular__page-link--prev button button--gray" href="/popular.php?pagen=<?= $pagesData['previous'] ;?><?= (!empty($filterPostTypeId)) ? '&post_type_id=' . $filterPostTypeId : ''; ?><?= (!empty($sortType)) ? '&sort=' . $sortType : ''; ?>">Предыдущая страница</a>
                <?php endif; ?>
                <?php if ($pagesData['next'] !== -1) : ?>
                    <a class="popular__page-link popular__page-link--next button button--gray" href="/popular.php?pagen=<?= $pagesData['next'] ;?><?= (!empty($filterPostTypeId)) ? '&post_type_id=' . $filterPostTypeId : ''; ?><?= (!empty($sortType)) ? '&sort=' . $sortType : ''; ?>">Следующая страница</a>
                <?php endif; ?>
            </div>
            <?php else : ?>
                <p>Постов не найдено</p>
            <?php endif; ?>     
        </div>
    </div>