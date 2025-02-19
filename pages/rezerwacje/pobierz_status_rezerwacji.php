<?php

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Zapytanie SQL do pobrania statusów rezerwacji
    $stmt = $pdo->query('SELECT id, nazwa_statusu AS name FROM rezerwacje_hotelowe.status_rezerwacji ORDER BY id');

    // Pobranie wyników jako tablicy asocjacyjnej
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Jeśli brak wyników, zwróć pustą tablicę
    if (empty($statuses)) {
        echo json_encode([]);
    } else {
        // Wyślij dane jako JSON
        echo json_encode($statuses);
    }
} catch (PDOException $e) {
    // Obsługa błędów bazy danych
    echo json_encode(['error' => 'Błąd podczas pobierania statusów rezerwacji: ' . $e->getMessage()]);
}
