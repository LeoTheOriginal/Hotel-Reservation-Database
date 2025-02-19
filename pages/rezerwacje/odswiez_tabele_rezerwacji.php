<?php

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query('
        SELECT 
            r.id,
            g.imię || \' \' || g.nazwisko AS guest, -- PostgreSQL konkatenacja
            p.numer_pokoju AS room,
            r.data_zameldowania AS check_in,
            r.data_wymeldowania AS check_out,
            sr.nazwa_statusu AS reservation_status, -- Dodanie statusu rezerwacji
            sp.nazwa_statusu AS payment_status,
            r.kwota_rezerwacji AS amount
        FROM rezerwacje_hotelowe.rezerwacja r
        JOIN rezerwacje_hotelowe.gość g ON r.gość_id = g.id
        JOIN rezerwacje_hotelowe.rezerwacja_pokój rp ON r.id = rp.rezerwacja_id
        JOIN rezerwacje_hotelowe.pokój p ON rp.pokój_id = p.id
        JOIN rezerwacje_hotelowe.status_rezerwacji sr ON r.status_rezerwacji_id = sr.id -- Połączenie z tabelą status_rezerwacji
        JOIN rezerwacje_hotelowe.status_płatności sp ON r.status_płatności_id = sp.id
        ORDER BY r.data_zameldowania DESC
    ');

    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugowanie
    if (empty($reservations)) {
        echo json_encode(['error' => 'Brak rezerwacji w bazie danych.']);
    } else {
        echo json_encode($reservations);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
