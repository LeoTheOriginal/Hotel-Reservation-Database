<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode(['error' => 'Brak ID wyposażenia w żądaniu.']);
    exit;
}

$id = (int) $data['id'];

try {
    // Sprawdź, czy istnieje
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.wyposażenie WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono wyposażenia o podanym ID.']);
        exit;
    }

    // Sprawdź powiązania w wyposażenie_pokoju_danej_klasy
    $countQuery = "
        SELECT COUNT(*) as liczba
        FROM rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy
        WHERE wyposażenie_id = :id
    ";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute(['id' => $id]);
    $row = $countStmt->fetch(PDO::FETCH_ASSOC);

    if ($row['liczba'] > 0) {
        echo json_encode(['error' => 'Nie można usunąć wyposażenia, ponieważ jest używane w tabeli wyposażenie_pokoju_danej_klasy.']);
        exit;
    }

    // Usunięcie
    $deleteQuery = "DELETE FROM rezerwacje_hotelowe.wyposażenie WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute(['id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Wyposażenie zostało pomyślnie usunięte.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas usuwania wyposażenia: ' . $e->getMessage()]);
}
?>
