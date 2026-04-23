<?php
declare(strict_types=1);

define('SITE_NAME', 'ゆうきまん');
define('SITE_TAGLINE', '今日もどこかで、ゆうきまんが生まれている。');
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
define('ROOT_DIR', dirname(__DIR__));
define('ARTICLES_DIR', ROOT_DIR . '/articles');
define('DATA_DIR', ROOT_DIR . '/data');
define('THUMBNAILS_DIR', ROOT_DIR . '/thumbnails');
define('INDEX_FILE', DATA_DIR . '/index.json');

define('ADMIN_USER', 'admin');
define('ADMIN_PASS_HASH', password_hash('yuukiman2026', PASSWORD_DEFAULT));

define('ARTICLES_PER_PAGE', 10);
define('MAX_DAILY_ARTICLES', 4);
