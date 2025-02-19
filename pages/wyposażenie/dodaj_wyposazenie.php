<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Dane przychodzą w formie POST 
$nazwa = $_POST['nazwaWyposazenia'] ?? '';

if (empty($nazwa)) {
    echo json_encode(['success' => false, 'error' => 'Nazwa wyposażenia jest wymagana.']);
    exit;
}

try {
    // Wstawienie nowego rekordu
    $query = "
        INSERT INTO rezerwacje_hotelowe.wyposażenie (nazwa_wyposażenia)
        VALUES (:nazwa_wyposazenia)
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['nazwa_wyposazenia' => $nazwa]);

    echo json_encode(['success' => true, 'message' => 'Wyposażenie zostało pomyślnie dodane.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania wyposażenia: ' . $e->getMessage()]);
}
?>
