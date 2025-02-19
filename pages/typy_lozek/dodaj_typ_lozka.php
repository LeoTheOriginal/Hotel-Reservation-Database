<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Dane z POST 
$nazwaTypu = $_POST['nazwaTypu'] ?? '';
$liczbaOsob = $_POST['liczbaOsob'] ?? '';

if (empty($nazwaTypu) || empty($liczbaOsob)) {
    echo json_encode(['success' => false, 'error' => 'Wszystkie pola są wymagane (nazwa, liczba osób).']);
    exit;
}

try {
    $liczba = (int) $liczbaOsob;
    if ($liczba <= 0) {
        echo json_encode(['success' => false, 'error' => 'Liczba osób musi być > 0.']);
        exit;
    }

    $query = "
        INSERT INTO rezerwacje_hotelowe.typ_łóżka (nazwa_typu, liczba_osob)
        VALUES (:nazwa, :liczba)
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'nazwa' => $nazwaTypu,
        'liczba' => $liczba
    ]);

    echo json_encode(['success' => true, 'message' => 'Typ łóżka został pomyślnie dodany.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania typu łóżka: ' . $e->getMessage()]);
}
?>
