<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/Article.php';

session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

$articles = Article::listAll();
$saved = isset($_GET['saved']);
$pageTitle = '管理画面 — ' . SITE_NAME;
require dirname(__DIR__) . '/includes/header.php';
?>

<div class="space-y-5">

  <div class="flex items-center justify-between">
    <h1 class="font-serif text-2xl font-medium text-nearblack">記事管理</h1>
    <div class="flex gap-2">
      <a href="<?= BASE_URL ?>/admin/edit.php" class="btn-terra">＋ 新規記事</a>
      <a href="<?= BASE_URL ?>/admin/logout.php" class="btn-sand">ログアウト</a>
    </div>
  </div>

  <?php if ($saved): ?>
    <div class="bg-ivory border border-sand rounded-xl px-4 py-3 text-sm text-olive">
      記事を保存しました。
    </div>
  <?php endif; ?>

  <?php if (empty($articles)): ?>
    <div class="card p-12 text-center">
      <p class="text-stone">まだ記事がありません。</p>
    </div>
  <?php else: ?>
    <div class="card overflow-hidden">
      <table class="w-full text-sm">
        <thead style="border-bottom:1px solid #e8e6dc; background:#f5f4ed">
          <tr>
            <th class="text-left px-5 py-3 font-medium text-olive text-xs uppercase tracking-wide">タイトル</th>
            <th class="text-left px-5 py-3 font-medium text-olive text-xs uppercase tracking-wide hidden sm:table-cell">日付</th>
            <th class="text-left px-5 py-3 font-medium text-olive text-xs uppercase tracking-wide hidden md:table-cell">タグ</th>
            <th class="text-left px-5 py-3 font-medium text-olive text-xs uppercase tracking-wide">状態</th>
            <th class="text-right px-5 py-3 font-medium text-olive text-xs uppercase tracking-wide">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($articles as $a): ?>
            <tr class="border-t border-cream hover:bg-parchment transition">
              <td class="px-5 py-3.5">
                <a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($a['slug']) ?>" target="_blank"
                   class="font-medium text-nearblack hover:text-terra transition line-clamp-1">
                  <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
              </td>
              <td class="px-5 py-3.5 text-stone hidden sm:table-cell text-xs">
                <?= htmlspecialchars($a['date'], ENT_QUOTES, 'UTF-8') ?>
              </td>
              <td class="px-5 py-3.5 hidden md:table-cell">
                <div class="flex flex-wrap gap-1">
                  <?php foreach (array_slice($a['tags'] ?? [], 0, 3) as $tag): ?>
                    <span class="tag"><?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?></span>
                  <?php endforeach; ?>
                </div>
              </td>
              <td class="px-5 py-3.5">
                <?php if ($a['published'] ?? true): ?>
                  <span class="inline-block text-xs rounded-full px-2.5 py-0.5 bg-ivory border border-sand text-olive">公開</span>
                <?php else: ?>
                  <span class="inline-block text-xs rounded-full px-2.5 py-0.5 bg-parchment border border-sand text-stone">下書き</span>
                <?php endif; ?>
              </td>
              <td class="px-5 py-3.5 text-right">
                <div class="flex justify-end gap-2">
                  <a href="<?= BASE_URL ?>/admin/edit.php?slug=<?= urlencode($a['slug']) ?>"
                     class="text-xs btn-sand" style="padding:4px 12px;border-radius:8px">編集</a>
                  <a href="<?= BASE_URL ?>/admin/delete.php?slug=<?= urlencode($a['slug']) ?>"
                     onclick="return confirm('本当に削除しますか？')"
                     class="text-xs border border-sand text-stone px-3 py-1 rounded-lg hover:border-terra hover:text-terra transition">削除</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
