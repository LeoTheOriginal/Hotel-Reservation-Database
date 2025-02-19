<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['nazwaKlasy'], $data['cenaPodstawowa'])) {
    echo json_encode(['error' => 'Brak wymaganych danych (id, nazwaKlasy, cenaPodstawowa).']);
    exit;
}

$id = (int) $data['id'];
$nazwa = trim($data['nazwaKlasy']);
$cena = floatval($data['cenaPodstawowa']);

$wyposazenie = (isset($data['wyposazenie']) && is_array($data['wyposazenie']))
    ? $data['wyposazenie'] : [];

$lozka = (isset($data['lozka']) && is_array($data['lozka']))
    ? $data['lozka'] : [];

try {
    // Rozpocznij transakcję
    $pdo->beginTransaction();

    // 1. Sprawdź istnienie klasy
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.klasa_pokoju WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono klasy pokoju o podanym ID.']);
        $pdo->rollBack();
        exit;
    }

    // 2. Aktualizacja nazwy i ceny
    $updateQuery = "
        UPDATE rezerwacje_hotelowe.klasa_pokoju
        SET nazwa_klasy = :nazwa_klasy,
            cena_podstawowa = :cena
        WHERE id = :id
    ";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([
        'nazwa_klasy' => $nazwa,
        'cena' => $cena,
        'id' => $id
    ]);

    // 3. Aktualizacja wyposażenia
    // Najpierw usuwamy wszystkie powiązania, a potem wstawiamy nowe
    $deleteEquipmentQuery = "
        DELETE FROM rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy
        WHERE klasa_pokoju_id = :klasaId
    ";
    $delEquipmentStmt = $pdo->prepare($deleteEquipmentQuery);
    $delEquipmentStmt->execute(['klasaId' => $id]);

    if (!empty($wyposazenie)) {
        $insertEquipmentQuery = "
            INSERT INTO rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy (klasa_pokoju_id, wyposażenie_id)
            VALUES (:klasaId, :wypId)
        ";
        $insEquipmentStmt = $pdo->prepare($insertEquipmentQuery);

        foreach ($wyposazenie as $wId) {
            $insEquipmentStmt->execute([
                'klasaId' => $id,
                'wypId'   => (int)$wId
            ]);
        }
    }

    // 4. Aktualizacja typów łóżek
    // Najpierw usuwamy stare powiązania
    $deleteBedsQuery = "
        DELETE FROM rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy
        WHERE klasa_pokoju_id = :klasaId
    ";
    $delBedsStmt = $pdo->prepare($deleteBedsQuery);
    $delBedsStmt->execute(['klasaId' => $id]);

    // Następnie wstawiamy nowe typy łóżek
    if (!empty($lozka)) {
        $insertBedsQuery = "
            INSERT INTO rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy (klasa_pokoju_id, typ_łóżka_id, liczba_łóżek)
            VALUES (:klasaId, :lozkoId, 1)
        ";
        $insBedsStmt = $pdo->prepare($insertBedsQuery);

        foreach ($lozka as $lozkoId) {
            $insBedsStmt->execute([
                'klasaId' => $id,
                'lozkoId' => (int)$lozkoId
            ]);
        }
    }

    // Zatwierdź transakcję
    $pdo->commit();

    echo json_encode(['success' => 'Zaktualizowano klasę pokoju, wyposażenie oraz typy łóżek.']);
} catch (Exception $e) {
    // Cofnij transakcję w razie błędu
    $pdo->rollBack();
    echo json_encode(['error' => 'Błąd aktualizacji: ' . $e->getMessage()]);
}
?>
