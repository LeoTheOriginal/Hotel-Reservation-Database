<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe dane wejściowe.']);
    exit;
}

$imie = isset($input['imie']) ? trim($input['imie']) : null;
$nazwisko = isset($input['nazwisko']) ? trim($input['nazwisko']) : null;
$telefon = isset($input['telefon']) ? trim($input['telefon']) : null;
$email = isset($input['email']) ? trim($input['email']) : null;
$pokoje = isset($input['pokoje']) ? array_map('intval', $input['pokoje']) : [];
$start_date = isset($input['start_date']) ? $input['start_date'] : null;
$end_date = isset($input['end_date']) ? $input['end_date'] : null;
$liczba_doroslych = isset($input['liczba_doroslych']) ? intval($input['liczba_doroslych']) : 0;
$liczba_dzieci = isset($input['liczba_dzieci']) ? intval($input['liczba_dzieci']) : 0;
$dodatki = isset($input['dodatki']) ? array_map('intval', $input['dodatki']) : null;

// Walidacja danych
if (!$imie || !$nazwisko || !$telefon || !$email || empty($pokoje) || !$start_date || !$end_date || $liczba_doroslych < 0 || $liczba_dzieci < 0) {
    echo json_encode(['success' => false, 'error' => 'Proszę uzupełnić wszystkie wymagane pola.']);
    exit;
}

try {
    // Rozpoczęcie transakcji
    $pdo->beginTransaction();

    // Dodanie nowego gościa
    $stmt = $pdo->prepare('INSERT INTO rezerwacje_hotelowe.gość (imię, nazwisko, numer_telefonu, adres_email) VALUES (:imie, :nazwisko, :telefon, :email) RETURNING id');
    $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
    $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
    $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $new_gosc_id = $stmt->fetchColumn();

    // Dodanie rezerwacji
    $stmt = $pdo->prepare('SELECT rezerwacje_hotelowe.dodaj_rezerwacje(:gosc_id, :pokoje, :start_date, :end_date, :liczba_doroslych, :liczba_dzieci, :dodatki)');
    $stmt->bindValue(':gosc_id', $new_gosc_id, PDO::PARAM_INT);
    $stmt->bindValue(':pokoje', '{' . implode(',', $pokoje) . '}', PDO::PARAM_STR); // PostgreSQL array format
    $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->bindValue(':liczba_doroslych', $liczba_doroslych, PDO::PARAM_INT);
    $stmt->bindValue(':liczba_dzieci', $liczba_dzieci, PDO::PARAM_INT);
    if ($dodatki) {
        $stmt->bindValue(':dodatki', '{' . implode(',', $dodatki) . '}', PDO::PARAM_STR);
    } else {
        $stmt->bindValue(':dodatki', null, PDO::PARAM_NULL);
    }
    $stmt->execute();

    // Zatwierdzenie transakcji
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Wycofanie transakcji w razie błędu
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
