<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Lepiej było użyć operator || zamiast CONCAT
    $stmt = $pdo->query('SELECT id, imię || \' \' || nazwisko AS nazwa FROM rezerwacje_hotelowe.gość ORDER BY nazwisko, imię');
    $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($guests);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
