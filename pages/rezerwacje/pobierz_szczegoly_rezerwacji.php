<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Sprawdź, czy ID zostało przekazane jako parametr GET
if (isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
} else {
    echo json_encode(['success' => false, 'error' => 'Brak ID rezerwacji']);
    exit;
}

$reservationId = intval($_GET['id']);

try {
    // Przygotuj i wykonaj zapytanie do funkcji
    $stmt = $pdo->prepare("SELECT rezerwacje_hotelowe.pobierz_szczegoly_rezerwacji(:reservation_id) AS result");
    $stmt->bindValue(':reservation_id', $reservationId, PDO::PARAM_INT);
    $stmt->execute();

    // Pobierz wynik
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $result = json_decode($row['result'], true);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'error' => 'Brak danych do zwrócenia']);
    }
} catch (PDOException $e) {
    // Obsługa błędów
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
