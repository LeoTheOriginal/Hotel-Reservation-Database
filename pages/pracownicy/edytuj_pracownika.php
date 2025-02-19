<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Pobieranie danych z żądania POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['imie']) || !isset($data['nazwisko']) || !isset($data['login']) || !isset($data['rola_id'])) {
    echo json_encode(['error' => 'Brak wymaganych danych wejściowych.']);
    exit;
}

try {
    // Połączenie z bazą danych
    $pdo = new PDO($dsn, $db_user, $db_password);

    // Aktualizacja danych pracownika
    $query = "UPDATE rezerwacje_hotelowe.pracownik
              SET imię = :imie, nazwisko = :nazwisko, login = :login, rola_id = :rola_id
              WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':imie', $data['imie'], PDO::PARAM_STR);
    $stmt->bindParam(':nazwisko', $data['nazwisko'], PDO::PARAM_STR);
    $stmt->bindParam(':login', $data['login'], PDO::PARAM_STR);
    $stmt->bindParam(':rola_id', $data['rola_id'], PDO::PARAM_INT);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

    $stmt->execute();

    echo json_encode(['success' => 'Dane pracownika zostały zaktualizowane.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas aktualizacji danych pracownika: ' . $e->getMessage()]);
}
?>
