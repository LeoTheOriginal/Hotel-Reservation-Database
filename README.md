# README – System Rezerwacji Hotelowych

Poniższy plik README opisuje projekt systemu rezerwacji hotelowych, który umożliwia zarządzanie gośćmi, rezerwacjami, pokojami, dodatkami, pracownikami i wieloma innymi elementami. Zawiera też wskazówki dotyczące uruchomienia aplikacji, konfiguracji, wdrożenia oraz propozycję miejsc, w których można dodać screenshoty ukazujące działanie systemu.

---
## Spis treści
1. [Opis projektu](#opis-projektu)
2. [Technologie i wymagania systemowe](#technologie-i-wymagania-systemowe)
3. [Struktura projektu](#struktura-projektu)
4. [Instalacja](#instalacja)
5. [Konfiguracja](#konfiguracja)
6. [Instrukcje uruchomienia](#instrukcje-uruchomienia)
7. [Główne funkcjonalności](#główne-funkcjonalności)
8. [Przykładowe screenshoty](#przykładowe-screenshoty)
9. [Architektura bazodanowa i logika](#architektura-bazodanowa-i-logika)
10. [Utrzymanie i rozwój projektu](#utrzymanie-i-rozwój-projektu)
11. [Autor](#autor)

---
## Opis projektu
**System Rezerwacji Hotelowych** to zaawansowana aplikacja umożliwiająca:
- Zarządzanie rezerwacjami
- Obsługę wyposażenia pokoi
- Zarządzanie pokojami i klasami pokojów
- Zarządzanie typami łóżek
- Dodawanie i edycję gości
- Dodawanie, edycję i usuwanie dodatków
- Zarządzanie pracownikami i rolami
- Generowanie różnego rodzaju raportów (m.in. finansowych, statystycznych)
- Logowanie zmian i operacji w bazie

Celem projektu jest automatyzacja czynności związanych z rezerwacjami, stanem pokoju, płatnościami, obsługą gości i zarządzaniem personelem.

---
## Technologie i wymagania systemowe
1. **Serwer bazodanowy**: PostgreSQL
2. **Języki**: PHP (backend), JavaScript (frontend), SQL (procedury, funkcje)
3. **Strona WWW**: HTML, CSS, JavaScript
4. **Biblioteki frontend**: Chart.js do wizualizacji raportów, jsPDF do eksportu PDF, XLSX do eksportu Excel
5. **Serwer**: Skonfigurowany serwer Apache (lub inny) z obsługą PHP
6. **Przeglądarka**: Nowoczesna przeglądarka wspierająca HTML5, JavaScript, CSS
7. **Inne**: 
   - php.ini z włączoną obsługą PDO do komunikacji z bazą danych
   - Uprawnienia do tworzenia schematów w PostgreSQL
   - **Względy bezpieczeństwa**: Hasła i klucze uwierzytelniające do bazy danych należy przechowywać poza repozytorium (lub w plikach, które nie są commitowane). Poniższy przykład w `config.php` jest jedynie poglądowy.

---
## Struktura projektu
Struktura plików (podział logiczny) wygląda następująco:

```
project-root/
├── pages/
│   ├── funkcjonalnosci/
│   ├── pracownicy/
│   ├── goscie/
│   ├── klasy_pokoi/
│   ├── typy_lozek/
│   ├── pokoje/
│   ├── raporty/
│   ├── rezerwacje/
│   ├── wyposazenie/
│   ├── dodatki/
│   └── logi_systemowe/
│
├── includes/
│   ├── header.php
│   └── footer.php
│
├── assets/
│   ├── css/
│   ├── images/
│   └── js/
│
├── config.php
├── index.php
├── test_connection.php
└── repo-sql/  (folder na repozytorium tylko dla plików SQL)
```

1. **pages/** – zawiera podstrony i moduły aplikacji (funkcjonalnosci, pracownicy, goscie itd.).
2. **includes/** – zawiera pliki nagłówkowe (header) i stopki (footer) oraz ewentualne ustawienia sesji.
3. **assets/** – przechowuje pliki CSS, JS, obrazy, ikony, itp.
4. **config.php** – ustawienia i parametry bazy danych oraz URL projektu (uwaga: nie ujawniaj prawdziwych credentiali w repozytorium publicznym).
5. **index.php** – Strona główna projektu (może być stroną powitalną lub przekierowującą).
6. **test_connection.php** – Skrypt testujący połączenie z bazą.
7. **repo-sql/** – osobny folder na repozytorium SQL (np. `projekt.sql`).

---
## Instalacja
1. **Klonowanie repozytorium** (lub pobranie paczki ZIP z plikami):

```bash
git clone https://github.com/LeoTheOriginal/Hotel-Reservation-Database.git
```

2. **Utworzenie bazy danych** w PostgreSQL (np. `hotel_db`).
3. **Uruchomienie skryptu** SQL z katalogu `repo-sql/`:

```bash
psql -U postgres -d hotel_db -f projekt.sql
```

4. **Skonfigurowanie pliku** `config.php` z danymi dostępowymi do bazy:

```php
<?php
// /config.php

$host = "host_adres_bazy"; 
$dbname = "nazwa_bazy"; 
$user = "nazwa_uzytkownika"; 
$password = "haslo"; 
$base_url = '/~2piotrowski/hotel'; 
$schema = "rezerwacje_hotelowe"; 

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
    // $pdo->exec("SET search_path TO $schema"); // ewentualnie

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
```

> **Uwaga**: Upewnij się, że nie umieszczasz w repozytorium prawdziwych haseł i kluczy!

---
## Konfiguracja
1. W pliku `config.php` ustaw:
   - **$host**, **$dbname**, **$user**, **$password** – adekwatnie do Twojej bazy PostgreSQL.
   - **$base_url** – URL projektu (np. http://localhost/hotel lub /~2piotrowski/hotel).
   - **$schema** – Nazwa schematu w bazie (domyślnie `rezerwacje_hotelowe`).
2. Sprawdź, czy folder `assets/images/uploads` ma odpowiednie uprawnienia (zapisywalny dla PHP) – jeśli korzystasz z uploadu zdjęć.
3. Upewnij się, że `php.ini` ma włączone rozszerzenie pdo_pgsql.
4. Po wypełnieniu tych informacji możesz uruchomić projekt.

---
## Instrukcje uruchomienia
1. Skonfiguruj wirtualny host lub używaj `http://localhost/hotel` (zależnie od Twojego środowiska).
2. Zarejestruj lub zaloguj się na konto (jeśli już istnieje konto Administratora).
3. Po zalogowaniu dostępne będą poszczególne moduły:
   - **Rezerwacje**
   - **Pokoje**
   - **Goście**
   - **Pracownicy**
   - **Raporty** (finansowe, statystyczne)
   - **Typy łóżek**
   - **Wyposażenie**
   - **Dodatki**
   - **Funkcjonalności** (opisowe)
   - **Logi systemowe**
4. Dostępność poszczególnych funkcji zależy od roli zalogowanego użytkownika (Administrator, Recepcjonista, Manager).

---
## Główne funkcjonalności
1. **Zarządzanie rezerwacjami**
   - Dodawanie rezerwacji (dla nowego lub istniejącego gościa)
   - Edycja rezerwacji (np. zmiana dat, dodawanie/ usuwanie dodatków)
   - Usuwanie i anulowanie rezerwacji
   - Wykwaterowanie (zmiana statusu pokoju na „Dostępny” oraz statusu płatności na „Zrealizowana”)
2. **Goście**
   - Dodawanie nowego gościa, weryfikacja unikalności email
   - Edycja danych gościa
   - Usuwanie gościa (tylko przez Administratora/Managera)
3. **Pokoje**
   - Definiowanie klasy pokoju, piętra, statusu
   - Automatyczne obliczanie liczby osób w zależności od łóżek i klasy pokoju
4. **Dodatki**
   - Definiowanie dodatków, np. Śniadanie, Parking, SPA
   - Możliwość przypisywania dodatków do rezerwacji
5. **Raporty**
   - Raporty finansowe (Chart.js): przychody w określonych miesiącach
   - Raporty dotyczące pokoi (obłożenie)
   - Raporty dotyczące gości (liczba rezerwacji na gościa)
   - Eksport wyników do CSV, Excel, PDF
6. **Logi systemowe**
   - Zapis szczegółów w formacie JSONB w bazie
   - Przeglądanie, usuwanie lub wyczyszczenie logów
7. **Pracownicy**
   - Dodawanie i edycja danych pracownika
   - Przypisywanie roli (Administrator, Recepcjonista, Manager)
   - Zmiana hasła i logina
8. **Wyposażenie**
   - Dodawanie/ edycja/ usuwanie wyposażenia (np. Telewizor, Wi-Fi, Klimatyzacja)
9. **Typy łóżek**
   - Definiowanie i usuwanie typów łóżek (np. Pojedyncze, Podwójne, Królewskie)
10. **Publiczna strona rezerwacji**
   - Możliwość dokonania rezerwacji przez gościa, wybór klasy pokoju, dodatków itp.
   - Automatycznie sprawdzana dostępność pokoi, obliczany koszt itd.
11. **Funkcjonalności**
   - Dodatkowe opisy poszczególnych elementów systemu (moduł informacyjny)

---
## Przykładowe screenshoty
Możesz dołączyć zrzuty ekranu prezentujące działanie poszczególnych widoków – wystarczy, że wstawisz je w odpowiednim miejscu w tym pliku README.

**Przykład:**


![Widok Logowania](assets/images/screenshots/login_view.png)
*Rys. 1. Ekran logowania.*

![Widok Rezerwacji](assets/images/screenshots/rezerwacje_view.png)
*Rys. 2. Lista rezerwacji wraz z opcją dodawania nowej.*


Oczywiście nazwy i ścieżki do plików graficznych musisz dostosować do faktycznej struktury swojego projektu.

---
## Architektura bazodanowa i logika
1. **Baza danych**: PostgreSQL z rozbudowanymi funkcjami, triggerami i widokami.
2. **Warstwa logiki**: 
   - Funkcje w PL/pgSQL obsługują kluczowe procesy (dodawanie rezerwacji, obliczanie kosztów, przydział pokoi, wykwaterowanie, anulowanie). 
   - Triggery dbają o zmianę statusu pokoju, wypełnianie kolumn `max_liczba_osob` i logowanie wydarzeń.
3. **Walidacja**: Rozbudowane constraints, np. 
   - unikalność w `gość(adres_email)` i `pracownik(login)`,
   - check constraints na liczbę osób, kwotę rezerwacji, telefon itp.
4. **Zarządzanie stanem aplikacji**:
   - Szerokie wykorzystanie sesji PHP (np. przechowywanie danych zalogowanego użytkownika),
   - Weryfikacja roli użytkownika przy odwoływaniu się do endpointów.
5. **Możliwość eksportu**:
   - Raporty można eksportować do PDF (jspdf), CSV, Excel (SheetJS).

---
## Utrzymanie i rozwój projektu
1. **Logi systemowe** – zawierają historię operacji. Można je wyczyścić, ale należy pamiętać o ewentualnych kopiach bezpieczeństwa.
2. **Backup bazy** – rekomendowane korzystanie z `pg_dump` (PostgreSQL) do cyklicznego backupu.
3. **Rozbudowa** – w przyszłości można dodać:
   - Moduł powiadomień mailowych
   - Integrację z zewnętrznymi serwisami płatności
   - Zaawansowane moduły raportowania z parametrami czasowymi
   - Integrację z systemami do zarządzania personelem
4. **Zabezpieczenia** – wprowadzić szyfrowanie połączeń, dodać logowanie zdarzeń bezpieczeństwa, rozbudować walidacje i mechanizmy autoryzacji.

---
## Autor
**Imię i nazwisko**: Piotrowski Dawid  
**Kierunek**: Informatyka Stosowana, AGH  
**Data ukończenia**: 15.01.2025r.  

Projekt powstał w ramach zajęć i ma na celu zaprezentowanie umiejętności w zakresie projektowania i implementacji bazy danych (PostgreSQL), tworzenia funkcjonalnych interfejsów w PHP, JavaScript, a także zarządzania złożonymi procesami rezerwacyjnymi.

