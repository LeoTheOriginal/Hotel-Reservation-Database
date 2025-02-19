<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query('SELECT id, nazwa_statusu AS nazwa FROM rezerwacje_hotelowe.status_pokoju ORDER BY nazwa_statusu');
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($statuses);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
