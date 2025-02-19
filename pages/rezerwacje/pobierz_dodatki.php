<?php
// pobierz_dodatki.php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query('SELECT id, nazwa_dodatku, cena FROM rezerwacje_hotelowe.dodatek ORDER BY nazwa_dodatku');
    $addons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($addons);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
