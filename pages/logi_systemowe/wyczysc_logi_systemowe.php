<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $query = "TRUNCATE TABLE rezerwacje_hotelowe.logi_systemowe RESTART IDENTITY"; 

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Wyczyszczono wszystkie logi systemowe.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas czyszczenia logów: ' . $e->getMessage()]);
}
?>
