<?php
require_once __DIR__ . '/../../config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// Bezpieczne pobranie i parsowanie danych JSON
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowy format JSON: ' . json_last_error_msg()]);
    exit;
}

// Sanityzacja danych wejściowych
$imię = isset($data['imię']) ? sanitizeInput($data['imię']) : '';
$nazwisko = isset($data['nazwisko']) ? sanitizeInput($data['nazwisko']) : '';
$numer_telefonu = isset($data['numer_telefonu']) ? sanitizeInput($data['numer_telefonu']) : '';
$adres_email = isset($data['adres_email']) ? sanitizeInput($data['adres_email']) : '';

// Walidacja danych wejściowych
$errors = [];

if (empty($imię)) {
    $errors[] = 'Imię jest wymagane';
}

if (empty($nazwisko)) {
    $errors[] = 'Nazwisko jest wymagane';
}

if (empty($numer_telefonu)) {
    $errors[] = 'Numer telefonu jest wymagany';
} elseif (!validatePhone($numer_telefonu)) {
    $errors[] = 'Nieprawidłowy format numeru telefonu';
}

if (empty($adres_email)) {
    $errors[] = 'Adres email jest wymagany';
} elseif (!validateEmail($adres_email)) {
    $errors[] = 'Nieprawidłowy format adresu email';
}

// Sprawdzenie czy wystąpiły błędy walidacji
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    // Użycie przygotowanego zapytania do bezpiecznego wprowadzenia danych
    $stmt = $pdo->prepare('
        INSERT INTO rezerwacje_hotelowe."gość" ("imię", "nazwisko", "numer_telefonu", "adres_email")
        VALUES (:imie, :nazwisko, :tel, :email)
        ON CONFLICT ("adres_email") DO NOTHING
    ');
    $stmt->execute([
        ':imie' => $imię,
        ':nazwisko' => $nazwisko,
        ':tel' => $numer_telefonu,
        ':email' => $adres_email
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Email już istnieje w bazie danych.']);
    }
} catch (PDOException $e) {
    // Logowanie błędu bez ujawniania szczegółów technicznych
    error_log("Błąd bazy danych: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Wystąpił błąd podczas dodawania gościa.']);
}