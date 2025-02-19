<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query('SELECT "id", "imię", "nazwisko", "numer_telefonu", "adres_email" FROM rezerwacje_hotelowe."gość" ORDER BY "nazwisko", "imię"');
    $goscie = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($goscie);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
