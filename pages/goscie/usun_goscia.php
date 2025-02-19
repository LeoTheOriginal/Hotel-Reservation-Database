<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Pobierz dane przesłane metodą POST
$data = json_decode(file_get_contents('php://input'), true);

// Wyciągnij adres e-mail z przesłanych danych
$email = $data['email'] ?? null;

// Sprawdź, czy email został przesłany
if (!$email) {
    echo json_encode(['success' => false, 'error' => 'Nie podano adresu e-mail do usunięcia.', 'debug_input' => $data]);
    exit;
}

try {
    // Sprawdzenie, czy gość ma powiązane rezerwacje
    $stmt = $pdo->prepare('
        SELECT COUNT(*) AS liczba_rezerwacji 
        FROM rezerwacje_hotelowe."rezerwacja" r
        JOIN rezerwacje_hotelowe."gość" g ON r."gość_id" = g."id"
        WHERE g."adres_email" = :email
    ');
    $stmt->execute([':email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['liczba_rezerwacji'] > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Nie można usunąć tego gościa, ponieważ ma powiązane rezerwacje.',
            // 'debug_input' => $data
        ]);
        exit;
    }

    // Przygotowanie zapytania SQL do usunięcia gościa
    $stmt = $pdo->prepare('DELETE FROM rezerwacje_hotelowe."gość" WHERE "adres_email" = :email');
    $stmt->execute([':email' => $email]);

    // Sprawdzenie, czy wiersz został usunięty
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nie znaleziono gościa z podanym adresem e-mail.', 'debug_input' => $data]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
