<?php
require 'config.php';
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!csrf_check()) $errors[] = 'Er ging iets mis. Probeer het opnieuw.';
    if ($name === '' || mb_strlen($name) > 60) $errors[] = 'Vul een naam in (max. 60 tekens).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Vul een geldig e-mailadres in.';
    if (strlen($password) < 8) $errors[] = 'Wachtwoord moet minimaal 8 tekens zijn.';
    if ($password !== $confirm) $errors[] = 'Wachtwoorden komen niet overeen.';

    if (!$errors) {
        try {
            $stmt = $db->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$db->lastInsertId();
            $_SESSION['name'] = $name;
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $ex) {
            $errors[] = 'Dit e-mailadres is al in gebruik.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registreren – Driemusketiers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap">
    <h1>Account maken</h1>
    <p class="lead">Slim eten met een kleine portemonnee.</p>

    <?php foreach ($errors as $err): ?><div class="alert" role="alert"><?= e($err) ?></div><?php endforeach; ?>

    <form method="post" novalidate>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <label for="name">Naam</label>
        <input type="text" id="name" name="name" value="<?= e($name) ?>" required autocomplete="name">
        <label for="email">E-mailadres</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email">
        <label for="password">Wachtwoord</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">
        <label for="confirm">Herhaal wachtwoord</label>
        <input type="password" id="confirm" name="confirm" required autocomplete="new-password">
        <button type="submit" class="btn btn-orange">Registreren</button>
    </form>

    <p class="center">Al een account? <a href="login.php">Inloggen</a></p>
</main>
</body>
</html>