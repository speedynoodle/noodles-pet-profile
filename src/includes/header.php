<?php require_once __DIR__ . '/../config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? "Noodles’ Pet Profiles") ?></title>
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
                🐾 Noodles’ Pet Profiles
            </a>
            <nav class="site-nav">
                <a href="/" <?= (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/') ? 'aria-current="page"' : '' ?>>Home</a>
                <a href="/pages/sitter.php" <?= (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/pages/sitter.php') ? 'aria-current="page"' : '' ?>>Sitter Info</a>
            </nav>        </div>
    </header>
    <main class="site-main">
        <div class="container">

    <!-- Toiletry Modal -->
    <div id="toiletry-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Log <span id="modal-log-type">Pee</span> for <span id="modal-pet-name">Buddy</span></h2>
                <button class="modal-close-btn" id="modal-close-btn">✕</button>
            </div>
            <div class="modal-body">
                <p class="modal-question">Was this an accident?</p>
                <div class="modal-checkbox-group">
                    <input type="checkbox" id="modal-is-accident" />
                    <label for="modal-is-accident">Mark as accident</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn--cancel" id="modal-cancel-btn">Cancel</button>
                <button class="modal-btn modal-btn--confirm" onclick="submitToiletryLog()">Log Now</button>
            </div>
        </div>
    </div>

