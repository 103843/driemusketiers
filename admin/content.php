<?php
require '_layout.php';
$errors = [];
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$isNew = isset($_GET['new']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) { exit('Ongeldig verzoek.'); }
    $id = (int)($_POST['id'] ?? 0);

    if (($_POST['action'] ?? '') === 'delete') {
        $db->prepare('DELETE FROM content WHERE id = ?')->execute([$id]);
        flash('Content verwijderd.');
        header('Location: content.php'); exit;
    }

    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($title === '' || mb_strlen($title) > 120) $errors[] = 'Vul een titel in (max. 120 tekens).';
    if ($desc === '') $errors[] = 'Vul een beschrijving in.';

    if (!$errors) {
        if ($id) {
            $db->prepare("UPDATE content SET title = ?, description = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$title, $desc, $id]);
            flash('Content bijgewerkt.');
        } else {
            $db->prepare('INSERT INTO content (title, description) VALUES (?, ?)')->execute([$title, $desc]);
            flash('Content toegevoegd.');
        }
        header('Location: content.php'); exit;
    }
    $editId = $id; $isNew = !$id;
}

$edit = ['id' => 0, 'title' => '', 'description' => ''];
if ($editId) {
    $stmt = $db->prepare('SELECT * FROM content WHERE id = ?');
    $stmt->execute([$editId]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC) ?: $edit;
}
$showForm = $isNew || $editId;
$rows = $db->query('SELECT * FROM content ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

admin_header('Content');
foreach ($errors as $err) echo '<div class="alert">' . e($err) . '</div>';
if ($showForm): ?>
    <form method="post" class="panel">
        <h2><?= $edit['id'] ? 'Content aanpassen' : 'Nieuwe content' ?></h2>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <label>Titel</label><input type="text" name="title" value="<?= e($_POST['title'] ?? $edit['title']) ?>" required>
        <label>Beschrijving</label><textarea name="description" rows="6" required><?= e($_POST['description'] ?? $edit['description']) ?></textarea>
        <button class="btn btn-orange" type="submit">Opslaan</button>
        <a class="btn btn-pink" href="content.php">Annuleren</a>
    </form>
<?php else: ?>
    <a class="btn btn-orange add" href="content.php?new=1">+ Nieuwe content</a>
<?php endif; ?>

    <div class="table-scroll">
        <table>
            <tr><th>ID</th><th>Titel</th><th>Beschrijving</th><th>Bijgewerkt</th><th></th></tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= e($r['title']) ?></td>
                    <td><?= e(mb_strimwidth($r['description'], 0, 80, '…')) ?></td>
                    <td><?= e($r['updated_at']) ?></td>
                    <td class="actions">
                        <a href="content.php?edit=<?= (int)$r['id'] ?>">Aanpassen</a>
                        <form method="post" onsubmit="return confirm('Deze content verwijderen?')">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button type="submit" class="link-danger">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?><tr><td colspan="5">Nog geen content.</td></tr><?php endif; ?>
        </table>
    </div>
<?php admin_footer(); ?>