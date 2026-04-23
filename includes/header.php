<?php
declare(strict_types=1);
if (!defined('SITE_NAME')) require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDesc ?? SITE_TAGLINE, ENT_QUOTES, 'UTF-8') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;500&family=Noto+Sans+JP:wght@400;500&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            parchment: '#f5f4ed',
            ivory:     '#faf9f5',
            nearblack: '#141413',
            terra:     '#c96442',
            coral:     '#d97757',
            olive:     '#5e5d59',
            stone:     '#87867f',
            cream:     '#f0eee6',
            sand:      '#e8e6dc',
            charcoal:  '#4d4c48',
            darksurf:  '#30302e',
            silver:    '#b0aea5',
            ringwarm:  '#d1cfc5',
          },
          fontFamily: {
            serif: ['"Noto Serif JP"', 'Georgia', 'serif'],
            sans:  ['"Noto Sans JP"', 'system-ui', 'sans-serif'],
          },
          boxShadow: {
            'ring':    '0 0 0 1px #d1cfc5',
            'ring-terra': '0 0 0 1px #c96442',
            'whisper': 'rgba(0,0,0,0.05) 0px 4px 24px',
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-parchment text-nearblack min-h-screen flex flex-col font-sans">

<header class="bg-parchment border-b border-cream sticky top-0 z-10" style="box-shadow:rgba(0,0,0,0.04) 0 1px 0">
  <div class="max-w-4xl mx-auto px-5 py-3 flex items-center justify-between gap-4">
    <a href="<?= BASE_URL ?>/" class="flex items-center gap-3 no-underline shrink-0">
      <span class="font-serif text-2xl font-medium text-terra leading-none tracking-tight">ゆうきまん</span>
      <span class="hidden sm:inline text-xs text-stone leading-tight">今日もどこかで、ゆうきまんが生まれている。</span>
    </a>
    <form action="<?= BASE_URL ?>/search.php" method="get" class="flex items-center gap-2 flex-1 max-w-xs">
      <input
        type="search" name="q"
        value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        placeholder="事件を検索..."
        class="w-full bg-ivory border border-sand rounded-xl px-4 py-1.5 text-sm text-nearblack placeholder-stone focus:outline-none focus:border-[#3898ec] focus:ring-1 focus:ring-[#3898ec] transition"
      >
      <button type="submit" class="btn-terra shrink-0 text-sm">検索</button>
    </form>
  </div>
</header>

<main class="flex-1 max-w-4xl mx-auto w-full px-5 py-8">
