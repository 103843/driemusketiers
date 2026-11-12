<?php
require __DIR__ . '/../config.php';
require_admin();

function admin_header(string $title): void { $flash = flash(); ?>
    <!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?> – Admin Driemusketiers</title>
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
    <nav class="admin-nav">
        <a href="index.php">Overzicht</a>
        <a href="users.php">Klanten</a>
        <a href="content.php">Content</a>
        <a href="../dashboard.php">Site</a>
        <a href="../logout.php">Uitloggen</a>
    </nav>
    <main class="wrap admin-wrap">
    <h1><?= e($title) ?></h1>
    <?php if ($flash): ?><div class="alert ok"><?= e($flash) ?></div><?php endif; ?>
<?php }

function admin_footer(): void { ?>
    </main>
    </body>
    </html>
<?php }