<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Pobierz dane z żądania POST
$requestPayload = file_get_contents("php://input");
$data = json_decode($requestPayload, true);

// Sprawdź, czy w żądaniu podano ID pracownika
if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode(['error' => 'Brak ID pracownika w żądaniu.']);
    exit;
}

$pracownikId = (int) $data['id'];

try {
    // Połączenie z bazą danych
    $pdo = new PDO($dsn, $db_user, $db_password);

    // Sprawdź, czy pracownik istnieje
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.pracownik WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':id', $pracownikId, PDO::PARAM_INT);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono pracownika o podanym ID.']);
        exit;
    }

    // Usuwanie pracownika
    $deleteQuery = "DELETE FROM rezerwacje_hotelowe.pracownik WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->bindParam(':id', $pracownikId, PDO::PARAM_INT);
    $deleteStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Pracownik został pomyślnie usunięty.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas usuwania pracownika: ' . $e->getMessage()]);
}
?>
