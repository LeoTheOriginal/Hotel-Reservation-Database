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
    <title>Funkcjonalność: Raporty - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Raporty</h1>

        <p>
            Moduł <strong>Raporty</strong> pozwala Managerowi lub Administratorowi w szybki i czytelny sposób uzyskać 
            kluczowe informacje na temat różnych obszarów działania hotelu. 
            Dane są prezentowane w postaci <strong>wykresów</strong> oraz <strong>tabel</strong>, 
            a dodatkowo istnieje możliwość eksportu do różnych formatów (PDF, Excel, CSV).
        </p>

        <h2>1. Wybór Raportu</h2>
        <p>
            Na stronie <strong>Raporty</strong> dostępny jest formularz z listą rozwijaną, 
            w której użytkownik może wybrać typ raportu:
        </p>
        <ul>
            <li><strong>Rezerwacje finansowe (financial)</strong> – przychód miesięczny z rezerwacji o statusie „Zrealizowana”.</li>
            <li><strong>Raport dotyczący pokoi (rooms)</strong> – ile rezerwacji przypisanych było do poszczególnych pokoi.</li>
            <li><strong>Raport dotyczący gości (guests)</strong> – łączna liczba rezerwacji (wszystkich i aktywnych) przypadająca na każdego gościa.</li>
        </ul>
        <p>
            Po wybraniu odpowiedniej opcji w polu <strong>Wybierz raport</strong> i kliknięciu <em>Generuj raport</em>, 
            system pobiera dane z bazy i generuje odpowiedni wykres i tabelę.
        </p>

        <h2>2. Generowanie Raportu</h2>
        <p>
            Po kliknięciu przycisku „<strong>Generuj raport</strong>” wywoływany jest plik 
            <code>generuj_raport.php?type=...</code>, który w zależności od wartości parametru <code>type</code>:
        </p>
        <ul>
            <li><code>financial</code> – oblicza przychód z rezerwacji (status „Zrealizowana”) w ujęciu miesięcznym,</li>
            <li><code>rooms</code> – zlicza liczbę rezerwacji przypisanych do każdego pokoju,</li>
            <li><code>guests</code> – prezentuje zestawienie liczby rezerwacji na gościa (aktywnych i wszystkich).</li>
        </ul>
        <p>
            Skrypt zwraca dane w formacie <strong>JSON</strong>, gdzie znajduje się sekcja z informacjami do wykresu (label, wartości) 
            oraz sekcja z danymi tabelarycznymi.
        </p>
        <p>
            Następnie plik <em>script_raporty.js</em> interpretuje otrzymane dane, buduje <strong>wykres</strong> (za pomocą 
            biblioteki <code>Chart.js</code>) oraz <strong>tabelę</strong>.
        </p>

        <h2>3. Prezentacja Wyników</h2>
        <h3>3.1. Wykres</h3>
        <p>
            W zależności od typu raportu, użytkownik może zobaczyć:
        </p>
        <ul>
            <li><strong>Liniowy wykres finansowy</strong> (przychód w czasie) – dla opcji „Rezerwacje finansowe”.</li>
            <li><strong>Wykres kolumnowy</strong> – dla raportu o pokojach i liczbie rezerwacji.</li>
            <li><strong>Wykres kolumnowy z wieloma seriami</strong> – dla raportu o gościach (porównanie liczby aktywnych i wszystkich rezerwacji).</li>
        </ul>
        <p>
            Wykres jest interaktywny (można najechać kursorem na punkty/kolumny, aby zobaczyć szczegółowe wartości).
        </p>

        <h3>3.2. Tabela</h3>
        <p>
            Pod wykresem wyświetlana jest <strong>tabela z danymi szczegółowymi</strong>:
        </p>
        <ul>
            <li><strong>Financial</strong> – każda linia reprezentuje przychód w danym miesiącu.</li>
            <li><strong>Rooms</strong> – każda linia opisuje konkretny pokój i liczbę rezerwacji przypisanych do niego.</li>
            <li><strong>Guests</strong> – zawiera ID gościa, imię, nazwisko, liczbę aktywnych i liczbę wszystkich rezerwacji.</li>
        </ul>

        <h2>4. Eksport</h2>
        <p>
            Podczas generowania raportu wyświetlają się <strong>trzy przyciski eksportu</strong>:
        </p>
        <ul>
            <li><strong>PDF</strong> – korzysta z bibliotek <code>jsPDF</code> oraz <code>jspdf-autotable</code>, a także 
                z funkcji <code>toDataURL</code> z <code>Chart.js</code>, aby wstawić wykres do wygenerowanego pliku PDF.</li>
            <li><strong>Excel</strong> – używa biblioteki <code>SheetJS (XLSX)</code> do wygenerowania pliku .xlsx 
                (zawierającego dane z tabeli w arkuszu Excela).</li>
            <li><strong>CSV</strong> – generuje surowy plik tekstowy w formacie CSV, gdzie poszczególne kolumny rozdzielone są przecinkami.</li>
        </ul>
        <p>
            Dzięki tym opcjom menedżer lub administrator może w prosty sposób przenosić dane raportu do zewnętrznych systemów analitycznych 
            czy sprawozdawczych.
        </p>

        <h2>5. Logika Raportu – Wybrane Przykłady</h2>

        <h3>5.1. Rezerwacje Finansowe (financial)</h3>
        <p>
            Raport koncentruje się na rezerwacjach ze statusem „Zrealizowana” i sumuje ich <code>kwota_rezerwacji</code> 
            w ujęciu miesięcznym. 
            Zapytanie w <code>generuj_raport.php</code> wykorzystuje funkcję <code>DATE_TRUNC('month', ...)</code> i grupowanie:
        </p>
        <pre><code>
