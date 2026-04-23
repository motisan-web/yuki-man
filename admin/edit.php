<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/Article.php';

session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

$editSlug = trim($_GET['slug'] ?? '');
$isNew = $editSlug === '';
$article = null;
$errors = [];

if (!$isNew) {
    $article = Article::readBySlug($editSlug);
    if (!$article) { header('Location: ' . BASE_URL . '/admin/'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title     = trim($_POST['title'] ?? '');
    $date      = trim($_POST['date'] ?? '');
    $slug      = Article::sanitizeSlug(trim($_POST['slug'] ?? ''));
    $tagsRaw   = trim($_POST['tags'] ?? '');
    $summary   = trim($_POST['summary'] ?? '');
    $body      = $_POST['body'] ?? '';
    $published = isset($_POST['published']);

    $thumbnail = $article['thumbnail'] ?? '';
    if (!empty($_FILES['thumbnail']['name'])) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['thumbnail']['tmp_name']);
        finfo_close($finfo);
        if (in_array($mime, $allowed)) {
            $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $ext = 'jpg';
            $filename = $slug . '-' . time() . '.' . $ext;
            $dest = THUMBNAILS_DIR . '/' . $filename;
            if (!is_dir(THUMBNAILS_DIR)) mkdir(THUMBNAILS_DIR, 0755, true);
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dest)) {
                $thumbnail = 'thumbnails/' . $filename;
            }
        } else {
            $errors[] = '画像形式が不正です（JPEG/PNG/GIF/WebPのみ）。';
        }
    }

    if ($title === '') $errors[] = 'タイトルは必須です。';
    if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $errors[] = '日付の形式が不正です（YYYY-MM-DD）。';
    if ($slug === '') $errors[] = 'スラッグは必須です。';

    if (empty($errors)) {
        $tags = array_values(array_filter(array_map('trim', explode(',', $tagsRaw)), fn($t) => $t !== ''));
        $meta = ['title' => $title, 'date' => $date, 'slug' => $slug, 'tags' => $tags,
                 'thumbnail' => $thumbnail, 'summary' => $summary, 'published' => $published];
        Article::save($meta, $body);
        header('Location: ' . BASE_URL . '/admin/?saved=1');
        exit;
    }
}

$f = [
    'title'     => $article['title'] ?? '',
    'date'      => $article['date'] ?? date('Y-m-d'),
    'slug'      => $article['slug'] ?? '',
    'tags'      => implode(', ', $article['tags'] ?? []),
    'summary'   => $article['summary'] ?? '',
    'body'      => $article['body'] ?? '',
    'thumbnail' => $article['thumbnail'] ?? '',
    'published' => $article['published'] ?? true,
];

$pageTitle = ($isNew ? '新規記事' : '記事を編集') . ' — ' . SITE_NAME;
require dirname(__DIR__) . '/includes/header.php';
?>

<div class="max-w-2xl mx-auto space-y-5">

  <div class="flex items-center justify-between">
    <h1 class="font-serif text-2xl font-medium text-nearblack">
      <?= $isNew ? '新規記事' : '記事を編集' ?>
    </h1>
    <a href="<?= BASE_URL ?>/admin/" class="text-sm text-stone hover:text-olive transition">← 一覧へ</a>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="bg-ivory border border-sand rounded-xl px-5 py-4 text-sm text-olive space-y-1">
      <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="card p-6 space-y-5">

    <div>
      <label class="label-warm">タイトル <span class="text-terra">*</span></label>
      <input type="text" name="title" required
             value="<?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?>"
             class="input-warm">
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label-warm">日付 <span class="text-terra">*</span></label>
        <input type="date" name="date" required
               value="<?= htmlspecialchars($f['date'], ENT_QUOTES, 'UTF-8') ?>"
               class="input-warm">
      </div>
      <div>
        <label class="label-warm">スラッグ <span class="text-terra">*</span></label>
        <input type="text" name="slug" required
               value="<?= htmlspecialchars($f['slug'], ENT_QUOTES, 'UTF-8') ?>"
               placeholder="kebab-case-only"
               class="input-warm font-mono text-sm">
      </div>
    </div>

    <div>
      <label class="label-warm">タグ（カンマ区切り）</label>
      <input type="text" name="tags"
             value="<?= htmlspecialchars($f['tags'], ENT_QUOTES, 'UTF-8') ?>"
             placeholder="逮捕, 事故, 食べ物"
             class="input-warm">
    </div>

    <div>
      <label class="label-warm">要約（検索・OGP用）</label>
      <textarea name="summary" rows="2" class="input-warm" style="resize:vertical"><?= htmlspecialchars($f['summary'], ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div>
      <label class="label-warm">サムネイル画像</label>
      <?php if (!empty($f['thumbnail'])): ?>
        <div class="mb-3">
          <img src="<?= BASE_URL ?>/<?= htmlspecialchars($f['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
               alt="" class="h-24 rounded-xl object-cover border border-sand">
          <p class="text-xs text-stone mt-1"><?= htmlspecialchars($f['thumbnail'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
      <?php endif; ?>
      <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/gif,image/webp"
             class="text-sm text-olive">
    </div>

    <div>
      <label class="label-warm">本文（Markdown）</label>
      <textarea name="body" id="body-editor" rows="20"
                class="input-warm font-mono text-sm" style="resize:vertical;line-height:1.6"><?= htmlspecialchars($f['body'], ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div class="flex items-center gap-2.5">
      <input type="checkbox" name="published" id="published"
             <?= $f['published'] ? 'checked' : '' ?>
             class="w-4 h-4 rounded border-sand accent-terra">
      <label for="published" class="text-sm text-olive">公開する</label>
    </div>

    <div class="flex gap-3 pt-1">
      <button type="submit" class="btn-terra" style="padding:9px 20px;border-radius:10px;font-size:0.9375rem">
        <?= $isNew ? '記事を作成' : '変更を保存' ?>
      </button>
      <a href="<?= BASE_URL ?>/admin/"
         class="btn-sand" style="padding:9px 20px;border-radius:10px;font-size:0.9375rem">
        キャンセル
      </a>
    </div>

  </form>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
