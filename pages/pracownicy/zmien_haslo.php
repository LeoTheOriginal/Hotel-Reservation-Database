<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Pobierz dane z żądania POST
$requestPayload = file_get_contents("php://input");
$data = json_decode($requestPayload, true);

if (!isset($data['id'], $data['current_password'], $data['new_password'])) {
    echo json_encode(['error' => 'Nie podano wymaganych danych.']);
    exit;
}

$id = (int) $data['id'];
$currentPassword = $data['current_password'];
$newPassword = $data['new_password'];

try {
    $pdo = new PDO($dsn, $db_user, $db_password);

    // Sprawdź, czy podano poprawne aktualne hasło
    $query = "SELECT hasło FROM rezerwacje_hotelowe.pracownik WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || md5($currentPassword) !== $result['hasło']) {
        echo json_encode(['error' => 'Nieprawidłowe aktualne hasło.']);
        exit;
    }

    // Zaktualizuj hasło
    $newPasswordHashed = md5($newPassword);
    $updateQuery = "UPDATE rezerwacje_hotelowe.pracownik SET hasło = :new_password WHERE id = :id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':new_password', $newPasswordHashed, PDO::PARAM_STR);
    $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $updateStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Hasło zostało pomyślnie zmienione.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas zmiany hasła: ' . $e->getMessage()]);
}
?>
