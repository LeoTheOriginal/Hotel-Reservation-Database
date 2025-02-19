<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Sprawdź, czy w żądaniu podano ID pracownika
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Brak ID pracownika w żądaniu.']);
    exit;
}

$pracownikId = (int) $_GET['id'];

try {
    // Połączenie z bazą danych
    $pdo = new PDO($dsn, $db_user, $db_password);

    // Zapytanie do bazy danych o szczegóły pracownika
    $query = "SELECT p.id, p.imię AS imie, p.nazwisko, p.login, r.id AS rola_id, r.nazwa_roli AS rola
              FROM rezerwacje_hotelowe.pracownik p
              JOIN rezerwacje_hotelowe.rola r ON p.rola_id = r.id
              WHERE p.id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $pracownikId, PDO::PARAM_INT);
    $stmt->execute();

    $pracownik = $stmt->fetch(PDO::FETCH_ASSOC);

    // Sprawdź, czy znaleziono pracownika
    if ($pracownik) {
        echo json_encode(['success' => true, 'pracownik' => $pracownik]);
    } else {
        echo json_encode(['error' => 'Nie znaleziono pracownika o podanym ID.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania danych pracownika: ' . $e->getMessage()]);
}
?>
