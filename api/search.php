<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/Article.php';
require_once dirname(__DIR__) . '/includes/Search.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

$q   = trim($_GET['q'] ?? '');
$tag = trim($_GET['tag'] ?? '');

if ($q !== '') {
    $results = Search::query($q, 20);
} elseif ($tag !== '') {
    $results = Search::byTag($tag);
} else {
    echo json_encode(['results' => [], 'count' => 0]);
    exit;
}

echo json_encode(['results' => $results, 'count' => count($results)], JSON_UNESCAPED_UNICODE);
