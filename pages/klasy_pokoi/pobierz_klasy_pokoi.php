<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $query = "
        SELECT kp.id,
               kp.nazwa_klasy,
               kp.cena_podstawowa,
               -- Gdy COUNT(p.id) > 0 => klasa nie jest usuwalna
               CASE WHEN COUNT(p.id) = 0 THEN true ELSE false END AS is_deletable
        FROM rezerwacje_hotelowe.klasa_pokoju kp
        LEFT JOIN rezerwacje_hotelowe.pokój p ON p.klasa_pokoju_id = kp.id
        GROUP BY kp.id
        ORDER BY kp.id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $klasy = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($klasy);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania klas pokoi: ' . $e->getMessage()]);
}
?>
