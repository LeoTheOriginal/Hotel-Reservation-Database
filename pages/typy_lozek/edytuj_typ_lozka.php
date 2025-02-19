<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['nazwa'], $data['liczba_osob'])) {
    echo json_encode(['error' => 'Brak wymaganych danych (id, nazwa, liczba_osob).']);
    exit;
}

$id = (int)$data['id'];
$nazwa = trim($data['nazwa']);
$liczba = (int)$data['liczba_osob'];

if ($id <= 0 || empty($nazwa) || $liczba <= 0) {
    echo json_encode(['error' => 'Niepoprawne dane wejściowe.']);
    exit;
}

try {
    // Sprawdzenie istnienia rekordu
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.typ_łóżka WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono typu łóżka o podanym ID.']);
        exit;
    }

    // Aktualizacja
    $updateQuery = "
        UPDATE rezerwacje_hotelowe.typ_łóżka
        SET nazwa_typu = :nazwa,
            liczba_osob = :liczba
        WHERE id = :id
    ";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([
        'nazwa' => $nazwa,
        'liczba' => $liczba,
        'id' => $id
    ]);

    echo json_encode(['success' => 'Typ łóżka został zaktualizowany.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas aktualizacji typu łóżka: ' . $e->getMessage()]);
}
?>
