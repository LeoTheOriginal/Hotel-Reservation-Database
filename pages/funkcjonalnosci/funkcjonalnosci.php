<?php
include_once __DIR__ . '/../../includes/header.php';

// Sprawdzenie, czy użytkownik jest Administrator
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== ROLE_ADMINISTRATOR) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Funkcjonalności Projektu - Stardust Hotel</title>
    <link rel="stylesheet" href="/~2piotrowski/hotel/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalności Projektu</h1>
        <p>W tej sekcji znajdziesz szczegółowy opis poszczególnych funkcjonalności naszego systemu rezerwacji hotelowych.</p>
        
        <h2>Struktura Katalogów</h2>
        <p>Poniżej przedstawiono strukturę katalogów projektu:</p>
        <pre>
/projekt
│
├── /pages
│   ├── /funkcjonalnosci
|   ├── /pracownicy
│   ├── /goscie
│   ├── /klasy_pokoi
│   ├── /typy_lozek
│   ├── /pokoje
│   ├── /raporty
│   ├── /rezerwacje
│   ├── /wyposazenie
│   ├── /dodatki
│   └── /logi_systemowe
│
├── /includes
│   ├── header.php
│   └── footer.php
│
├── /assets
│   ├── /css
│   ├── /images
│   └── /js
│
├── config.php
├── index.php
└── test_connection.php
        </pre>
        
        <h2>Omówienie Funkcjonalności</h2>
        <p>Każda z poniższych sekcji opisuje poszczególne funkcjonalności systemu:</p>
        <ul>
            <li><a href="funkcjonalnosci_goscie.php">Goście</a></li>
            <li><a href="funkcjonalnosci_klasy_pokoi.php">Klasy pokoi</a></li>
            <li><a href="funkcjonalnosci_typy_lozek.php">Typy łóżek</a></li>
            <li><a href="funkcjonalnosci_pokoje.php">Pokoje</a></li>
            <li><a href="funkcjonalnosci_raporty.php">Raporty</a></li>
            <li><a href="funkcjonalnosci_rezerwacje.php">Rezerwacje</a></li>
            <li><a href="funkcjonalnosci_pracownicy.php">Pracownicy</a></li>
            <li><a href="funkcjonalnosci_wyposazenie.php">Wyposażenie</a></li>
            <li><a href="funkcjonalnosci_dodatki.php">Dodatki</a></li>
            <li><a href="funkcjonalnosci_logi_systemowe.php">Logi systemowe</a></li>
        </ul>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
