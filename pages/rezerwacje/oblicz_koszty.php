<?php

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe dane wejściowe.']);
    exit;
}

$roomIds = isset($input['roomIds']) ? $input['roomIds'] : [];
$checkInDate = isset($input['checkInDate']) ? $input['checkInDate'] : null;
$checkOutDate = isset($input['checkOutDate']) ? $input['checkOutDate'] : null;
$addons = isset($input['addons']) ? $input['addons'] : [];

if (empty($roomIds) || !$checkInDate || !$checkOutDate) {
    echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych do obliczenia kosztów.']);
    exit;
}

try {
    // Wywołanie funkcji SQL do obliczenia kosztów
    $stmt = $pdo->prepare("SELECT rezerwacje_hotelowe.oblicz_koszt_rezerwacji_z_parametrami(:roomIds, :checkInDate, :checkOutDate, :addons) AS total_cost");
    $stmt->bindValue(':roomIds', '{' . implode(',', $roomIds) . '}', PDO::PARAM_STR);
    $stmt->bindValue(':checkInDate', $checkInDate);
    $stmt->bindValue(':checkOutDate', $checkOutDate);
    $stmt->bindValue(':addons', '{' . implode(',', $addons) . '}', PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && isset($result['total_cost'])) {
        echo json_encode(['success' => true, 'totalCost' => $result['total_cost']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nie udało się obliczyć kosztów.']);
    }
} catch (PDOException $e) {
    error_log('Błąd SQL: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Błąd serwera.']);
}
?>
