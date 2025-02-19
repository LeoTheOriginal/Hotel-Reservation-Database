<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query('SELECT id, nazwa_klasy FROM rezerwacje_hotelowe.klasa_pokoju ORDER BY nazwa_klasy');
    $klasy_pokoi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($klasy_pokoi);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
