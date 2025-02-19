<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $query = "SELECT id, nazwa_wyposażenia FROM rezerwacje_hotelowe.wyposażenie ORDER BY id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $wyposazenia = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($wyposazenia);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania listy wyposażenia: ' . $e->getMessage()]);
}
?>
