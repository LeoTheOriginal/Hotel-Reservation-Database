<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Przyjmujemy JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['nazwaKlasy'], $data['cenaPodstawowa'])) {
    echo json_encode(['success' => false, 'error' => 'Brak nazwy klasy lub ceny podstawowej.']);
    exit;
}

$nazwaKlasy = trim($data['nazwaKlasy']);
$cenaPodstawowa = (float)$data['cenaPodstawowa'];

// Tablica wyposażenia 
$wyposazenie = (isset($data['wyposazenie']) && is_array($data['wyposazenie']))
    ? $data['wyposazenie']
    : [];

// Tablica typów łóżek 
$lozka = (isset($data['lozka']) && is_array($data['lozka']))
    ? $data['lozka']
    : [];

try {
    // Rozpocznij transakcję
    $pdo->beginTransaction();

    // 1. Dodaj nową klasę do bazy
    $query = "INSERT INTO rezerwacje_hotelowe.klasa_pokoju (nazwa_klasy, cena_podstawowa)
              VALUES (:nazwa_klasy, :cena) RETURNING id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'nazwa_klasy' => $nazwaKlasy,
        'cena' => $cenaPodstawowa
    ]);
    
    $newClassId = $stmt->fetchColumn(); // Pobierz ID nowo dodanej klasy

    // 2. Wstaw wyposażenie (jeśli jest)
    if (!empty($wyposazenie)) {
        $insertEquipmentQuery = "
            INSERT INTO rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy (klasa_pokoju_id, wyposażenie_id)
            VALUES (:klasaId, :wypId)
        ";
        $insEquipmentStmt = $pdo->prepare($insertEquipmentQuery);

        foreach ($wyposazenie as $wId) {
            $insEquipmentStmt->execute([
                'klasaId' => $newClassId,
                'wypId'   => (int)$wId
            ]);
        }
    }

    // 3. Wstaw typy łóżek (jeśli są)
    if (!empty($lozka)) {
        $insertBedsQuery = "
            INSERT INTO rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy (klasa_pokoju_id, typ_łóżka_id, liczba_łóżek)
            VALUES (:klasaId, :lozkoId, 1)
        ";
        $insBedsStmt = $pdo->prepare($insertBedsQuery);

        foreach ($lozka as $lozkoId) {
            $insBedsStmt->execute([
                'klasaId' => $newClassId,
                'lozkoId' => (int)$lozkoId
            ]);
        }
    }

    // Zatwierdź transakcję
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Nowa klasa pokoju została dodana wraz z wyposażeniem i typami łóżek.',
        'new_id' => $newClassId
    ]);
} catch (Exception $e) {
    // Cofnij transakcję w razie błędu
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania klasy: ' . $e->getMessage()]);
}
?>
