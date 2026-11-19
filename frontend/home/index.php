<?php
require 'config.php'; // Jouw databaseverbinding met $conn

$search = $_GET['search'] ?? '';

if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE name LIKE :search OR description LIKE :search");
    $stmt->execute([':search' => '%' . $search . '%']);
} else {
    $stmt = $conn->query("SELECT * FROM categories");
}

// Variabelen aanmaken zoals in jouw voorbeeld
$resultaten = $stmt->fetchAll();
$aantalRijen = count($resultaten);

// View inladen
require 'views/index_view.php';
