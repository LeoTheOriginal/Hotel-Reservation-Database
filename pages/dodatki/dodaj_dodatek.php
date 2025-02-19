<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Dane z POST
$nazwa = $_POST['nazwaDodatku'] ?? '';
$cena = $_POST['cena'] ?? '';

if (empty($nazwa) || empty($cena)) {
    echo json_encode(['success' => false, 'error' => 'Wszystkie pola (nazwa, cena) są wymagane.']);
    exit;
}

try {
    $cenaValue = floatval($cena);

    $query = "INSERT INTO rezerwacje_hotelowe.dodatek (nazwa_dodatku, cena)
              VALUES (:nazwa, :cena)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'nazwa' => $nazwa,
        'cena' => $cenaValue
    ]);

    echo json_encode(['success' => true, 'message' => 'Dodatek został pomyślnie dodany.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania dodatku: ' . $e->getMessage()]);
}
?>
