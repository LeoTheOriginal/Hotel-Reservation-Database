<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Pobierz ID pokoju z żądania
    $roomId = $_GET['id'] ?? null;

    if (!$roomId) {
        echo json_encode(['success' => false, 'error' => 'Brak ID pokoju.']);
        exit;
    }

    // Pobierz szczegóły pokoju
    $stmt = $pdo->prepare("
        SELECT 
            p.id, 
            p.numer_pokoju, 
            k.nazwa_klasy, 
            p.max_liczba_osob 
        FROM rezerwacje_hotelowe.pokój p
        JOIN rezerwacje_hotelowe.klasa_pokoju k ON p.klasa_pokoju_id = k.id
        WHERE p.id = :roomId
    ");
    $stmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
    $stmt->execute();

    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room) {
        echo json_encode(['success' => true, 'room' => $room]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nie znaleziono pokoju o podanym ID.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
