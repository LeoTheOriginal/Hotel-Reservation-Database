<?php
require_once __DIR__ . '/../../config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

$imię = $data['imię'] ?? '';
$nazwisko = $data['nazwisko'] ?? '';
$numer_telefonu = $data['numer_telefonu'] ?? '';
$adres_email = $data['adres_email'] ?? '';

if (empty($imię) || empty($nazwisko) || empty($numer_telefonu) || empty($adres_email)) {
    echo json_encode(['success' => false, 'error' => 'Nie wszystkie pola zostały wypełnione']);
    exit;
}

try {
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
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}