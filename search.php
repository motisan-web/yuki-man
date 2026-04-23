<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Article.php';
require_once __DIR__ . '/includes/Search.php';

$q   = trim($_GET['q'] ?? '');
$tag = trim($_GET['tag'] ?? '');

$results = [];
if ($tag !== '') {
    $results = Search::byTag($tag);
    $heading = "タグ：" . $tag;
} elseif ($q !== '') {
    $results = Search::query($q);
    $heading = "「{$q}」の検索結果";
} else {
    $heading = '検索';
}

$pageTitle = ($q ?: $tag ?: '検索') . ' — ' . SITE_NAME;
require __DIR__ . '/includes/header.php';
?>

<div class="space-y-6">

  <div class="flex items-baseline gap-3">
    <h1 class="font-serif text-2xl font-medium text-nearblack"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h1>
    <?php if (!empty($results)): ?>
      <span class="text-sm text-stone"><?= count($results) ?>件</span>
    <?php endif; ?>
  </div>

  <?php if ($q === '' && $tag === ''): ?>
    <!-- Empty search form -->
    <form action="search.php" method="get" class="flex gap-2 max-w-lg">
      <input type="search" name="q" placeholder="キーワードで検索..."
             class="input-warm flex-1">
      <button type="submit" class="btn-terra">検索</button>
    </form>

  <?php elseif (($q !== '' || $tag !== '') && empty($results)): ?>
    <div class="card p-10 text-center">
      <p class="text-stone">該当する記事が見つかりませんでした。</p>
      <a href="<?= BASE_URL ?>/search.php" class="text-terra text-sm hover:underline mt-2 inline-block">← 検索に戻る</a>
    </div>

  <?php else: ?>
    <div class="space-y-3">
      <?php foreach ($results as $a): ?>
        <article class="card overflow-hidden flex">
          <?php if (!empty($a['thumbnail'])): ?>
            <div class="w-28 sm:w-36 shrink-0">
              <a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($a['slug']) ?>">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($a['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
                     alt="" class="w-full h-full object-cover">
              </a>
            </div>
          <?php endif; ?>
          <div class="p-5 flex flex-col justify-between flex-1 min-w-0">
            <div>
              <div class="flex flex-wrap gap-1.5 mb-2">
                <?php foreach ($a['tags'] ?? [] as $t): ?>
                  <a href="<?= BASE_URL ?>/search.php?tag=<?= urlencode($t) ?>" class="tag">
                    <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
                  </a>
                <?php endforeach; ?>
              </div>
              <h2 class="font-serif font-medium text-base text-nearblack leading-snug" style="line-height:1.4">
                <a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($a['slug']) ?>"
                   class="hover:text-terra transition">
                  <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
              </h2>
              <?php if (!empty($a['summary'])): ?>
                <p class="text-sm text-olive line-clamp-2 mt-1" style="line-height:1.6">
                  <?= htmlspecialchars($a['summary'], ENT_QUOTES, 'UTF-8') ?>
                </p>
              <?php endif; ?>
            </div>
            <time class="text-xs text-stone mt-3 block"><?= htmlspecialchars($a['date'], ENT_QUOTES, 'UTF-8') ?></time>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
