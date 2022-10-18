<main class="page__main page__main--feed">
	<div class="container">
		<h1 class="page__title page__title--feed">Моя лента</h1> </div>
	<div class="page__main-wrapper container">
		<section class="feed">
			<h2 class="visually-hidden">Лента</h2>
			<div class="feed__main-wrapper">
				<div class="feed__wrapper">
          <?php if ($postsData) : ?>
            <?php foreach ($postsData as $postIndex => $post) : ?>
              <?php print(include_template( 'parts/post-preview.php', ['post' => $post, 'postTemplateName' => 'feed']));?>
            <?php endforeach; ?>
          <?php else : ?>
            <p>Постов не найдено</p>
          <?php endif; ?>
				</div>
			</div>
			<ul class="feed__filters filters">
          <li class="feed__filters-item filters__item">
            <a class="filters__button <?= (empty($filterPostTypeId)) ? 'filters__button--active' : ''; ?>" href="/feed.php">
              <span>Все</span>
            </a>
          </li>
          <?php foreach ($postTypes as $postType) : ?>
            <?php if ($postType['name'] == 'post-photo') : ?>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--photo button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/feed.php?post_type_id=<?= $postType['id']; ?>">
                        <span class="visually-hidden">Фото</span>
                        <svg class="filters__icon" width="22" height="18">
                            <use xlink:href="#icon-filter-photo"></use>
                        </svg>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($postType['name'] == 'post-video') : ?>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--video button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/feed.php?post_type_id=<?= $postType['id']; ?>">
                        <span class="visually-hidden">Видео</span>
                        <svg class="filters__icon" width="24" height="16">
                            <use xlink:href="#icon-filter-video"></use>
                        </svg>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($postType['name'] == 'post-text') : ?>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--text button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/feed.php?post_type_id=<?= $postType['id']; ?>">
                        <span class="visually-hidden">Текст</span>
                        <svg class="filters__icon" width="20" height="21">
                            <use xlink:href="#icon-filter-text"></use>
                        </svg>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($postType['name'] == 'post-quote') : ?>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--quote button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/feed.php?post_type_id=<?= $postType['id']; ?>">
                        <span class="visually-hidden">Цитата</span>
                        <svg class="filters__icon" width="21" height="20">
                            <use xlink:href="#icon-filter-quote"></use>
                        </svg>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($postType['name'] == 'post-link') : ?>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--link button <?= ($filterPostTypeId == $postType['id']) ? 'filters__button--active' : ''; ?>" href="/feed.php?post_type_id=<?= $postType['id']; ?>">
                        <span class="visually-hidden">Ссылка</span>
                        <svg class="filters__icon" width="21" height="18">
                            <use xlink:href="#icon-filter-link"></use>
                        </svg>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
			</ul>
		</section>
		<aside class="promo">
			<article class="promo__block promo__block--barbershop">
				<h2 class="visually-hidden">Рекламный блок</h2>
				<p class="promo__text"> Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе! </p> <a class="promo__link" href="#">
              Подробнее
            </a> </article>
			<article class="promo__block promo__block--technomart">
				<h2 class="visually-hidden">Рекламный блок</h2>
				<p class="promo__text"> Товары будущего уже сегодня в онлайн-сторе Техномарт! </p> <a class="promo__link" href="#">
              Перейти в магазин
            </a> </article>
			<article class="promo__block">
				<h2 class="visually-hidden">Рекламный блок</h2>
				<p class="promo__text"> Здесь
					<br> могла быть
					<br> ваша реклама </p> <a class="promo__link" href="#">
              Разместить
            </a> </article>
		</aside>
	</div>
</main>