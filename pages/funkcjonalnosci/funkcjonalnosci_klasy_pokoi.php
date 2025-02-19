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
    <title>Funkcjonalność: Klasy Pokoi - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Klasy Pokoi</h1>
        
        <p>
            Moduł <strong>Klasy Pokoi</strong> pozwala na zarządzanie informacjami o dostępnych rodzajach (klasach) pokoi w hotelu. 
            Dla każdej klasy możemy ustalić jej <strong>cenę podstawową</strong>, wskazać <strong>typy łóżek</strong> 
            (np. pojedyncze, podwójne, królewskie) oraz <strong>wyposażenie</strong> (telewizor, minibar, klimatyzacja itp.).
        </p>

        <h2>1. Przeglądanie listy klas pokoi</h2>
        <p>
            Na stronie <strong>Lista Klas Pokoi</strong> widoczna jest tabela z kolumnami:
        </p>
        <ul>
            <li><strong>#</strong> – numer porządkowy (wygodny do przeglądania),</li>
            <li><strong>Nazwa klasy</strong> – tekstowa nazwa klasy (np. „Standard”, „Deluxe” itd.),</li>
            <li><strong>Cena podstawowa</strong> – stawka bazowa za noc, do której mogą być doliczane inne dodatki,</li>
            <li><strong>Akcje</strong> – przyciski <em>Edytuj</em> i <em>Usuń</em> (ten ostatni tylko, jeśli dana klasa nie jest powiązana z istniejącymi pokojami oraz jeśli zalogowana rola użytkownika to Administrator/Manager).</li>
        </ul>

        <p>
            Jeżeli klasa jest <strong>powiązana z istniejącymi pokojami</strong>, system blokuje możliwość jej usunięcia 
            (wyświetla przycisk wyszarzony, z komunikatem o blokadzie).
            Dodatkowo w warstwie aplikacji, jeśli aktualnie zalogowany użytkownik nie jest 
            <strong>Administratorem</strong> lub <strong>Managerem</strong>, przycisk <em>Usuń</em> 
            również będzie domyślnie wyszarzony.
        </p>

        <h2>2. Dodawanie nowej klasy pokoju</h2>
        <p>
            Aby dodać nową klasę, kliknij <strong>Dodaj nową klasę pokoju</strong>. Pojawi się modal z formularzem, 
            w którym uzupełniamy:
        </p>
        <ul>
            <li><strong>Nazwę klasy</strong> (np. Standard, Deluxe),</li>
            <li><strong>Cenę podstawową</strong>,</li>
            <li><strong>Rodzaje łóżek</strong> – można wybrać wiele typów łóżek w <em>select multiple</em>,</li>
            <li><strong>Wyposażenie</strong> – lista checkboxów odpowiadających dostępnym elementom (telewizor, klimatyzacja, Wi-Fi, itp.).</li>
        </ul>
        <p>
            Po wypełnieniu formularza i zatwierdzeniu, klasa zostanie zapisana w bazie danych, razem z przypisanym wyposażeniem i typami łóżek.
        </p>

        <h2>3. Edycja istniejącej klasy pokoju</h2>
        <p>
            W kolumnie <em>Akcje</em> dla każdej klasy znajduje się przycisk <strong>Edytuj</strong>. Po jego kliknięciu 
            wyświetla się okno modalne, w którym można zmodyfikować:
        </p>
        <ul>
            <li><strong>Nazwę klasy</strong> i <strong>cenę podstawową</strong>,</li>
            <li><strong>Rodzaje łóżek</strong> – dodać nowe, usunąć stare,</li>
            <li><strong>Wyposażenie</strong> – dodać nowe pozycje lub odznaczyć te, które nie są już potrzebne.</li>
        </ul>
        <p>
            Po zapisaniu zmian dane w bazie zostają zaktualizowane, a tabela na stronie – odświeżona.
        </p>

        <h2>4. Zasady usuwania klasy pokoju</h2>
        <p>
            Funkcjonalność <strong>Usuń</strong> przy klasie pokoju dostępna jest wyłącznie, jeśli:
        </p>
        <ul>
            <li>nie jest ona przypisana do żadnego <em>pokoju</em> (inaczej musielibyśmy najpierw zmienić klasę pokoju na inną, aby zwolnić tę zależność),</li>
            <li>aktualna rola użytkownika to <strong>Administrator</strong> lub <strong>Manager</strong> (dla innych ról przycisk będzie wyszarzony).</li>
        </ul>
        <p>
            Wewnętrznie sprawdzane jest to w dwóch miejscach:
        </p>
        <ol>
            <li><strong>Front-end</strong> – w pliku <em>script_klasy_pokoi.js</em> sprawdzamy atrybut <code>is_deletable</code> (czy klasa nie jest używana przez żaden pokój) oraz rolę użytkownika (<em>userRole</em>), żeby przycisk miał klasę <code>btn-disabled</code> w razie konieczności.</li>
            <li><strong>Back-end</strong> – w pliku <em>usun_klase_pokoju.php</em> ponownie weryfikujemy zależności w bazie danych (np. czy wciąż istnieją jakieś pokoje powiązane z tą klasą) i w razie potrzeby blokujemy usunięcie.</li>
        </ol>

        <h2>Podsumowanie</h2>
        <p>
            Moduł <strong>Klasy Pokoi</strong> zapewnia centralne miejsce do:
        </p>
        <ul>
            <li>Tworzenia i edycji rodzajów (klas) pokoi z określoną ceną,</li>
            <li>Przypisywania do klas konkretnych <em>typów łóżek</em> (m.in. w celu obliczania maks. liczby osób),</li>
            <li>Określania <em>wyposażenia</em> dostępnego w danym typie pokoju,</li>
            <li>Usuwania niepotrzebnych już klas – pod warunkiem, że nie są powiązane z żadnym istniejącym pokojem.</li>
        </ul>
        <p>
            Podobnie jak w przypadku pozostałych modułów, uprawnienia do <strong>usuwania</strong> 
            mają wyłącznie Administrator i Manager.
        </p>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
