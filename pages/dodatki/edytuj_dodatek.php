<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Dane w formacie JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['nazwa'], $data['cena'])) {
    echo json_encode(['error' => 'Brak wymaganych danych wejściowych (id, nazwa, cena).']);
    exit;
}

$id = (int) $data['id'];
$nazwa = trim($data['nazwa']);
$cena = floatval($data['cena']);

if ($id <= 0 || empty($nazwa)) {
    echo json_encode(['error' => 'Niepoprawne dane wejściowe.']);
    exit;
}

try {
    // Sprawdź, czy dany dodatek istnieje
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.dodatek WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono dodatku o podanym ID.']);
        exit;
    }

    // Aktualizacja
    $updateQuery = "
        UPDATE rezerwacje_hotelowe.dodatek
        SET nazwa_dodatku = :nazwa, cena = :cena
        WHERE id = :id
    ";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([
        'nazwa' => $nazwa,
        'cena' => $cena,
        'id' => $id
    ]);

    echo json_encode(['success' => 'Dodatek został zaktualizowany.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas aktualizacji dodatku: ' . $e->getMessage()]);
}
?>
