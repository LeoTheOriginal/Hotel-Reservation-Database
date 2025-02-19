<?php
include 'config.php';

try {
    $stmt = $pdo->query('SELECT VERSION()');
    $version = $stmt->fetch();
    echo "Połączono z bazą danych PostgreSQL, wersja: " . $version[0];
} catch (PDOException $e) {
    echo "Błąd: " . $e->getMessage();
}
?>
