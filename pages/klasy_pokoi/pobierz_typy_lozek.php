<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $query = "SELECT id, nazwa_typu FROM rezerwacje_hotelowe.typ_łóżka ORDER BY id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $lozka = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($lozka);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania typów łóżek: ' . $e->getMessage()]);
}
?>
