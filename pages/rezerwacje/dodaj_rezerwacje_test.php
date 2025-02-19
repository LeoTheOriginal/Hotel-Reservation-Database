<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = [
    'guest' => 1, 
    'rooms' => [2, 3],
    'check_in' => '2024-12-16',
    'check_out' => '2024-12-17',
    'adults' => 2,
    'children' => 1,
    'addons' => [1, 2] 
];

// Logowanie danych testowych
error_log('Test data: ' . json_encode($data));

try {
    $stmt = $pdo->prepare('SELECT dodaj_rezerwacje(:guest, :rooms, :check_in, :check_out, :adults, :children, :addons)');
    $stmt->execute([
        ':guest' => $data['guest'],
        ':rooms' => '{' . implode(',', $data['rooms']) . '}',
        ':check_in' => $data['check_in'],
        ':check_out' => $data['check_out'],
        ':adults' => $data['adults'],
        ':children' => $data['children'],
        ':addons' => '{' . implode(',', $data['addons']) . '}'
    ]);

    // Wyświetlenie komunikatu o sukcesie
    echo json_encode(['success' => true, 'message' => 'Rezerwacja została dodana pomyślnie.']);
} catch (PDOException $e) {
    // Obsługa błędów SQL
    error_log('Błąd SQL: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (Exception $e) {
    // Obsługa innych błędów
    error_log('Błąd: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
