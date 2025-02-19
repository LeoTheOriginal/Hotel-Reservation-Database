<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode(['error' => 'Brak ID typu łóżka w żądaniu.']);
    exit;
}

$id = (int)$data['id'];

try {
    // Sprawdź, czy typ łóżka istnieje
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.typ_łóżka WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono typu łóżka o podanym ID.']);
        exit;
    }

    // Sprawdź powiązania
    $countQuery = "
        SELECT COUNT(*) AS liczba
        FROM rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy
        WHERE typ_łóżka_id = :id
    ";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute(['id' => $id]);
    $row = $countStmt->fetch(PDO::FETCH_ASSOC);

    if ($row['liczba'] > 0) {
        echo json_encode(['error' => 'Nie można usunąć tego typu łóżka, ponieważ jest używany w tabeli typ_łóżka_pokoju_danej_klasy.']);
        exit;
    }

    // Usuń
    $deleteQuery = "DELETE FROM rezerwacje_hotelowe.typ_łóżka WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute(['id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Typ łóżka został pomyślnie usunięty.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas usuwania typu łóżka: ' . $e->getMessage()]);
}
?>
