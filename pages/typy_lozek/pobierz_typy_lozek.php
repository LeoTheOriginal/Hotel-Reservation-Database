<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    /*
    Wreszcze obluguje is_deletable
    */
    $query = "
        SELECT t.id,
               t.nazwa_typu,
               t.liczba_osob,
               CASE WHEN COUNT(tlp.typ_łóżka_id) = 0 THEN true ELSE false END AS is_deletable
        FROM rezerwacje_hotelowe.typ_łóżka t
        LEFT JOIN rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy tlp
               ON tlp.typ_łóżka_id = t.id
        GROUP BY t.id
        ORDER BY t.id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $typyLozek = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($typyLozek);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania typów łóżek: ' . $e->getMessage()]);
}
?>
