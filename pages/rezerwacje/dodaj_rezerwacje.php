<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe dane wejściowe.']);
    exit;
}

// Mapowanie nazw pól
$gosc_id = isset($input['gosc_id']) ? intval($input['gosc_id']) : null;
$pokoje = isset($input['pokoje']) ? array_map('intval', $input['pokoje']) : [];
$start_date = isset($input['start_date']) ? $input['start_date'] : (isset($input['check_in']) ? $input['check_in'] : null);
$end_date = isset($input['end_date']) ? $input['end_date'] : (isset($input['check_out']) ? $input['check_out'] : null);
$liczba_doroslych = isset($input['liczba_doroslych']) ? intval($input['liczba_doroslych']) : 0;
$liczba_dzieci = isset($input['liczba_dzieci']) ? intval($input['liczba_dzieci']) : 0;
$dodatki = isset($input['dodatki']) ? array_map('intval', $input['dodatki']) : null;

// Logowanie otrzymanych danych
error_log('Received data: ' . json_encode($input));

// Walidacja danych
if (!$gosc_id || empty($pokoje) || !$start_date || !$end_date || $liczba_doroslych < 0 || $liczba_dzieci < 0) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe dane wejściowe.']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT rezerwacje_hotelowe.dodaj_rezerwacje(:gosc_id, :pokoje, :start_date, :end_date, :liczba_doroslych, :liczba_dzieci, :dodatki)');
    $stmt->bindValue(':gosc_id', $gosc_id, PDO::PARAM_INT);
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

    echo json_encode(['success' => true, 'message' => 'Rezerwacja została dodana pomyślnie.']);
} catch (PDOException $e) {
    error_log('Błąd SQL: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
