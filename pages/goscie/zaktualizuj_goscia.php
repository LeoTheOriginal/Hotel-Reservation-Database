<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$name = $data['name'] ?? null;
$surname = $data['surname'] ?? null;
$phone = $data['phone'] ?? null;
$email = $data['email'] ?? null;

if (!$id || !$name || !$surname || !$phone || !$email) {
    echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych.']);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE rezerwacje_hotelowe."gość" SET "imię" = :name, "nazwisko" = :surname, "numer_telefonu" = :phone, "adres_email" = :email WHERE "id" = :id');
    $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':surname' => $surname,
        ':phone' => $phone,
        ':email' => $email
    ]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
