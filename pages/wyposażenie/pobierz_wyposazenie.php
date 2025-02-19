<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $query = "
        SELECT w.id,
               w.nazwa_wyposażenia,
               CASE WHEN COUNT(wpdk.wyposażenie_id) = 0 THEN true ELSE false END AS is_deletable
        FROM rezerwacje_hotelowe.wyposażenie w
        LEFT JOIN rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy wpdk
               ON wpdk.wyposażenie_id = w.id
        GROUP BY w.id
        ORDER BY w.id
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Zwracamy tablicę asocjacyjną
    $wyposazenie = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($wyposazenie);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania wyposażenia: ' . $e->getMessage()]);
}
?>
