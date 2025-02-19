<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $query = "SELECT id, data, opis, szczegóły
              FROM rezerwacje_hotelowe.logi_systemowe
              ORDER BY data DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $logi = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($logi);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania logów: ' . $e->getMessage()]);
}
?>
