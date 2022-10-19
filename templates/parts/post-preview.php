<article class="<?= $postTemplateName;?>__post post <?= $post['type_name'];?>">
    <header class="post__header post__author">
        <?php if ($post['is_repost']) : ?>
            <div class="post__author">
                <a class="post__author-link" href="/profile?user=<?= getUserDataById($post['origin_user_id'])['login']; ?>" title="Автор">
                <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                    <img class="post__author-avatar" src="<?= checkFilePath(getUserDataById($post['origin_user_id'])['avatar']); ?>" alt="Аватар пользователя">
                </div>
                <div class="post__info">
                    <b class="post__author-name">Репост: <?= getUserDataById($post['origin_user_id'])['login']; ?></b>
                    <time class="post__time" datetime="<?= $post['create_date'];?>"><?= getRelativeDateDifference(new DateTime($post['create_date']), 'назад'); ?></time>
                </div>
                </a>
            </div>
        <?php else : ?>
            <a class="post__author-link" href="#" title="Автор">
                <div class="post__avatar-wrapper"> 
                    <img class="post__author-avatar" src="<?= checkFilePath($post['avatar']); ?>" alt="Аватар пользователя" width="60" height="60"> 
                </div>
                <div class="post__info"> 
                    <b class="post__author-name"><?= $post['login']; ?></b> <span class="post__time">
                    <?= getRelativeDateDifference(new DateTime($post['create_date']), 'назад'); ?></span> 
                </div>
            </a>
        <?php endif; ?>
    </header>
    <div class="post__main">
        <?php if ($post['type_name'] == 'post-photo') : ?>
            <h2><a href="/post.php?post_id=<?= ($post['is_repost']) ? $post['origin_post_id'] : $post['id']; ?>"><?= htmlspecialchars($post['header']); ?></a></h2>
            <div class="post-photo__image-wrapper"> <img src="<?= checkFilePath($post['post_image']); ?>" alt="Фото от пользователя" width="760" height="396"> </div>
        <?php elseif ($post['type_name'] == 'post-text') : ?>
            <h2><a href="/post.php?post_id=<?= ($post['is_repost']) ? $post['origin_post_id'] : $post['id']; ?>"><?= htmlspecialchars($post['header']); ?></a></h2>
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
    <footer class="post__footer post__indicators">
        <div class="post__buttons">
        <a class="post__indicator post__indicator--likes button" href="/like.php?post_id=<?= $post['id']; ?>" title="Лайк">
            <svg class="post__indicator-icon" width="20" height="17">
            <use xlink:href="#icon-heart"></use>
            </svg>
            <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
            <use xlink:href="#icon-heart-active"></use>
            </svg> <span><?= getDBDataCount($post['id'], 'post_id', 'likes'); ?></span> <span class="visually-hidden">количество лайков</span> </a>
        <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
            <svg class="post__indicator-icon" width="19" height="17">
            <use xlink:href="#icon-comment"></use>
            </svg> <span><?= getDBDataCount($post['id'], 'post_id', 'comments'); ?></span> <span class="visually-hidden">количество комментариев</span> </a>
        </div>
    </footer>
</article>