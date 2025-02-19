<?php
require_once __DIR__ . '/../../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

try {
    // Pobranie danych z żądania POST
    $input = json_decode(file_get_contents('php://input'), true);

    $startDate = $input['checkIn'] ?? null;
    $endDate = $input['checkOut'] ?? null;
    $klasaPokojuId = $input['roomClass'] ?? null;

    // Przygotowanie zapytania do funkcji sprawdz_dostepne_pokoje_z_parametrami
    $query = '
        SELECT pokoj_id, numer_pokoju, nazwa_klasy, max_liczba_osob
        FROM rezerwacje_hotelowe.sprawdz_dostepne_pokoje_z_parametrami(:startDate, :endDate, NULL, :klasaPokojuId)
    ';
    $stmt = $pdo->prepare($query);

    // Przypisanie parametrów
    $stmt->bindValue(':startDate', $startDate);
    $stmt->bindValue(':endDate', $endDate);
    $stmt->bindValue(':klasaPokojuId', $klasaPokojuId);

    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($rooms) {
        echo json_encode(['rooms' => $rooms]);
    } else {
        echo json_encode(['error' => 'Brak dostępnych pokoi']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
