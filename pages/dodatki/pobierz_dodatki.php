<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $query = "
        SELECT d.id,
               d.nazwa_dodatku,
               d.cena,
               CASE WHEN COUNT(rd.dodatek_id) = 0 THEN true ELSE false END AS is_deletable
        FROM rezerwacje_hotelowe.dodatek d
        LEFT JOIN rezerwacje_hotelowe.rezerwacja_dodatek rd
               ON rd.dodatek_id = d.id
        GROUP BY d.id
        ORDER BY d.id
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $dodatki = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($dodatki);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania dodatków: ' . $e->getMessage()]);
}
?>
