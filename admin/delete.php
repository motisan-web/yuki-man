<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/Article.php';

session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

$slug = trim($_GET['slug'] ?? '');
if ($slug !== '') {
    Article::delete($slug);
}
header('Location: ' . BASE_URL . '/admin/');
exit;
