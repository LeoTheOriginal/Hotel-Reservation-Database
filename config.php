<?php
// /config.php

$host = "inscrutably-entrancing-vulture.data-1.euc1.tembo.io"; // tutaj nalezy podac adres bazy danych
$dbname = "postgres"; // nazwa bazy danych
$user = "postgres"; // nazwa użytkownika
$password = "CJgm14hS8oMuraUe"; // hasło użytkownika
$base_url = '/~2piotrowski/hotel'; // ścieżka do katalogu głównego aplikacji
$schema = "rezerwacje_hotelowe"; // schemat bazy danych

// Definicje stałych dla ról
define('ROLE_ADMINISTRATOR', 'Administrator');
define('ROLE_MANAGER', 'Manager');
define('ROLE_RECEPCJONISTA', 'Recepcjonista');

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
    // Obsługa błędów połączenia
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
    exit;
}

$test = $pdo->query("SELECT 1");
if (!$test) {
    echo json_encode(['success' => false, 'error' => 'Brak połączenia z bazą danych.']);
    exit;
}
?>
