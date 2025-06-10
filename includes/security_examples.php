<?php
/**
 * Security_examples.php
 * 
 * Ten plik zawiera przykłady implementacji bezpiecznego przetwarzania danych wejściowych
 * i zabezpieczenia przed popularnymi atakami w systemie zarządzania hotelem.
 */

require_once __DIR__ . '/../config.php';

/**
 * Przykład 1: Bezpieczne przetwarzanie danych z formularza
 */
function processFormSafely($formData) {
    // Sanityzacja wszystkich pól
    $sanitizedData = [];
    foreach ($formData as $key => $value) {
        $sanitizedData[$key] = sanitizeInput($value);
    }
    
    // Walidacja wymaganych pól
    $requiredFields = ['imię', 'nazwisko', 'email'];
    $errors = [];
    
    foreach ($requiredFields as $field) {
        if (empty($sanitizedData[$field])) {
            $errors[] = "Pole {$field} jest wymagane.";
        }
    }
    
    // Walidacja formatu adresu email
    if (!empty($sanitizedData['email']) && !validateEmail($sanitizedData['email'])) {
        $errors[] = "Podany adres email jest nieprawidłowy.";
    }
    
    // Walidacja numeru telefonu (jeśli został podany)
    if (!empty($sanitizedData['telefon']) && !validatePhone($sanitizedData['telefon'])) {
        $errors[] = "Podany numer telefonu jest nieprawidłowy.";
    }
    
    // Walidacja daty (jeśli została podana)
    if (!empty($sanitizedData['data']) && !validateDate($sanitizedData['data'])) {
        $errors[] = "Podana data jest nieprawidłowa. Użyj formatu YYYY-MM-DD.";
    }
    
    // Zwracanie wyników walidacji
    return [
        'data' => $sanitizedData,
        'errors' => $errors,
        'isValid' => empty($errors)
    ];
}

/**
 * Przykład 2: Bezpieczne wykonanie zapytania do bazy danych
 */
function getUserSafely($pdo, $userId) {
    // Użycie przygotowanego zapytania z parametrem
    $stmt = prepareQuery($pdo, 'SELECT * FROM rezerwacje_hotelowe."gość" WHERE "id" = :id', [':id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Przykład 3: Bezpieczna walidacja i tworzenie nowego użytkownika
 */
function createUserSafely($pdo, $userData) {
    // Walidacja danych
    $validationResult = processFormSafely($userData);
    
    if (!$validationResult['isValid']) {
        return [
            'success' => false,
            'errors' => $validationResult['errors']
        ];
    }
    
    // Bezpieczne dane po walidacji
    $data = $validationResult['data'];
    
    // Przygotowanie i wykonanie zapytania
    try {
        $stmt = $pdo->prepare('
            INSERT INTO rezerwacje_hotelowe."gość" ("imię", "nazwisko", "numer_telefonu", "adres_email")
            VALUES (:imie, :nazwisko, :tel, :email)
        ');
        
        $stmt->execute([
            ':imie' => $data['imię'],
            ':nazwisko' => $data['nazwisko'],
            ':tel' => $data['telefon'] ?? null,
            ':email' => $data['email']
        ]);
        
        return [
            'success' => true,
            'userId' => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'error' => 'Błąd podczas dodawania użytkownika: ' . $e->getMessage()
        ];
    }
}

/**
 * Przykład 4: Bezpieczne przetwarzanie danych JSON
 */
function processJsonDataSafely() {
    // Pobranie danych JSON
    $jsonData = file_get_contents('php://input');
    
    // Walidacja formatu JSON
    $data = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'Nieprawidłowy format JSON: ' . json_last_error_msg()
        ];
    }
    
    // Sanityzacja i walidacja danych
    $result = processFormSafely($data);
    
    if (!$result['isValid']) {
        return [
            'success' => false,
            'errors' => $result['errors']
        ];
    }
    
    // Dalsze przetwarzanie danych...
    return [
        'success' => true,
        'data' => $result['data']
    ];
}

/**
 * Przykład 5: Bezpieczne przetwarzanie plików
 */
function uploadFileSafely($fileData) {
    // Dozwolone typy plików
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    
    // Maksymalny rozmiar pliku (2MB)
    $maxFileSize = 2 * 1024 * 1024;
    
    // Katalog docelowy
    $uploadDir = __DIR__ . '/../assets/uploads/';
    
    // Walidacja typu pliku
    if (!in_array($fileData['type'], $allowedTypes)) {
        return [
            'success' => false,
            'error' => 'Niedozwolony typ pliku. Dozwolone typy: ' . implode(', ', $allowedTypes)
        ];
    }
    
    // Walidacja rozmiaru pliku
    if ($fileData['size'] > $maxFileSize) {
        return [
            'success' => false,
            'error' => 'Plik jest zbyt duży. Maksymalny rozmiar to 2MB.'
        ];
    }
    
    // Generowanie bezpiecznej nazwy pliku
    $fileExtension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('file_', true) . '.' . $fileExtension;
    $targetFile = $uploadDir . $newFileName;
    
    // Przeniesienie pliku
    if (move_uploaded_file($fileData['tmp_name'], $targetFile)) {
        return [
            'success' => true,
            'filePath' => $targetFile,
            'fileName' => $newFileName
        ];
    } else {
        return [
            'success' => false,
            'error' => 'Wystąpił błąd podczas przesyłania pliku.'
        ];
    }
}
?>