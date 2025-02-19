<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Pobranie danych JSON z żądania
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe dane wejściowe.']);
    exit;
}

// Pobranie danych rezerwacji
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
$amount = isset($input['amount']) ? floatval($input['amount']) : 0.0;

// Walidacja danych
if (!$imie || !$nazwisko || !$telefon || !$email || empty($pokoje) || !$start_date || !$end_date || $liczba_doroslych <= 0) {
    echo json_encode(['success' => false, 'error' => 'Proszę uzupełnić wszystkie wymagane pola.']);
    exit;
}

try {
    // Rozpoczęcie transakcji
    $pdo->beginTransaction();

    // Wywołanie funkcji PostgreSQL
    $stmt = $pdo->prepare('SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_goscia_public(:imie, :nazwisko, :telefon, :adres_email, :pokoje, :start_date, :end_date, :liczba_doroslych, :liczba_dzieci, :dodatki)');
    $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
    $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
    $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
    $stmt->bindValue(':adres_email', $email, PDO::PARAM_STR);
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

    echo json_encode(['success' => true, 'message' => 'Rezerwacja została pomyślnie dodana.']);
} catch (PDOException $e) {
    // Wycofanie transakcji w razie błędu
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania rezerwacji: ' . $e->getMessage()]);
}
?>
