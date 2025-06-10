<?php
// /config.php

$host = ""; // tutaj nalezy podac adres bazy danych
$dbname = ""; // nazwa bazy danych
$user = ""; // nazwa użytkownika
$password = ""; // hasło użytkownika
$base_url = ''; // ścieżka do katalogu głównego aplikacji
$schema = ""; // schemat bazy danych

// Definicje stałych dla ról
define('ROLE_ADMINISTRATOR', 'Administrator');
define('ROLE_MANAGER', 'Manager');
define('ROLE_RECEPCJONISTA', 'Recepcjonista');

// Definicja zmiennej środowiskowej - domyślnie produkcja
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'production'); // Można zmienić na 'development' w środowisku deweloperskim
}

// Funkcja do logowania błędów - zapisuje szczegóły błędu do pliku log
function logDatabaseError($error) {
    $logFile = __DIR__ . '/logs/db_errors.log';
    $logDir = dirname($logFile);
    
    // Sprawdzenie czy katalog logów istnieje, jeśli nie - utwórz go
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    // Format wpisu logu: [data i czas] Komunikat błędu
    $logEntry = '[' . date('Y-m-d H:i:s') . '] ' . $error . PHP_EOL;
    
    // Zapisz log do pliku
    error_log($logEntry, 3, $logFile);
}

// Ustawienia połączenia
$dsn = "pgsql:host=$host;port=5432;dbname=$dbname;user=$user;password=$password";

try {
    // Utworzenie nowego połączenia PDO
    $pdo = new PDO($dsn);
    $pdo->exec("SET NAMES 'UTF8'");
    $pdo->exec("SET search_path TO $schema"); // przy kazdym polaczeniu sie resetuje i nie zawsze dziala -> lepiej nie uzywać

    // Włączenie obsługi błędów
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Logowanie szczegółów błędu (tylko do pliku, niewidoczne dla użytkownika)
    logDatabaseError('Błąd połączenia z bazą danych: ' . $e->getMessage());
    
    // Wyświetlenie ogólnego komunikatu błędu bez ujawniania szczegółów technicznych
    if (ENVIRONMENT === 'development') {
        // W środowisku deweloperskim można pokazać więcej szczegółów
        echo "<h3>Błąd połączenia z bazą danych</h3>";
        echo "<p>Szczegóły błędu: " . $e->getMessage() . "</p>";
    } else {
        // W produkcji pokazujemy tylko ogólny komunikat
        echo "<h3>Błąd połączenia z systemem</h3>";
        echo "<p>Przepraszamy, wystąpił problem z połączeniem do systemu. Prosimy spróbować później lub skontaktować się z administratorem.</p>";
    }
    exit;
}

// Sprawdzenie czy połączenie działa
try {
    $test = $pdo->query("SELECT 1");
    if (!$test) {
        // Logowanie błędu
        logDatabaseError('Nie można wykonać zapytania testowego');
        
        // Zwrócenie generycznego komunikatu błędu
        if (ENVIRONMENT === 'development') {
            echo json_encode(['success' => false, 'error' => 'Brak połączenia z bazą danych. Zapytanie testowe nie powiodło się.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Wystąpił problem z systemem. Prosimy spróbować później.']);
        }
        exit;
    }
} catch (PDOException $e) {
    // Logowanie szczegółów błędu
    logDatabaseError('Błąd podczas testu połączenia: ' . $e->getMessage());
    
    // Zwrócenie generycznego komunikatu błędu
    if (ENVIRONMENT === 'development') {
        echo json_encode(['success' => false, 'error' => 'Błąd zapytania: ' . $e->getMessage()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Wystąpił problem z systemem. Prosimy spróbować później.']);
    }
    exit;
}
?>