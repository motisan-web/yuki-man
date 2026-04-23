<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Article.php';
require_once __DIR__ . '/includes/Parsedown.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') { header('Location: /'); exit; }

$article = Article::readBySlug($slug);
if (!$article || !($article['published'] ?? true)) {
    http_response_code(404);
    $pageTitle = '記事が見つかりません — ' . SITE_NAME;
    require __DIR__ . '/includes/header.php';
    echo '<div class="text-center py-24"><p class="text-stone text-xl font-serif">記事が見つかりませんでした。</p><a href="/" class="text-terra hover:underline mt-4 inline-block text-sm">← トップへ戻る</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pd = new Parsedown();
$pd->setSafeMode(true);
$html = $pd->text($article['body']);

$pageTitle = $article['title'] . ' — ' . SITE_NAME;
$pageDesc = $article['summary'] ?? SITE_TAGLINE;
require __DIR__ . '/includes/header.php';
?>

<article class="max-w-2xl mx-auto">

  <!-- Breadcrumb -->
  <nav class="text-xs text-stone mb-6 flex items-center gap-1.5">
    <a href="<?= BASE_URL ?>/" class="hover:text-terra transition">トップ</a>
    <span class="text-cream">/</span>
    <span class="text-olive truncate"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></span>
  </nav>

  <!-- Thumbnail -->
  <?php if (!empty($article['thumbnail'])): ?>
    <div class="mb-8 rounded-2xl overflow-hidden aspect-[16/9] bg-sand">
      <img src="<?= BASE_URL ?>/<?= htmlspecialchars($article['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
           alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>"
           class="w-full h-full object-cover">
    </div>
  <?php endif; ?>

  <!-- Header -->
  <header class="mb-8">
    <div class="flex flex-wrap gap-1.5 mb-4">
      <?php foreach ($article['tags'] ?? [] as $tag): ?>
        <a href="<?= BASE_URL ?>/search.php?tag=<?= urlencode($tag) ?>" class="tag">
          <?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>
        </a>
      <?php endforeach; ?>
    </div>
    <h1 class="font-serif text-2xl sm:text-3xl font-medium text-nearblack mb-4" style="line-height:1.3">
      <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
    </h1>
    <?php if (!empty($article['summary'])): ?>
      <p class="text-olive text-base border-l-2 border-terra pl-4" style="line-height:1.7">
        <?= htmlspecialchars($article['summary'], ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php endif; ?>
    <time class="text-xs text-stone mt-4 block"><?= htmlspecialchars($article['date'], ENT_QUOTES, 'UTF-8') ?></time>
  </header>

  <hr style="border:none;border-top:1px solid #e8e6dc;margin-bottom:2rem">

  <!-- Body -->
  <div class="article-body">
    <?= $html ?>
  </div>

  <!-- Back -->
  <div class="mt-12 pt-8" style="border-top:1px solid #e8e6dc">
    <a href="<?= BASE_URL ?>/" class="text-terra hover:text-coral transition text-sm">← 記事一覧に戻る</a>
  </div>

</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
