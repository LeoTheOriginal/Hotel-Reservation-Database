<?php

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query('SELECT 
        p.id,
        p.numer_pokoju,
        k.nazwa_klasy,
        s.nazwa_statusu,
        pt.numer_piętra,
        p.max_liczba_osob,
        p.klasa_pokoju_id,
        p.status_pokoju_id,
        p.piętro_id,
        CASE 
            WHEN s.nazwa_statusu != \'Dostępny\'
                OR EXISTS (
                    SELECT 1 
                    FROM rezerwacje_hotelowe.rezerwacja_pokój rp
                    JOIN rezerwacje_hotelowe.rezerwacja r ON rp.rezerwacja_id = r.id
                    WHERE rp.pokój_id = p.id
                    AND r.data_wymeldowania >= CURRENT_DATE
                )
            THEN FALSE
            ELSE TRUE
        END AS can_delete
    FROM rezerwacje_hotelowe.pokój p
    JOIN rezerwacje_hotelowe.klasa_pokoju k ON p.klasa_pokoju_id = k.id
    JOIN rezerwacje_hotelowe.status_pokoju s ON p.status_pokoju_id = s.id
    JOIN rezerwacje_hotelowe.piętro pt ON p.piętro_id = pt.id
    ORDER BY p.numer_pokoju');

    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rooms);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
