<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode(['error' => 'Brak ID dodatku w żądaniu.']);
    exit;
}

$id = (int) $data['id'];

try {
    // Sprawdź, czy dodatek istnieje
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.dodatek WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono dodatku o podanym ID.']);
        exit;
    }

    // Sprawdź powiązania w rezerwacja_dodatek
    $countQuery = "
        SELECT COUNT(*) AS liczba
        FROM rezerwacje_hotelowe.rezerwacja_dodatek
        WHERE dodatek_id = :id
    ";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute(['id' => $id]);
    $row = $countStmt->fetch(PDO::FETCH_ASSOC);

    if ($row['liczba'] > 0) {
        echo json_encode(['error' => 'Nie można usunąć dodatku, ponieważ jest używany w tabeli rezerwacja_dodatek.']);
        exit;
    }

    // Usuń dodatek
    $deleteQuery = "DELETE FROM rezerwacje_hotelowe.dodatek WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute(['id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Dodatek został pomyślnie usunięty.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas usuwania dodatku: ' . $e->getMessage()]);
}
?>
