<main class="page__main page__main--search-results">
      <h1 class="visually-hidden">Страница результатов поиска</h1>
      <section class="search">
        <h2 class="visually-hidden">Результаты поиска</h2>
        <div class="search__query-wrapper">
          <div class="search__query container">
            <span>Вы искали:</span>
            <span class="search__query-text">#<?= $searchQuery; ?></span>
          </div>
        </div>
        <div class="search__results-wrapper">
          <div class="container">
            <div class="search__content">
                <?php foreach ($postsData as $postIndex => $post) : ?>
                    <?php print(include_template( 'parts/post-preview.php', ['post' => $post, 'postTemplateName' => 'search']));?>
                <?php endforeach; ?>
            </div>
          </div>
        </div>
      </section>
    </main>