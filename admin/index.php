<?php
require '_layout.php';
$users = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$items = $db->query('SELECT COUNT(*) FROM content')->fetchColumn();
admin_header('Admin panel');
?>
    <div class="stats">
        <a class="stat" href="users.php"><strong><?= (int)$users ?></strong>Klanten</a>
        <a class="stat" href="content.php"><strong><?= (int)$items ?></strong>Content</a>
    </div>
<?php admin_footer(); ?>