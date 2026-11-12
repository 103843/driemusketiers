<?php
require '_layout.php';
$errors = [];
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) { exit('Ongeldig verzoek.'); }
    $id = (int)($_POST['id'] ?? 0);

    if (($_POST['action'] ?? '') === 'delete') {
        if ($id === (int)$_SESSION['user_id']) flash('Je kunt jezelf niet verwijderen.');
        else { $db->prepare('DELETE FROM users WHERE id = ?')->execute([$id]); flash('Klant verwijderd.'); }
        header('Location: users.php'); exit;
    }

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $role = in_array($_POST['role'] ?? '', ['user', 'admin'], true) ? $_POST['role'] : 'user';
    $pw = $_POST['password'] ?? '';
    if ($id === (int)$_SESSION['user_id']) $role = 'admin'; // jezelf niet degraderen

    if ($name === '' || mb_strlen($name) > 60) $errors[] = 'Vul een naam in (max. 60 tekens).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Vul een geldig e-mailadres in.';
    if ($pw !== '' && strlen($pw) < 8) $errors[] = 'Nieuw wachtwoord: minimaal 8 tekens.';

    if (!$errors) {
        try {
            $db->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?')->execute([$name, $email, $role, $id]);
            if ($pw !== '') $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
            flash('Klant opgeslagen.');
            header('Location: users.php'); exit;
        } catch (PDOException $ex) { $errors[] = 'Dit e-mailadres is al in gebruik.'; }
    }
    $editId = $id;
}

$edit = null;
if ($editId) {
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$editId]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
$rows = $db->query('SELECT id, name, email, role, created_at FROM users ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

admin_header('Klanten');
foreach ($errors as $err) echo '<div class="alert">' . e($err) . '</div>';
if ($edit): ?>
    <form method="post" class="panel">
        <h2>Klant aanpassen</h2>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <label>Naam</label><input type="text" name="name" value="<?= e($_POST['name'] ?? $edit['name']) ?>" required>
        <label>E-mailadres</label><input type="email" name="email" value="<?= e($_POST['email'] ?? $edit['email']) ?>" required>
        <label>Rol</label>
        <select name="role">
            <option value="user" <?= $edit['role'] === 'user' ? 'selected' : '' ?>>Klant</option>
            <option value="admin" <?= $edit['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <label>Nieuw wachtwoord (leeg laten = ongewijzigd)</label><input type="password" name="password" autocomplete="new-password">
        <button class="btn btn-orange" type="submit">Opslaan</button>
        <a class="btn btn-pink" href="users.php">Annuleren</a>
    </form>
<?php endif; ?>

    <div class="table-scroll">
        <table>
            <tr><th>ID</th><th>Naam</th><th>E-mail</th><th>Rol</th><th>Aangemaakt</th><th></th></tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= e($r['name']) ?></td>
                    <td><?= e($r['email']) ?></td>
                    <td><?= e($r['role']) ?></td>
                    <td><?= e($r['created_at']) ?></td>
                    <td class="actions">
                        <a href="users.php?edit=<?= (int)$r['id'] ?>">Aanpassen</a>
                        <?php if ((int)$r['id'] !== (int)$_SESSION['user_id']): ?>
                            <form method="post" onsubmit="return confirm('Deze klant verwijderen?')">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button type="submit" class="link-danger">Verwijderen</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php admin_footer(); ?>