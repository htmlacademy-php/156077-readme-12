<main class="page__main page__main--search-results">
      <h1 class="visually-hidden">Страница результатов поиска</h1>
      <section class="search">
        <h2 class="visually-hidden">Результаты поиска</h2>
        <div class="search__query-wrapper">
          <div class="search__query container">
            <?php if ($searchTagMode) : ?>
              <span>Вы искали тег:</span>
            <?php else : ?>
              <span>Вы искали:</span>
            <?php endif; ?>
           
            <span class="search__query-text">#<?= htmlspecialchars($searchQuery); ?></span>
          </div>
        </div>
        <div class="search__results-wrapper">
          <div class="container">
            <div class="search__content">
                <?php foreach ($postsData as $postIndex => $post) : ?>
                    <?php if ($post['is_repost']) continue; ?>
                    <?php print(include_template( 'parts/post-preview.php', ['post' => $post, 'postTemplateName' => 'search']));?>
                <?php endforeach; ?>
            </div>
          </div>
        </div>
      </section>
    </main>