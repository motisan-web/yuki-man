<?php if (!defined('SITE_NAME')) require_once __DIR__ . '/config.php'; ?>
</main>

<footer class="bg-nearblack text-silver mt-16">
  <div class="max-w-4xl mx-auto px-5 py-8 flex flex-col sm:flex-row items-center justify-between gap-3">
    <span class="font-serif text-lg font-medium text-ivory">ゆうきまん</span>
    <p class="text-xs text-stone text-center sm:text-left">今日もどこかで、ゆうきまんが生まれている。</p>
    <nav class="flex gap-5 text-sm">
      <a href="<?= BASE_URL ?>/" class="text-silver hover:text-ivory transition">トップ</a>
      <a href="<?= BASE_URL ?>/search.php" class="text-silver hover:text-ivory transition">検索</a>
      <a href="<?= BASE_URL ?>/admin/" class="text-silver hover:text-ivory transition">管理</a>
    </nav>
  </div>
  <div class="border-t border-darksurf">
    <p class="text-center text-xs text-stone py-4">&copy; <?= date('Y') ?> ゆうきまん</p>
  </div>
</footer>

</body>
</html>
