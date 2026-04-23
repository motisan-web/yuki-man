<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/config.php';
session_start();
session_destroy();
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
