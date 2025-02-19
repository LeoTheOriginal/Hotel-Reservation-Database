<?php

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Odbieranie danych z żądania
$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;

// Sprawdzenie, czy ID zostało przekazane
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Brak ID klasy do usunięcia.']);
    exit;
}

try {
    // Rozpoczęcie transakcji
    $pdo->beginTransaction();

    // 1. Sprawdzenie, czy istnieją pokoje używające tej klasy
    $checkRoomsStmt = $pdo->prepare('SELECT COUNT(*) AS count FROM rezerwacje_hotelowe.pokój WHERE klasa_pokoju_id = :id');
    $checkRoomsStmt->execute([':id' => $id]);
    $roomsResult = $checkRoomsStmt->fetch(PDO::FETCH_ASSOC);

    if ($roomsResult['count'] > 0) {
        // Jeśli klasa jest przypisana do pokoi, nie można jej usunąć
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Nie można usunąć klasy, która jest przypisana do jednego lub więcej pokoi.']);
        exit;
    }

    // 2. Usunięcie powiązań z wyposażeniem pokoju
    $deleteEquipmentStmt = $pdo->prepare('DELETE FROM rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy WHERE klasa_pokoju_id = :id');
    $deleteEquipmentStmt->execute([':id' => $id]);

    // 3. Usunięcie powiązań z typami łóżek
    $deleteBedsStmt = $pdo->prepare('DELETE FROM rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy WHERE klasa_pokoju_id = :id');
    $deleteBedsStmt->execute([':id' => $id]);

    // 4. Usunięcie samej klasy pokoju
    $deleteClassStmt = $pdo->prepare('DELETE FROM rezerwacje_hotelowe.klasa_pokoju WHERE id = :id');
    $deleteClassStmt->execute([':id' => $id]);

    if ($deleteClassStmt->rowCount() > 0) {
        // Zatwierdzenie transakcji
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Klasa pokoju została pomyślnie usunięta.']);
    } else {
        // Jeśli klasa nie została znaleziona
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Nie znaleziono klasy o podanym ID.']);
    }
} catch (PDOException $e) {
    // Wycofanie transakcji w przypadku błędu
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Obsługa błędów SQL
    echo json_encode(['success' => false, 'error' => 'Błąd podczas usuwania klasy pokoju: ' . $e->getMessage()]);
}
