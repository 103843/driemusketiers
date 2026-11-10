<?php
require 'config.php';
require_login();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard – Driemusketiers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap">
    <h1>Hoi <?= e($_SESSION['name']) ?>!</h1>
    <p class="lead">Je bent ingelogd. Hier komen binnenkort jouw favoriete studentengerechten.</p>
    <?php if (is_admin()): ?><a href="admin/index.php" class="btn btn-orange">Admin panel</a> <?php endif; ?>
    <a href="logout.php" class="btn btn-pink">Uitloggen</a>
</main>
</body>
</html>