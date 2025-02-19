<?php

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Odczytaj dane wejściowe
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe dane wejściowe.']);
    exit;
}

// Pobierz i zdefiniuj zmienne
$reservation_id = isset($input['reservation_id']) ? intval($input['reservation_id']) : null;
$guest_id = isset($input['guest_id']) ? intval($input['guest_id']) : null;
$room_id = isset($input['room_id']) ? intval($input['room_id']) : null;
$check_in_date = isset($input['check_in_date']) ? $input['check_in_date'] : null;
$check_out_date = isset($input['check_out_date']) ? $input['check_out_date'] : null;
$payment_status_id = isset($input['payment_status_id']) ? intval($input['payment_status_id']) : null;
$reservation_status_id = isset($input['reservation_status_id']) ? intval($input['reservation_status_id']) : null;
$amount = isset($input['amount']) ? floatval($input['amount']) : 0.0;
$addons = isset($input['addons']) && is_array($input['addons']) ? array_map('intval', $input['addons']) : [];

// Walidacja wymaganych pól
if (
    !$reservation_id ||
    !$guest_id ||
    !$room_id ||
    !$check_in_date ||
    !$check_out_date ||
    !$payment_status_id ||
    !$reservation_status_id
) {
    echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych do aktualizacji rezerwacji.']);
    exit;
}

try {
    // Rozpoczęcie transakcji
    $pdo->beginTransaction();

    // Przygotowanie tablicy pokoi w formacie PostgreSQL
    $rooms_pg = '{' . $room_id . '}';

    // Przygotowanie tablicy dodatków w formacie PostgreSQL
    $addons_pg = !empty($addons) ? '{' . implode(',', $addons) . '}' : null;

    // Wywołanie funkcji PostgreSQL
    $stmt = $pdo->prepare("SELECT rezerwacje_hotelowe.zaktualizuj_rezerwacje(
        :p_reservation_id,
        :p_guest_id,
        :p_check_in_date,
        :p_check_out_date,
        :p_rooms,
        :p_payment_status_id,
        :p_reservation_status_id,
        :p_amount,
        :p_addons
    )");

    $stmt->bindValue(':p_reservation_id', $reservation_id, PDO::PARAM_INT);
    $stmt->bindValue(':p_guest_id', $guest_id, PDO::PARAM_INT);
    $stmt->bindValue(':p_check_in_date', $check_in_date, PDO::PARAM_STR);
    $stmt->bindValue(':p_check_out_date', $check_out_date, PDO::PARAM_STR);
    $stmt->bindValue(':p_rooms', $rooms_pg, PDO::PARAM_STR);
    $stmt->bindValue(':p_payment_status_id', $payment_status_id, PDO::PARAM_INT);
    $stmt->bindValue(':p_reservation_status_id', $reservation_status_id, PDO::PARAM_INT);
    $stmt->bindValue(':p_amount', $amount, PDO::PARAM_STR); 
    $stmt->bindValue(':p_addons', $addons_pg, PDO::PARAM_STR); 

    $stmt->execute();

    // Zatwierdzenie transakcji
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Błąd SQL: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Błąd serwera podczas aktualizacji rezerwacji.']);
}
?>
