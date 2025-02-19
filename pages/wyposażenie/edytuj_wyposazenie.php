<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

// Sprawdzamy, czy otrzymaliśmy ID oraz nową nazwę
if (!isset($data['id']) || !isset($data['nazwa'])) {
    echo json_encode(['error' => 'Brak wymaganych danych wejściowych.']);
    exit;
}

$id = (int) $data['id'];
$nazwa = trim($data['nazwa']);

if ($id <= 0 || empty($nazwa)) {
    echo json_encode(['error' => 'Niepoprawne dane wejściowe.']);
    exit;
}

try {
    // Sprawdź, czy dany rekord istnieje
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.wyposażenie WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono wyposażenia o podanym ID.']);
        exit;
    }

    // Aktualizacja
    $updateQuery = "
        UPDATE rezerwacje_hotelowe.wyposażenie
        SET nazwa_wyposażenia = :nazwa
        WHERE id = :id
    ";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([
        'nazwa' => $nazwa,
        'id' => $id
    ]);

    echo json_encode(['success' => 'Wyposażenie zostało zaktualizowane.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas aktualizacji wyposażenia: ' . $e->getMessage()]);
}
?>
