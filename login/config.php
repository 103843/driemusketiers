<?php
// Gedeelde configuratie: sessie, database (MySQL) en helpers.
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();

// Database-instellingen (XAMPP: root zonder wachtwoord, MAMP: root/root)
$dbHost = 'localhost';
$dbPort = 3306;
$dbName = 'ontkokingberoeps';
$dbUser = 'root';
$dbPass = '';

try {
    $db = new PDO(
        "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $ex) {
    http_response_code(500);
    exit('Kan geen verbinding maken met de database.');
}

// Tabel `gebruiker` bestaat al: id, naam, email, wachtwoord, rol.
// Let op: `rol` heeft geen standaardwaarde, geef bij registratie dus 'user' mee.

$db->exec("CREATE TABLE IF NOT EXISTS content (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
function require_login(): void {
    if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
}

function require_admin(): void {
    global $db;
    require_login();
    $stmt = $db->prepare('SELECT rol FROM gebruiker WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    if ($stmt->fetchColumn() !== 'admin') { http_response_code(403); exit('Geen toegang.'); }
}
function is_admin(): bool {
    global $db;
    if (empty($_SESSION['user_id'])) return false;
    $stmt = $db->prepare('SELECT rol FROM gebruiker WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchColumn() === 'admin';
}
function flash(?string $msg = null): ?string {
    if ($msg !== null) { $_SESSION['flash'] = $msg; return null; }
    $m = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $m;
}
