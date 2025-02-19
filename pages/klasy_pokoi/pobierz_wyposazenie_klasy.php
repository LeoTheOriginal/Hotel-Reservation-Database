<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Brak parametru ID klasy pokoju.']);
    exit;
}

$klasaId = (int) $_GET['id'];

try {
    // Sprawdź, czy klasa istnieje
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.klasa_pokoju WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $klasaId]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono klasy pokoju o podanym ID.']);
        exit;
    }

    // Pobierz ID wyposażenia dla tej klasy
    $query = "
        SELECT wyposażenie_id
        FROM rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy
        WHERE klasa_pokoju_id = :id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $klasaId]);

    // Zwrócimy tablicę samych ID wyposażenia
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania wyposażenia klasy: ' . $e->getMessage()]);
}
?>
