<?php

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$numer_pokoju = $data['room_number'] ?? '';
$klasa_pokoju_id = $data['room_class'] ?? null;
$status_pokoju_id = $data['room_status'] ?? null;
$pietro_id = $data['floor'] ?? null;

if (!$id || empty($numer_pokoju) || !$klasa_pokoju_id || !$status_pokoju_id || !$pietro_id) {
    echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych.']);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE rezerwacje_hotelowe.pokój SET numer_pokoju = :numer_pokoju, klasa_pokoju_id = :klasa_pokoju_id, status_pokoju_id = :status_pokoju_id, piętro_id = :pietro_id WHERE id = :id');
    $stmt->execute([
        ':id' => $id,
        ':numer_pokoju' => $numer_pokoju,
        ':klasa_pokoju_id' => $klasa_pokoju_id,
        ':status_pokoju_id' => $status_pokoju_id,
        ':pietro_id' => $pietro_id 
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nie udało się zaktualizować pokoju.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
