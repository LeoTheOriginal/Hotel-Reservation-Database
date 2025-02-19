<?php

include_once __DIR__ . '/../../includes/header.php';

// Sprawdzenie, czy użytkownik jest zalogowany i czy ma uprawnienia Administratora
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== ROLE_ADMINISTRATOR) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Funkcjonalność: Logi Systemowe - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Logi Systemowe</h1>

        <p>
            Moduł <strong>Logi Systemowe</strong> umożliwia Administratorom monitorowanie i zarządzanie działaniami wykonywanymi w systemie <em>Stardust Hotel</em>. 
            Dzięki temu modułowi można śledzić wszelkie operacje, które wpływają na działanie systemu, co jest kluczowe dla zapewnienia bezpieczeństwa i integralności danych.
        </p>

        <h2>1. Dostęp do strony i Kontrola Ról</h2>
        <p>
            Dostęp do modułu <strong>Logi Systemowe</strong> mają wyłącznie użytkownicy o roli:
        </p>
        <ul>
            <li><strong>Administrator</strong></li>
        </ul>
        <p>
            Kontrola dostępu jest realizowana poprzez sprawdzenie sesji i roli użytkownika w pliku <em>logi_systemowe.php</em>:
        </p>
        <pre><code class="language-php">
if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    $_SESSION['role'] !== ROLE_ADMINISTRATOR
) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
        </code></pre>

        <h3>Uprawnienia Administratora</h3>
        <ul>
            <li><strong>Pełen dostęp</strong> – może przeglądać wszystkie logi systemowe, usuwać pojedyncze wpisy oraz wyczyścić całą tabelę logów.</li>
        </ul>

        <h2>2. Przeglądanie Logów Systemowych</h2>
        <p>
            Na stronie <strong>Logi Systemowe</strong> znajduje się tabela prezentująca wszystkie zarejestrowane logi:
        </p>
        <ul>
            <li><strong>ID</strong> – unikalny identyfikator loga,</li>
            <li><strong>Data</strong> – czas wykonania operacji,</li>
            <li><strong>Opis</strong> – krótki opis operacji,</li>
            <li><strong>Szczegóły</strong> – szczegółowe informacje w formacie JSON,</li>
            <li><strong>Akcje</strong> – przyciski <em>Usuń</em> do usuwania pojedynczych logów.</li>
        </ul>
        <p>
            Tabela jest dynamicznie wypełniana danymi za pomocą JavaScript, który pobiera dane z pliku <em>pobierz_logi_systemowe.php</em>. 
            Dzięki temu Administrator może szybko przeglądać najnowsze logi bez konieczności odświeżania strony.
        </p>

        <h2>3. Dodawanie Nowego Loga (Automatyczne)</h2>
        <p>
            Nowe logi systemowe są dodawane automatycznie przez system w momencie wykonywania operacji, takich jak dodawanie, edycja czy usuwanie danych w innych modułach. 
            Administrator nie musi ręcznie dodawać logów.
        </p>

        <h2>4. Usuwanie Logów Systemowych</h2>
        <p>
            Administrator ma możliwość usuwania pojedynczych logów lub całej tabeli logów:
        </p>
        <ul>
            <li><strong>Usuwanie pojedynczego loga:</strong> Kliknięcie przycisku <em>Usuń</em> przy wybranym logu otwiera okno dialogowe potwierdzenia. Po potwierdzeniu, log zostaje usunięty za pomocą skryptu <em>usun_logi_systemowe.php</em>.</li>
            <li><strong>Wyczyść wszystkie logi:</strong> Kliknięcie przycisku <em>Wyczyść wszystkie logi</em> usuwa wszystkie wpisy z tabeli logów za pomocą skryptu <em>wyczysc_logi_systemowe.php</em>.</li>
        </ul>

        <h2>5. Szczegóły Logów</h2>
        <p>
            Każdy log posiada sekcję <strong>Szczegóły</strong>, która zawiera dodatkowe informacje w formacie JSON. 
            Administrator może kliknąć przycisk <em>Zobacz</em> w kolumnie <strong>Szczegóły</strong>, aby otworzyć modal z pełnym opisem loga.
        </p>

        <h2>6. Bezpieczeństwo i Walidacja</h2>
        <p>
            Operacje w module <strong>Logi Systemowe</strong> są ściśle kontrolowane, aby zapewnić bezpieczeństwo danych:
        </p>
        <ol>
            <li><strong>Kontrola Dostępu:</strong> Tylko Administratorzy mają dostęp do przeglądania i zarządzania logami systemowymi.</li>
            <li><strong>Walidacja Danych:</strong> Wszystkie operacje usuwania logów są zabezpieczone poprzez sprawdzenie poprawności danych wejściowych (np. ID loga).</li>
            <li><strong>Ochrona Przed SQL Injection:</strong> Użycie przygotowanych zapytań <code>prepare/execute</code> w backendowych skryptach zabezpiecza przed atakami typu SQL Injection.</li>
        </ol>

        <h2>7. Najważniejsze Pliki i Endpointy</h2>
        <ul>
            <li><strong>logi_systemowe.php</strong> – główny widok modułu Logi Systemowe, zawiera tabelę logów oraz przyciski do zarządzania logami.</li>
            <li><strong>pobierz_logi_systemowe.php</strong> – zwraca JSON z listą wszystkich logów systemowych.</li>
            <li><strong>usun_logi_systemowe.php</strong> – usuwa pojedynczy log systemowy na podstawie przekazanego ID (<code>DELETE</code>).</li>
            <li><strong>wyczysc_logi_systemowe.php</strong> – usuwa wszystkie logi systemowe z bazy danych (<code>TRUNCATE</code> lub <code>DELETE</code>).</li>
            <li><strong>script_logi_systemowe.js</strong> – obsługuje interakcje front-endu, takie jak pobieranie logów, usuwanie pojedynczych logów oraz wyczyszczanie całej tabeli logów.</li>
            <li><strong>style_logi.css</strong> – stylizuje wygląd strony Logi Systemowe, tabeli, przycisków oraz modali.</li>
        </ul>

        <h2>8. Przykładowy Przebieg Przeglądania i Usuwania Loga</h2>
        <ol>
            <li><strong>Przeglądanie Logów:</strong> Administrator otwiera stronę <em>Logi Systemowe</em>, gdzie widzi tabelę z logami. Logi są sortowane od najnowszych do najstarszych.</li>
            <li><strong>Wyświetlanie Szczegółów:</strong> Kliknięcie przycisku <em>Zobacz</em> w kolumnie <strong>Szczegóły</strong> otwiera modal z pełnym opisem loga w formacie JSON.</li>
            <li><strong>Usuwanie Pojedynczego Loga:</strong> Kliknięcie przycisku <em>Usuń</em> przy wybranym logu otwiera okno dialogowe potwierdzenia. Po potwierdzeniu, log zostaje usunięty, a tabela jest odświeżana.</li>
            <li><strong>Wyczyść Wszystkie Logi:</strong> Kliknięcie przycisku <em>Wyczyść wszystkie logi</em> otwiera okno dialogowe potwierdzenia. Po potwierdzeniu, wszystkie logi zostają usunięte, a tabela jest pusta.</li>
        </ol>

        <h2>9. Przykładowy Przebieg Usuwania Loga</h2>
        <ol>
            <li><strong>Otworzenie Logów:</strong> Administrator przechodzi do modułu Logi Systemowe i widzi tabelę z logami.</li>
            <li><strong>Wybór Loga:</strong> Administrator wybiera log do usunięcia poprzez kliknięcie przycisku <em>Usuń</em> w kolumnie <strong>Akcje</strong>.</li>
            <li><strong>Potwierdzenie:</strong> System wyświetla okno dialogowe potwierdzenia. Administrator potwierdza chęć usunięcia loga.</li>
            <li><strong>Usunięcie Loga:</strong> Skrypt <em>usun_logi_systemowe.php</em> usuwa log z bazy danych.</li>
            <li><strong>Odświeżenie Tabeli:</strong> Tabela z logami jest odświeżana, aby odzwierciedlić usunięcie loga.</li>
            <li><strong>Komunikat:</strong> Administrator otrzymuje komunikat o pomyślnym usunięciu loga.</li>
        </ol>

        <h2>10. Podsumowanie</h2>
        <p>
            Moduł <strong>Logi Systemowe</strong> w systemie <em>Stardust Hotel</em> jest niezbędnym narzędziem dla Administratorów, którzy chcą monitorować i zarządzać działaniami wykonywanymi w systemie. 
            Dzięki intuicyjnemu interfejsowi, dynamicznie aktualizowanej tabeli oraz funkcjom usuwania pojedynczych logów lub całej tabeli, Administracja może efektywnie śledzić i reagować na wszelkie operacje. 
            Implementacja odpowiednich zabezpieczeń i walidacji danych zapewnia, że moduł działa bezpiecznie i niezawodnie, co przekłada się na stabilność całego systemu.
        </p>
        <p><strong>Najważniejsze Cechy Modułu Logi Systemowe:</strong></p>
        <ul>
            <li>Pełny dostęp dla Administratorów – możliwość przeglądania, usuwania pojedynczych logów oraz wyczyszczania całej tabeli logów.</li>
            <li>Dynamiczna tabela logów – automatyczne pobieranie i odświeżanie danych za pomocą JavaScript.</li>
            <li>Detaliczne logi – każdy log zawiera szczegółowe informacje w formacie JSON, dostępne w modalu.</li>
            <li>Bezpieczeństwo – kontrola dostępu, walidacja danych oraz ochrona przed SQL Injection.</li>
            <li>Rozszerzalność – możliwość dodania funkcji filtrowania, paginacji czy eksportu logów.</li>
        </ul>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
