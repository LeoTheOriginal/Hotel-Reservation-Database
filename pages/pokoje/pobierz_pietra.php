<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query('SELECT id, numer_piętra AS nazwa FROM rezerwacje_hotelowe.piętro ORDER BY numer_piętra');
    $floors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($floors);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
