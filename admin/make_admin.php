<?php
// Gebruik: php make_admin.php email@voorbeeld.nl
if (PHP_SAPI !== 'cli') exit('Alleen via de terminal.');
require 'config.php';
$email = strtolower($argv[1] ?? '');
$stmt = $db->prepare("UPDATE users SET role = 'admin' WHERE email = ?");
$stmt->execute([$email]);
echo $stmt->rowCount() ? "$email is nu admin.\n" : "Geen gebruiker gevonden met dit e-mailadres. Registreer eerst.\n";