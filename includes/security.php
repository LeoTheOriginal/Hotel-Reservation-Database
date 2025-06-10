<?php
/**
 * Security.php
 * 
 * Ten plik zawiera dokumentację i implementację metod związanych z bezpieczeństwem,
 * walidacją danych wejściowych i sanityzacją w systemie zarządzania hotelem.
 */

/**
 * Funkcje walidacji danych wejściowych
 */

/**
 * Funkcja do sanityzacji danych wejściowych
 * 
 * @param string $data Dane wejściowe do sanityzacji
 * @return string Sanityzowane dane
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Walidacja adresu email
 * 
 * @param string $email Adres email do walidacji
 * @return bool True jeśli adres email jest prawidłowy, false w przeciwnym razie
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Walidacja numeru telefonu
 * 
 * @param string $phone Numer telefonu do walidacji
 * @return bool True jeśli numer telefonu jest prawidłowy, false w przeciwnym razie
 */
function validatePhone($phone) {
    // Przykładowa prosta walidacja numeru telefonu - tylko cyfry i minimum 9 znaków
    return preg_match('/^[0-9]{9,}$/', $phone) === 1;
}

/**
 * Walidacja daty
 * 
 * @param string $date Data w formacie YYYY-MM-DD
 * @return bool True jeśli data jest prawidłowa, false w przeciwnym razie
 */
function validateDate($date) {
    $format = 'Y-m-d';
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Funkcje bezpieczeństwa związane z bazą danych
 */

/**
 * Przygotowanie zapytania z parametrami (prepared statements)
 * 
 * Przykład użycia:
 * $stmt = prepareQuery($pdo, 'SELECT * FROM users WHERE id = :id', [':id' => $userId]);
 * $result = $stmt->fetch(PDO::FETCH_ASSOC);
 * 
 * @param PDO $pdo Obiekt PDO
 * @param string $query Zapytanie SQL z placeholderami
 * @param array $params Tablica parametrów do bindowania
 * @return PDOStatement Przygotowane zapytanie
 */
function prepareQuery($pdo, $query, $params = []) {
    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt;
}

/**
 * Dokumentacja bezpieczeństwa i walidacji w systemie
 */

/**
 * Strategia walidacji danych wejściowych
 * 
 * W naszym systemie stosujemy następujące praktyki walidacji danych wejściowych:
 * 
 * 1. Sprawdzanie pustych pól - wszystkie wymagane pola muszą być wypełnione
 * 2. Walidacja typów danych - np. numery muszą zawierać tylko cyfry, email musi mieć prawidłowy format
 * 3. Walidacja długości - sprawdzanie czy dane mieszczą się w dozwolonych zakresach
 * 4. Walidacja formatu - sprawdzanie czy dane mają odpowiedni format (np. data, email, telefon)
 * 5. Walidacja biznesowa - sprawdzanie czy dane są zgodne z regułami biznesowymi (np. data zameldowania < data wymeldowania)
 */

/**
 * Zapobieganie SQL Injection
 * 
 * W celu zapobiegania atakom typu SQL Injection, w naszym systemie:
 * 
 * 1. Używamy zawsze zapytań parametryzowanych (prepared statements)
 * 2. Nigdy nie konkatenujemy zmiennych bezpośrednio w zapytaniach SQL
 * 3. Używamy PDO z odpowiednimi atrybutami bezpieczeństwa
 * 4. Stosujemy zasadę minimalnych uprawnień dla konta bazodanowego
 * 
 * Przykład użycia prepared statements:
 * 
 * $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
 * $stmt->bindParam(':username', $username);
 * $stmt->execute();
 * 
 * NIE NALEŻY używać:
 * $query = "SELECT * FROM users WHERE username = '$username'";
 * $result = $pdo->query($query);
 */

/**
 * Zapobieganie Cross-Site Scripting (XSS)
 * 
 * W celu zapobiegania atakom XSS:
 * 
 * 1. Sanityzujemy wszystkie dane wejściowe przed użyciem
 * 2. Używamy funkcji htmlspecialchars() przy wyświetlaniu danych
 * 3. Ustawiamy odpowiednie nagłówki HTTP (Content-Type, X-XSS-Protection)
 * 4. Korzystamy z funkcji sanitizeInput() zdefiniowanej w tym pliku
 */

/**
 * Bezpieczne zarządzanie sesjami
 * 
 * W naszym systemie stosujemy następujące praktyki bezpiecznego zarządzania sesjami:
 * 
 * 1. Regeneracja ID sesji po zalogowaniu
 * 2. Ustawianie cookies z flagami HttpOnly i Secure (gdy używamy HTTPS)
 * 3. Weryfikacja roli użytkownika przy każdym żądaniu wymagającym uprawnień
 * 4. Automatyczne wylogowanie po okresie bezczynności
 */

/**
 * Walidacja plików
 * 
 * Przy uploadzie plików stosujemy:
 * 
 * 1. Sprawdzanie MIME type pliku
 * 2. Walidację rozszerzenia pliku
 * 3. Ograniczenie rozmiaru pliku
 * 4. Zmianę nazwy pliku na losową, aby zapobiec atakom path traversal
 * 5. Przechowywanie plików poza katalogiem publicznym, jeśli to możliwe
 */
?>