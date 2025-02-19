<?php

include_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Funkcjonalność: Dashboard - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Dashboard Pracownika</h1>
        
        <p>
            Moduł <strong>Dashboard</strong> jest głównym ekranem, na który trafia zalogowany pracownik systemu <em>Stardust Hotel</em>. 
            Służy on do wyświetlania podstawowych informacji o koncie pracownika oraz umożliwia wykonywanie najważniejszych operacji, 
            takich jak zmiana hasła, zmiana loginu czy modyfikacja zdjęcia profilowego.
        </p>

        <h2>1. Dostęp do Dashboardu</h2>
        <p>
            Dostęp do <strong>Dashboardu</strong> jest ograniczony do zalogowanych pracowników. Gdy pracownik poda poprawne dane logowania, 
            sesja zostaje utrzymana, a użytkownik jest przekierowywany właśnie na stronę Dashboardu. 
        </p>
        <p>
            W pliku <em>dashboard.php</em> weryfikowane jest, czy:
        </p>
        <pre><code class="language-php">
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
            </code></pre>
        <p>
            Jeśli warunek nie jest spełniony, użytkownik jest przekierowywany do strony logowania. 
            Dzięki temu żaden niezalogowany użytkownik nie ma dostępu do informacji o koncie pracownika.
        </p>

        <h2>2. Profil Pracownika</h2>
        <p>
            Po zalogowaniu, pracownik widzi w sekcji <strong>Profil</strong> między innymi:
        </p>
        <ul>
            <li><strong>Zdjęcie profilowe</strong> (jeśli zostało wgrane; w przeciwnym razie wyświetlane jest domyślne zdjęcie),</li>
            <li><strong>Dane osobowe</strong> (imię, nazwisko, login),</li>
            <li><strong>Aktualną rolę</strong> (Administrator, Manager, Recepcjonista itp.),</li>
            <li><strong>Formularz zmiany loginu i hasła</strong> w postaci modali wyświetlanych po kliknięciu przycisków „Zmień Login” lub „Zmień Hasło”.</li>
        </ul>

        <h3>Zdjęcie Profilowe</h3>
        <p>
            Aby zmienić zdjęcie profilowe, pracownik klika etykietę „Zmień zdjęcie” i wybiera plik graficzny z dysku. 
            Po wybraniu pliku następuje automatyczne przesłanie formularza do skryptu <em>upload_picture.php</em>, który:
        </p>
        <ol>
            <li>Weryfikuje rozszerzenie i rozmiar pliku,</li>
            <li>Tworzy unikalną nazwę,</li>
            <li>Zapisuje plik w folderze <code>/assets/images/uploads/</code>,</li>
            <li>Aktualizuje ścieżkę do pliku w bazie danych.</li>
        </ol>
        <p>
            Następnie pracownik widzi swoje nowe zdjęcie wczytane na <strong>Dashboardzie</strong>. 
            Jeśli pracownik miał poprzednio inne zdjęcie, jest ono usuwane, aby nie zajmowało niepotrzebnie miejsca na serwerze.
        </p>

        <h3>Zmiana Loginu i Hasła</h3>
        <p>
            Dashboard zawiera dwa przyciski:
        </p>
        <ul>
            <li><strong>Zmień Login</strong> – otwiera modal z formularzem do wprowadzenia nowego loginu oraz potwierdzenia aktualnego hasła,</li>
            <li><strong>Zmień Hasło</strong> – otwiera modal, w którym należy wprowadzić aktualne hasło, nowe hasło i jego potwierdzenie.</li>
        </ul>
        <p>
            W obu przypadkach wykonywana jest weryfikacja w bazie danych, czy podane aktualne hasło jest poprawne. 
            Jeśli tak, następuje zmiana loginu (sprawdzana jest także unikalność) lub aktualizacja hasła. 
            Po pomyślnym wykonaniu operacji na ekranie pojawia się komunikat o sukcesie.
        </p>

        <h2>3. Struktura i Pliki</h2>
        <p>
            Główne pliki związane z modułem <strong>Dashboard Pracownika</strong>:
        </p>
        <ul>
            <li>
                <strong>dashboard.php</strong> – główny widok zawierający informacje o zalogowanym pracowniku (imię, nazwisko, login, rola) 
                oraz formularze do zmiany loginu/hasła i zmiany zdjęcia profilowego.
            </li>
            <li>
                <strong>login.php</strong> oraz <strong>login_process.php</strong> – obsługa logowania, weryfikacja danych w bazie i 
                przekierowanie na <em>dashboard.php</em> po pomyślnym zalogowaniu.
            </li>
            <li>
                <strong>logout.php</strong> – wylogowanie użytkownika, zniszczenie sesji i przekierowanie do strony logowania.
            </li>
            <li>
                <strong>update_login.php</strong> – skrypt obsługujący zmianę loginu w bazie (wymaga podania aktualnego hasła).
            </li>
            <li>
                <strong>update_password.php</strong> – skrypt obsługujący zmianę hasła (aktualne hasło + nowe hasło).
            </li>
            <li>
                <strong>upload_picture.php</strong> – skrypt obsługujący wysyłanie nowego zdjęcia profilowego i aktualizujący rekord w bazie.
            </li>
            <li>
                <strong>script_dashboard.js</strong> – kod JavaScript odpowiedzialny za otwieranie i zamykanie modali (do zmiany loginu i hasła), 
                podgląd zdjęcia przed uploadem oraz automatyczne wysłanie formularza.
            </li>
            <li>
                <strong>style_dashboard.css</strong> – stylizacja strony <em>dashboard.php</em>, np. układ sekcji profilu i informacji, 
                styl przycisków, modali itp.
            </li>
            <li>
                <strong>style_login.css</strong> – stylizacja ekranu logowania dla pracowników (opcjonalnie wspólna z innymi stylami).
            </li>
        </ul>

        <h2>4. Logika i Przebieg Działania</h2>
        <ol>
            <li><strong>Logowanie:</strong> Pracownik wypełnia dane na <em>login.php</em>. Po pomyślnej weryfikacji hasła w <em>login_process.php</em>, 
                ustawiana jest sesja i użytkownik trafia na <em>dashboard.php</em>.</li>
            <li><strong>Wyświetlenie Profilu:</strong> Plik <em>dashboard.php</em> pobiera dane o zalogowanym pracowniku (imię, nazwisko, login, rola, ścieżka do zdjęcia) 
                i wyświetla je na stronie.</li>
            <li><strong>Zmiana Zdjęcia:</strong> Kliknięcie „Zmień zdjęcie” wczytuje plik i automatycznie wysyła go do <em>upload_picture.php</em>. 
                Po sprawdzeniu i zapisaniu pliku w folderze <code>/assets/images/uploads/</code> ścieżka jest aktualizowana w bazie. 
                Po zakończeniu operacji <em>dashboard.php</em> się odświeża, wyświetlając nowe zdjęcie.</li>
            <li><strong>Zmiana Login/Hasło:</strong> Przyciski „Zmień Login” lub „Zmień Hasło” otwierają modale z formularzami. 
                Po wypełnieniu i wysłaniu skrypt <em>update_login.php</em> lub <em>update_password.php</em> weryfikuje dane (m.in. aktualne hasło) 
                i odpowiednio aktualizuje rekord pracownika w bazie.</li>
            <li><strong>Wylogowanie:</strong> Pracownik może kliknąć „Wyloguj” w nagłówku lub w innym miejscu. 
                Plik <em>logout.php</em> usuwa dane sesji i przekierowuje do <em>login.php</em>.</li>
        </ol>

        <h2>5. Bezpieczeństwo i Walidacja</h2>
        <ul>
            <li><strong>Kontrola Dostępu:</strong> Bez ważnej sesji pracownik nie wejdzie na <em>dashboard.php</em>. 
                Dodatkowo weryfikowane jest, czy pracownik istnieje w bazie (zapobiega to sytuacji, w której ID nie istnieje).</li>
            <li><strong>Walidacja Formularzy:</strong> Zarówno formularz logowania, jak i zmiany loginu/hasła sprawdzają wymagane pola, 
                długości haseł itp. Komunikaty o błędach są przechowywane w <code>$_SESSION</code> i wyświetlane na stronach.</li>
            <li><strong>Operacje na Plikach:</strong> <em>upload_picture.php</em> sprawdza typ i rozmiar pliku, aby uniknąć złośliwych uploadów. 
                Nazwa pliku jest generowana unikatowo, a poprzednie zdjęcie jest usuwane.</li>
            <li><strong>Hashowanie Haseł:</strong> W przykładzie użyto MD5 (dla celów demo).</li>
        </ul>

        <h2>6. Rozbudowa Dashboardu</h2>
        <p>
            Moduł <strong>Dashboard</strong> można rozszerzyć o dodatkowe funkcje, takie jak:
        </p>
        <ul>
            <li><strong>Statystyki i Raporty</strong> – wyświetlanie podstawowych statystyk (np. ilości rezerwacji, statusów rezerwacji, 
                przeglądu płatności).</li>
            <li><strong>Powiadomienia i Wiadomości</strong> – wprowadzenie systemu notyfikacji, np. o nowych rezerwacjach czy zbliżających się terminach.</li>
            <li><strong>Personalizacja</strong> – możliwość ustawiania motywów kolorystycznych, preferencji językowych itp.</li>
        </ul>

        <h2>7. Podsumowanie</h2>
        <p>
            Moduł <strong>Dashboard Pracownika</strong> stanowi centralny punkt dostępu dla wszystkich zalogowanych pracowników w systemie <em>Stardust Hotel</em>. 
            Zapewnia spersonalizowany widok pozwalający na:
        </p>
        <ul>
            <li>Sprawdzenie podstawowych informacji o koncie (imię, nazwisko, login, rola),</li>
            <li>Aktualizację danych uwierzytelniających (login, hasło),</li>
            <li>Wgranie lub zmianę zdjęcia profilowego,</li>
            <li>Wylogowanie z systemu.</li>
        </ul>
        <p>
            Dzięki przejrzystemu układowi i zastosowaniu dobrych praktyk (m.in. w zakresie bezpieczeństwa) 
            Dashboard jest wygodnym narzędziem do codziennej pracy dla pracowników hotelu, niezależnie od pełnionej funkcji (Administrator, Manager, Recepcjonista itd.). 
            Możliwość dalszej rozbudowy pozwala dostosować go do rosnących potrzeb i wymagań organizacji.
        </p>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
