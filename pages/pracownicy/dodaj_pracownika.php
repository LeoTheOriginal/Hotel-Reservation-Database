<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Połączenie z bazą danych
    $pdo = new PDO($dsn, $db_user, $db_password);

    // Pobieranie danych z żądania POST
    $imie = $_POST['first_name'] ?? '';
    $nazwisko = $_POST['last_name'] ?? '';
    $login = $_POST['login'] ?? '';
    $haslo = $_POST['password'] ?? '';
    $rola_id = $_POST['role'] ?? '';

    // Walidacja danych
    if (empty($imie) || empty($nazwisko) || empty($login) || empty($haslo) || empty($rola_id)) {
        echo json_encode(['success' => false, 'error' => 'Wszystkie pola są wymagane.']);
        exit;
    }

    // Sprawdzenie, czy login jest unikalny
    $checkQuery = "SELECT COUNT(*) FROM rezerwacje_hotelowe.pracownik WHERE login = :login";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['login' => $login]);
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'error' => 'Login już istnieje. Wybierz inny.']);
        exit;
    }

    // Hashowanie hasła
    $hashedPassword = md5($haslo);

    // Dodawanie pracownika do bazy
    $query = "INSERT INTO rezerwacje_hotelowe.pracownik (imię, nazwisko, login, hasło, rola_id)
              VALUES (:imie, :nazwisko, :login, :haslo, :rola_id)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'imie' => $imie,
        'nazwisko' => $nazwisko,
        'login' => $login,
        'haslo' => $hashedPassword,
        'rola_id' => $rola_id,
    ]);

    echo json_encode(['success' => true, 'message' => 'Pracownik został pomyślnie dodany.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania pracownika: ' . $e->getMessage()]);
}
?>
