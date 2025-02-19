<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode(['error' => 'Brak ID loga w żądaniu.']);
    exit;
}

$id = (int) $data['id'];

try {
    // Sprawdź, czy log istnieje
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.logi_systemowe WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono loga o podanym ID.']);
        exit;
    }

    // Usunięcie
    $deleteQuery = "DELETE FROM rezerwacje_hotelowe.logi_systemowe WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute(['id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Log został pomyślnie usunięty.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas usuwania loga: ' . $e->getMessage()]);
}
?>
