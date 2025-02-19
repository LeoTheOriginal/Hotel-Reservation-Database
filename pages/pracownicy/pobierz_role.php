<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO($dsn, $db_user, $db_password);

    $query = "SELECT id, nazwa_roli FROM rezerwacje_hotelowe.rola ORDER BY nazwa_roli";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $role = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($role);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania ról: ' . $e->getMessage()]);
}
?>
