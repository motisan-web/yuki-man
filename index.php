<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Article.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$result = Article::listPublished($page, ARTICLES_PER_PAGE);
$articles = $result['items'];
$totalPages = $result['pages'];

$pageTitle = SITE_NAME . ' — ' . SITE_TAGLINE;
$pageDesc = SITE_TAGLINE;
require __DIR__ . '/includes/header.php';
?>

<div class="space-y-8">

  <!-- Hero -->
  <div class="bg-nearblack rounded-2xl px-8 py-10 text-center" style="box-shadow:rgba(0,0,0,0.05) 0 4px 24px">
    <p class="text-stone text-xs tracking-widest uppercase mb-3">Japan's Finest Incidents</p>
    <h1 class="font-serif text-4xl sm:text-5xl font-medium text-ivory leading-tight mb-3">ゆうきまん</h1>
    <p class="text-silver text-sm" style="line-height:1.6">今日もどこかで、ゆうきまんが生まれている。</p>
  </div>

  <!-- Article List -->
  <?php if (empty($articles)): ?>
    <p class="text-center text-stone py-16">まだ記事がありません。</p>
  <?php else: ?>
    <div class="space-y-3">
      <?php foreach ($articles as $a): ?>
        <article class="card overflow-hidden flex">
          <?php if (!empty($a['thumbnail'])): ?>
            <div class="w-32 sm:w-44 shrink-0">
              <a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($a['slug']) ?>">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($a['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
                     alt=""
                     class="w-full h-full object-cover">
              </a>
            </div>
          <?php endif; ?>
          <div class="p-5 flex flex-col justify-between flex-1 min-w-0">
            <div>
              <div class="flex flex-wrap gap-1.5 mb-2.5">
                <?php foreach ($a['tags'] ?? [] as $tag): ?>
                  <a href="<?= BASE_URL ?>/search.php?tag=<?= urlencode($tag) ?>" class="tag">
                    <?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>
                  </a>
                <?php endforeach; ?>
              </div>
              <h2 class="font-serif font-medium text-base sm:text-lg text-nearblack leading-snug mb-2" style="line-height:1.4">
                <a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($a['slug']) ?>"
                   class="hover:text-terra transition">
                  <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
              </h2>
              <?php if (!empty($a['summary'])): ?>
                <p class="text-sm text-olive line-clamp-2" style="line-height:1.6">
                  <?= htmlspecialchars($a['summary'], ENT_QUOTES, 'UTF-8') ?>
                </p>
              <?php endif; ?>
            </div>
            <time class="text-xs text-stone mt-3 block"><?= htmlspecialchars($a['date'], ENT_QUOTES, 'UTF-8') ?></time>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav class="flex justify-center gap-2 pt-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <?php if ($i === $page): ?>
            <span class="px-4 py-1.5 rounded-lg text-sm bg-terra text-ivory font-medium"><?= $i ?></span>
          <?php else: ?>
            <a href="?page=<?= $i ?>"
               class="px-4 py-1.5 rounded-lg text-sm bg-ivory border border-sand text-charcoal hover:border-ringwarm transition">
              <?= $i ?>
            </a>
          <?php endif; ?>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
