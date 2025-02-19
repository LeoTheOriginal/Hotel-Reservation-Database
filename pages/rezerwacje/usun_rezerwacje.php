<?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$rezerwacja_id = isset($input['id']) ? (int)$input['id'] : null;

if (!$rezerwacja_id) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe ID rezerwacji.']);
    exit;
}

try {
    // Wywołanie funkcji
    $stmt = $pdo->prepare('SELECT rezerwacje_hotelowe.usun_rezerwacje(:rezerwacja_id)');
    $stmt->execute(['rezerwacja_id' => $rezerwacja_id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
