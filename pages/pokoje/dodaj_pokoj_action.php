<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Odbieranie danych z żądania
$data = json_decode(file_get_contents('php://input'), true);

$numer_pokoju = $data['room_number'] ?? '';
$piętro_id = $data['floor'] ?? null;
$klasa_pokoju_id = $data['room_class'] ?? null;
$status_pokoju_id = $data['room_status'] ?? null;

// Walidacja danych wejściowych
if (empty($numer_pokoju) || !$piętro_id || !$klasa_pokoju_id || !$status_pokoju_id) {
    echo json_encode(['success' => false, 'error' => 'Nie wszystkie pola zostały wypełnione']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $stmt = $pdo->prepare('
        INSERT INTO rezerwacje_hotelowe.pokój (numer_pokoju, piętro_id, klasa_pokoju_id, status_pokoju_id)
        VALUES (:numer_pokoju, :pietro_id, :klasa_pokoju_id, :status_pokoju_id)
    ');

    // Wykonanie zapytania z bindowaniem parametrów
    $stmt->execute([
        ':numer_pokoju' => $numer_pokoju,
        ':pietro_id' => $piętro_id,
        ':klasa_pokoju_id' => $klasa_pokoju_id,
        ':status_pokoju_id' => $status_pokoju_id
    ]);

    // Sprawdzenie wyniku
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nie udało się dodać pokoju.']);
    }
} catch (PDOException $e) {
    // Obsługa błędów SQL
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
