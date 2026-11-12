<?php
require 'config.php';
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_check()) {
        $error = 'Er ging iets mis. Probeer het opnieuw.';
    } else {
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([strtolower($email)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Altijd dezelfde melding, zodat niemand kan raden welke e-mails bestaan.
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'E-mailadres of wachtwoord klopt niet.';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inloggen – Driemusketiers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap">
    <h1>Welkom terug!</h1>
    <p class="lead">Log in voor goedkope gerechten voor studenten.</p>

    <?php if ($error): ?><div class="alert" role="alert"><?= e($error) ?></div><?php endif; ?>

    <form method="post" novalidate>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <label for="email">E-mailadres</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email">
        <label for="password">Wachtwoord</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
        <button type="submit" class="btn btn-orange">Inloggen</button>
    </form>

    <section class="card">
        <h2>Nog geen account?</h2>
        <p>Maak er gratis een aan en ontdek betaalbare recepten.</p>
        <a href="register.php" class="btn btn-pink">Registreren</a>
    </section>
</main>
</body>
</html>