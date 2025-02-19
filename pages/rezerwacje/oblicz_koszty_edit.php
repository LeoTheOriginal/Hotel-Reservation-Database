<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Pobierz dane z żądania
    $inputData = json_decode(file_get_contents('php://input'), true);
    $roomIds = $inputData['roomIds'] ?? [];
    $checkInDate = $inputData['checkInDate'] ?? null;
    $checkOutDate = $inputData['checkOutDate'] ?? null;
    $addons = $inputData['addons'] ?? [];

    // Sprawdź, czy wszystkie wymagane dane są dostępne
    if (empty($roomIds) || !$checkInDate || !$checkOutDate) {
        echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych.']);
        exit;
    }

    // Przygotuj zapytanie do funkcji SQL
    $roomIdsAsArray = '{' . implode(',', $roomIds) . '}'; 
    $addonsAsArray = !empty($addons) ? '{' . implode(',', $addons) . '}' : null; 

    $stmt = $pdo->prepare(
        'SELECT rezerwacje_hotelowe.oblicz_koszt_rezerwacji_z_parametrami(:room_ids, :check_in, :check_out, :addons) AS total_cost'
    );
    $stmt->bindValue(':room_ids', $roomIdsAsArray, PDO::PARAM_STR);
    $stmt->bindValue(':check_in', $checkInDate, PDO::PARAM_STR);
    $stmt->bindValue(':check_out', $checkOutDate, PDO::PARAM_STR);
    if ($addonsAsArray) {
        $stmt->bindValue(':addons', $addonsAsArray, PDO::PARAM_STR);
    } else {
        $stmt->bindValue(':addons', null, PDO::PARAM_NULL);
    }
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Weryfikacja wyniku
    if ($result && $result['total_cost'] !== null) {
        echo json_encode(['success' => true, 'totalCost' => $result['total_cost']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nie udało się obliczyć kosztów.']);
    }
} catch (Exception $e) {
    // Obsługa błędów
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
