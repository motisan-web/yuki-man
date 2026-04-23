<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/config.php';
session_start();

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . BASE_URL . '/admin/');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        session_regenerate_id(true);
        header('Location: ' . BASE_URL . '/admin/');
        exit;
    }
    $error = 'ユーザー名またはパスワードが違います。';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>管理ログイン — <?= SITE_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;500&family=Noto+Sans+JP:wght@400;500&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        colors: { parchment:'#f5f4ed', ivory:'#faf9f5', nearblack:'#141413', terra:'#c96442', olive:'#5e5d59', stone:'#87867f', cream:'#f0eee6', sand:'#e8e6dc', charcoal:'#4d4c48' },
        fontFamily: { serif:['"Noto Serif JP"','Georgia','serif'], sans:['"Noto Sans JP"','system-ui','sans-serif'] }
      }}
    }
  </script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-nearblack min-h-screen flex items-center justify-center font-sans">
  <div class="w-full max-w-sm px-4">
    <div class="text-center mb-8">
      <p class="font-serif text-3xl font-medium text-ivory mb-1">ゆうきまん</p>
      <p class="text-xs text-stone">今日もどこかで、ゆうきまんが生まれている。</p>
    </div>
    <div class="bg-parchment rounded-2xl p-8" style="box-shadow:rgba(0,0,0,0.3) 0 8px 32px">
      <h2 class="text-base font-medium text-nearblack mb-6 text-center">管理ログイン</h2>
      <?php if ($error): ?>
        <div class="bg-ivory border border-sand rounded-xl px-4 py-3 mb-5 text-sm text-olive">
          <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>
      <form method="post" class="space-y-4">
        <div>
          <label class="label-warm">ユーザー名</label>
          <input type="text" name="username" required autocomplete="username" class="input-warm">
        </div>
        <div>
          <label class="label-warm">パスワード</label>
          <input type="password" name="password" required autocomplete="current-password" class="input-warm">
        </div>
        <button type="submit" class="btn-terra w-full text-center mt-2" style="padding:10px 14px;border-radius:12px;font-size:0.9375rem">
          ログイン
        </button>
      </form>
    </div>
  </div>
</body>
</html>
