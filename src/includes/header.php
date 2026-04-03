<?php require_once __DIR__ . '/../config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? "Noodle's Pet Profiles") ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php if (isAdminLoggedIn()): ?>
    <div class="admin-bar">
        <div class="container">
            <span>🔑 Logged in as <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></span>
            <div class="admin-bar__links">
                <a href="/admin/">Dashboard</a>
                <a href="/admin/logout.php">Log out</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <header class="site-header">
        <div class="container">
            <a href="/" class="site-logo">
                🐾 Noodle's Pet Profiles
            </a>
            <nav class="site-nav">
                <a href="/">Home</a>
                <?php if (!isAdminLoggedIn()): ?>
                    <a href="/admin/login.php">Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="site-main">
        <div class="container">
