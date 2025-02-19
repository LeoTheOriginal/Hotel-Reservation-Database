<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO($dsn, $db_user, $db_password);

    $query = "SELECT p.id, p.imię AS imie, p.nazwisko, p.login, r.nazwa_roli AS rola
              FROM rezerwacje_hotelowe.pracownik p
              JOIN rezerwacje_hotelowe.rola r ON p.rola_id = r.id
              ORDER BY p.nazwisko, p.imię";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $pracownicy = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($pracownicy);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania danych pracowników: ' . $e->getMessage()]);
}
?>
