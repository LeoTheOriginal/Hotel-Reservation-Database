<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query('SELECT id, nazwa_klasy AS nazwa FROM rezerwacje_hotelowe.klasa_pokoju ORDER BY id');
    $roomClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($roomClasses);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