// ...
SELECT DATE_TRUNC('month', r.data_zameldowania) AS miesiąc,
       SUM(r.kwota_rezerwacji) AS przychód
FROM rezerwacje_hotelowe.rezerwacja r
WHERE r.status_rezerwacji_id = (SELECT id FROM rezerwacje_hotelowe.status_rezerwacji
                                WHERE nazwa_statusu = 'Zrealizowana')
GROUP BY miesiąc
ORDER BY miesiąc
        </code></pre>
        <p>
            Wynik jest rysowany jako wykres liniowy (czas vs. przychód).
        </p>

        <h3>5.2. Raport dotyczący pokoi (rooms)</h3>
        <p>
            Dla każdego pokoju (z tabeli <code>pokój</code>) system zlicza, ile rezerwacji (tabela <code>rezerwacja_pokój</code>) 
            jest z nim powiązanych. 
            Zapytanie łączy <em>pokój</em> z <em>rezerwacja_pokój</em> i <em>rezerwacja</em> 
            (choć tutaj <em>rezerwacja</em> jest left-joinem i może nie być aktywnych rezerwacji).
        </p>
        <p>
            Rezultatem jest kolumnowy wykres z liczbą rezerwacji w danym pokoju.
        </p>

        <h3>5.3. Raport dotyczący gości (guests)</h3>
        <p>
            System prezentuje łącznie: 
        </p>
        <ul>
            <li><strong>Liczbę aktywnych rezerwacji</strong> (status rezerwacji inny niż „Anulowana”).</li>
            <li><strong>Liczbę wszystkich rezerwacji</strong> (niezależnie od statusu).</li>
        </ul>
        <p>
            Wykres tworzony jest bar-chartem z dwiema seriami (datasets): „Aktywne rezerwacje” i „Wszystkie rezerwacje”.
        </p>

        <h2>6. Bezpieczeństwo i Kontrola Dostępu</h2>
        <p>
            Zgodnie z plikiem <code>raporty.php</code>, jedynie <strong>Administrator</strong> oraz <strong>Manager</strong> 
            mają dostęp do funkcjonalności generowania raportów:
        </p>
        <pre><code>
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true
    || ($_SESSION['role'] !== ROLE_ADMINISTRATOR && $_SESSION['role'] !== ROLE_MANAGER)) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
        </code></pre>
        <p>
            Zapewnia to, że osoby bez wystarczających uprawnień nie będą mogły wygenerować wrażliwych danych raportowych.
        </p>

        <h2>7. Techniczne Szczegóły Wdrożenia</h2>
        <ul>
            <li><strong>Chart.js</strong> – biblioteka do wizualizacji danych w postaci wykresów (CDN: <code>https://cdn.jsdelivr.net/npm/chart.js</code>).</li>
            <li><strong>jsPDF & jspdf-autotable</strong> – generacja plików PDF oraz wstawianie do nich tabel i obrazów (wykresów).</li>
            <li><strong>SheetJS (XLSX)</strong> – generowanie plików Excel z tabeli HTML.</li>
            <li><strong>CSV</strong> – prosta funkcja do konwersji tabeli HTML na CSV (rozszerzalna wedle potrzeb).</li>
        </ul>

        <h2>8. Korzyści z Modułu Raportów</h2>
        <ul>
            <li><strong>Szybki wgląd w kluczowe dane</strong> (przychody, obłożenie pokoi, statystyki gości) bez konieczności pisania osobnych zapytań.</li>
            <li><strong>Łatwy eksport</strong> – umożliwia przesłanie danych do księgowości, działu marketingu czy innych systemów analitycznych.</li>
            <li><strong>Poprawa efektywności</strong> – menedżerowie mogą w oparciu o dane raportów podejmować decyzje np. o promocjach, strategii cenowej, zarządzaniu infrastrukturą.</li>
        </ul>

        <h2>9. Podsumowanie</h2>
        <p>
            Moduł <strong>Raporty</strong> w systemie rezerwacji hotelu <em>Stardust Hotel</em> to potężne narzędzie, 
            które umożliwia menedżerom i administratorom szybki dostęp do kluczowych informacji finansowych i operacyjnych. 
            Dzięki różnorodnym typom raportów, możliwość wizualizacji danych (wykresy) 
            i eksportu (PDF/Excel/CSV), moduł ten usprawnia analizę i prezentację informacji, co wspiera proces decyzyjny 
            i pozwala lepiej zarządzać zasobami hotelu.
        </p>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
