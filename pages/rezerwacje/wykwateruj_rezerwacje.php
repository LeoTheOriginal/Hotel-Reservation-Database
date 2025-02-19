<?php
require_once '../../config.php';

header('Content-Type: application/json');

// Odczytanie danych wejściowych
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['reservationId'])) {
    echo json_encode(['success' => false, 'error' => 'Brak ID rezerwacji.']);
    exit;
}

$reservationId = $data['reservationId'];

try {
    // Połączenie z bazą danych
    $pdo = new PDO($dsn, $username, $password, $options);

    // Wywołanie funkcji wykwateruj_rezerwacje
    $stmt = $pdo->prepare('SELECT rezerwacje_hotelowe.wykwateruj_rezerwacje(:reservationId)');
    $stmt->bindParam(':reservationId', $reservationId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
