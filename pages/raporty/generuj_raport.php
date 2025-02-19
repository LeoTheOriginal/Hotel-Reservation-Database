<?php
include_once __DIR__ . '/../../config.php';

$type = $_GET['type'] ?? null;

try {
    $pdo = new PDO($dsn, $db_user, $db_password, [
        PDO::ATTR_EMULATE_PREPARES => false, // Wyłącz emulację przygotowań
        PDO::ATTR_STRINGIFY_FETCHES => false, // Nie przekształcaj typów na stringi
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $data = ['success' => true];

    if ($type === 'financial') {
        $query = "SELECT DATE_TRUNC('month', r.data_zameldowania) AS miesiąc, SUM(r.kwota_rezerwacji) AS przychód
                  FROM rezerwacje_hotelowe.rezerwacja r
                  WHERE r.status_rezerwacji_id = (SELECT id FROM rezerwacje_hotelowe.status_rezerwacji WHERE nazwa_statusu = 'Zrealizowana')
                  GROUP BY miesiąc
                  ORDER BY miesiąc";
        $stmt = $pdo->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data['chart'] = [
            'labels' => array_map(function($row) { return date("M Y", strtotime($row['miesiąc'])); }, $rows),
            'data' => array_map(function($row) { return (float)$row['przychód']; }, $rows),
            'label' => 'Przychód miesięczny (PLN)'
        ];
        $data['table'] = $rows;
    } elseif ($type === 'rooms') {
        $query = "SELECT p.numer_pokoju, COUNT(r.id) AS liczba_rezerwacji
                  FROM rezerwacje_hotelowe.pokój p
                  LEFT JOIN rezerwacje_hotelowe.rezerwacja_pokój rp ON p.id = rp.pokój_id
                  LEFT JOIN rezerwacje_hotelowe.rezerwacja r ON rp.rezerwacja_id = r.id
                  GROUP BY p.numer_pokoju
                  ORDER BY liczba_rezerwacji DESC";
        $stmt = $pdo->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data['chart'] = [
            'labels' => array_column($rows, 'numer_pokoju'),
            'data' => array_column($rows, 'liczba_rezerwacji'),
            'label' => 'Liczba rezerwacji pokoi'
        ];
        $data['table'] = $rows;
    } elseif ($type === 'guests') {
        $query = "SELECT g.id AS gość_id, g.imię, g.nazwisko, COUNT(r.id) FILTER (WHERE sr.nazwa_statusu NOT IN ('Anulowana')) AS liczba_aktywnych_rezerwacji, 
                         COUNT(r.id) AS liczba_wszystkich_rezerwacji
                  FROM rezerwacje_hotelowe.gość g
                  LEFT JOIN rezerwacje_hotelowe.rezerwacja r ON g.id = r.gość_id
                  LEFT JOIN rezerwacje_hotelowe.status_rezerwacji sr ON r.status_rezerwacji_id = sr.id
                  GROUP BY g.id, g.imię, g.nazwisko
                  ORDER BY liczba_wszystkich_rezerwacji DESC";
        $stmt = $pdo->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Przygotowanie danych dla wykresu
        $labels = array_map(function($row) {
            return $row['imię'] . ' ' . $row['nazwisko'];
        }, $rows);

        $activeReservations = array_map(function($row) {
            return (int)$row['liczba_aktywnych_rezerwacji'];
        }, $rows);

        $totalReservations = array_map(function($row) {
            return (int)$row['liczba_wszystkich_rezerwacji'];
        }, $rows);

        $data['chart'] = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Aktywne rezerwacje',
                    'data' => $activeReservations,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)'
                ],
                [
                    'label' => 'Wszystkie rezerwacje',
                    'data' => $totalReservations,
                    'backgroundColor' => 'rgba(153, 102, 255, 0.6)'
                ]
            ]
        ];
        $data['table'] = $rows;
    } else {
        throw new Exception('Nieprawidłowy typ raportu.');
    }

    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
