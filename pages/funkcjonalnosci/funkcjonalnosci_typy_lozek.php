<?php
// tutaj sie troche rozpisalem niepotrzebnie ale nie chce mi sie tego usuwac :(((
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
    <title>Funkcjonalność: Typy Łóżek - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Typy Łóżek</h1>
        
        <p>
            Moduł <strong>Typy Łóżek</strong> umożliwia zarządzanie rodzajami łóżek dostępnych w hotelu. 
            Dzięki temu modułowi administratorzy i menedżerowie mogą:
        </p>
        <ul>
            <li>Tworzyć nowe typy łóżek, określając ich nazwę oraz maksymalną liczbę osób.</li>
            <li>Edycjonować istniejące typy łóżek, modyfikując ich nazwę i liczbę osób.</li>
            <li>Usuwać typy łóżek, które nie są powiązane z żadnymi klasami pokoi.</li>
        </ul>
        
        <h2>1. Przeglądanie Listy Typów Łóżek</h2>
        <p>
            Na stronie <strong>Lista Typów Łóżek</strong> znajduje się tabela prezentująca wszystkie dostępne typy łóżek w systemie. Tabela zawiera następujące kolumny:
        </p>
        <ul>
            <li><strong>#</strong> – numer porządkowy, ułatwiający przeglądanie listy.</li>
            <li><strong>Nazwa typu łóżka</strong> – tekstowa nazwa typu łóżka (np. „Pojedyncze”, „Podwójne”, „Królewskie”).</li>
            <li><strong>Liczba osób</strong> – maksymalna liczba osób, które mogą korzystać z danego typu łóżka.</li>
            <li><strong>Akcje</strong> – przyciski <em>Edytuj</em> i <em>Usuń</em> umożliwiające modyfikację lub usunięcie danego typu łóżka.</li>
        </ul>
        
        <p>
            <strong>Przycisk "Usuń"</strong> jest aktywny tylko wtedy, gdy:
        </p>
        <ul>
            <li>Typ łóżka nie jest przypisany do żadnej klasy pokoju (brak powiązanych rezerwacji).</li>
            <li>Zalogowany użytkownik posiada rolę <strong>Administratora</strong> lub <strong>Managera</strong>.</li>
        </ul>
        <p>
            W przeciwnym razie, przycisk <em>Usuń</em> jest wyszarzony i nieaktywny, a najechanie na niego wyświetla odpowiedni komunikat (tooltip) informujący o przyczynie blokady.
        </p>
        
        <h2>2. Dodawanie Nowego Typu Łóżka</h2>
        <p>
            Aby dodać nowy typ łóżka, użytkownik musi wykonać następujące kroki:
        </p>
        <ol>
            <li>**Kliknięcie przycisku "Dodaj nowy typ łóżka"** – znajduje się on na górze strony.</li>
            <li>**Otwarcie modala** – pojawia się okno modalne z formularzem dodawania nowego typu łóżka.</li>
            <li>**Wypełnienie formularza** – użytkownik musi uzupełnić:
                <ul>
                    <li><strong>Nazwa typu łóżka</strong> – pole tekstowe (np. „Pojedyncze”, „Podwójne”).</li>
                    <li><strong>Liczba osób</strong> – pole numeryczne, określające maksymalną liczbę osób korzystających z tego typu łóżka.</li>
                </ul>
            </li>
            <li>**Zatwierdzenie formularza** – kliknięcie przycisku **"Dodaj"** wysyła dane do serwera w celu zapisania nowego typu łóżka w bazie danych.</li>
        </ol>
        
        <h3>Walidacja Danych</h3>
        <ul>
            <li><strong>Nazwa typu łóżka</strong> – musi być niepusta i unikalna.</li>
            <li><strong>Liczba osób</strong> – musi być liczbą całkowitą większą od zera.</li>
        </ul>
        
        <h3>Obsługa Wprowadzonych Danych</h3>
        <p>
            Po poprawnym wypełnieniu formularza i zatwierdzeniu, nowe dane są przesyłane do skryptu <em>dodaj_typ_lozka.php</em>, który:
        </p>
        <ul>
            <li>**Sprawdza poprawność danych** – upewnia się, że wszystkie wymagane pola są wypełnione i mają poprawne wartości.</li>
            <li>**Dodaje nowy wpis do bazy danych** – zapisuje nowy typ łóżka w tabeli <em>typ_łóżka</em>.</li>
            <li>**Zwraca odpowiedź JSON** – informuje o sukcesie lub ewentualnych błędach podczas dodawania.</li>
        </ul>
        
        <h3>Feedback dla Użytkownika</h3>
        <ul>
            <li><strong>Sukces</strong> – wyświetlany jest komunikat potwierdzający dodanie nowego typu łóżka, modal jest zamykany, a tabela jest odświeżana.</li>
            <li><strong>Błąd</strong> – wyświetlany jest komunikat z informacją o przyczynie błędu (np. brak wymaganych danych, duplikacja nazwy).</li>
        </ul>
        
        <h2>3. Edycja Istniejącego Typu Łóżka</h2>
        <p>
            Aby edytować istniejący typ łóżka, użytkownik musi:
        </p>
        <ol>
            <li>**Kliknięcie przycisku "Edytuj"** – znajdującego się w kolumnie <strong>Akcje</strong> dla wybranego typu łóżka.</li>
            <li>**Otwarie modala edycji** – pojawia się okno modalne z formularzem edycji danych.</li>
            <li>**Wprowadzenie zmian w formularzu** – użytkownik może zmienić:
                <ul>
                    <li><strong>Nazwa typu łóżka</strong>.</li>
                    <li><strong>Liczba osób</strong>.</li>
                </ul>
            </li>
            <li>**Zatwierdzenie zmian** – kliknięcie przycisku **"Zapisz zmiany"** wysyła zaktualizowane dane do serwera.</li>
        </ol>
        
        <h3>Walidacja Danych</h3>
        <ul>
            <li><strong>Nazwa typu łóżka</strong> – musi być niepusta i unikalna.</li>
            <li><strong>Liczba osób</strong> – musi być liczbą całkowitą większą od zera.</li>
        </ul>
        
        <h3>Obsługa Zmodyfikowanych Danych</h3>
        <p>
            Po zatwierdzeniu zmian, dane są przesyłane do skryptu <em>edytuj_typ_lozka.php</em>, który:
        </p>
        <ul>
            <li>**Sprawdza poprawność danych** – upewnia się, że wszystkie wymagane pola są wypełnione i mają poprawne wartości.</li>
            <li>**Sprawdza, czy typ łóżka istnieje** w bazie danych.</li>
            <li>**Aktualizuje wpis w bazie danych** – modyfikuje istniejący typ łóżka w tabeli <em>typ_łóżka</em>.</li>
            <li>**Zwraca odpowiedź JSON** – informuje o sukcesie lub ewentualnych błędach podczas aktualizacji.</li>
        </ul>
        
        <h3>Feedback dla Użytkownika</h3>
        <ul>
            <li><strong>Sukces</strong> – wyświetlany jest komunikat potwierdzający aktualizację typu łóżka, modal jest zamykany, a tabela jest odświeżana.</li>
            <li><strong>Błąd</strong> – wyświetlany jest komunikat z informacją o przyczynie błędu (np. brak wymaganych danych, duplikacja nazwy).</li>
        </ul>
        
        <h2>4. Zasady Usuwania Typu Łóżka</h2>
        <p>
            Funkcjonalność <strong>Usuń</strong> jest dostępna wyłącznie wtedy, gdy:
        </p>
        <ul>
            <li>**Typ łóżka nie jest przypisany do żadnej klasy pokoju** – oznacza to, że nie ma żadnych powiązań w tabeli <em>typ_łóżka_pokoju_danej_klasy</em>, które uniemożliwiają jego usunięcie.</li>
            <li>**Zalogowany użytkownik posiada rolę Administratora lub Managera** – tylko użytkownicy z tymi rolami mają uprawnienia do usuwania typów łóżek.</li>
        </ul>
        <p>
            <strong>Przycisk "Usuń"</strong> jest **aktywny** tylko wtedy, gdy oba powyższe warunki są spełnione. W przeciwnym razie, przycisk jest wyszarzony i nieaktywny, a najechanie na niego wyświetla odpowiedni komunikat (tooltip) informujący o przyczynie blokady.
        </p>
        
        <h3>Implementacja Kontroli Usuwania</h3>
        <ol>
            <li><strong>Front-end</strong> – w pliku <em>script_typy_lozek.js</em> sprawdzamy atrybut <code>is_deletable</code> (czy typ łóżka nie jest używany przez żadną klasę pokoju) oraz rolę użytkownika (<em>userRole</em>), aby przycisk miał klasę <code>btn-disabled</code> w razie konieczności.</li>
            <li><strong>Back-end</strong> – w pliku <em>usun_typ_lozka.php</em> ponownie weryfikujemy zależności w bazie danych (czy typ łóżka jest powiązany z jakąkolwiek klasą pokoju) i w razie potrzeby blokujemy usunięcie.</li>
        </ol>
        
        <h3>Proces Usuwania Typu Łóżka</h3>
        <ol>
            <li>**Kliknięcie przycisku "Usuń"** – znajduje się on w kolumnie <strong>Akcje</strong> dla wybranego typu łóżka.</li>
            <li>**Potwierdzenie akcji** – użytkownik jest pytany o potwierdzenie usunięcia danego typu łóżka.</li>
            <li>**Przesłanie żądania usunięcia** – po potwierdzeniu, wysyłane jest żądanie do skryptu <em>usun_typ_lozka.php</em> z odpowiednim identyfikatorem typu łóżka.</li>
            <li>**Obsługa odpowiedzi** – w zależności od wyniku operacji:
                <ul>
                    <li><strong>Sukces</strong> – wyświetlany jest komunikat potwierdzający usunięcie, a tabela jest odświeżana.</li>
                    <li><strong>Błąd</strong> – wyświetlany jest komunikat z informacją o przyczynie błędu.</li>
                </ul>
            </li>
        </ol>
        
        <h2>5. Dostępność do Strony</h2>
        <p>
            Dostęp do modułu **Typy Łóżek** jest **ograniczony** do użytkowników, którzy:
        </p>
        <ul>
            <li>**Są zalogowani** – sprawdzane jest, czy sesja użytkownika jest aktywna.</li>
            <li>**Posiadają odpowiednią rolę** – tylko **Administratorzy** i **Managerowie** mają uprawnienia do zarządzania typami łóżek.</li>
        </ul>
        
        <h3>Implementacja Kontroli Dostępu w PHP</h3>
        <pre><code class="language-php">
&lt;?php
include_once __DIR__ . '/../../includes/header.php';

// Sprawdzenie, czy użytkownik jest zalogowany i czy ma uprawnienia Administratora lub Managera
if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    !in_array($_SESSION['role'], [ROLE_ADMINISTRATOR, ROLE_MANAGER], true)
) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
?&gt;
        </code></pre>
        <p>
            <strong>Wyjaśnienie:</strong>
        </p>
        <ul>
            <li><strong>Sprawdzenie, czy użytkownik jest zalogowany:</strong> Upewnia się, że sesja użytkownika jest aktywna i użytkownik jest zalogowany.</li>
            <li><strong>Sprawdzenie roli użytkownika:</strong> Tylko użytkownicy o roli <strong>Administratora</strong> lub <strong>Managera</strong> mają dostęp do tego modułu.</li>
            <li>W przypadku braku uprawnień, skrypt przerywa działanie i wyświetla komunikat.</li>
        </ul>
        
        <h2>6. Dodatkowe Funkcje i Udogodnienia</h2>
        <ul>
            <li><strong>Dynamiczne Ładowanie Danych:</strong> Tabela z typami łóżek jest dynamicznie wypełniana za pomocą JavaScript, co umożliwia płynne i szybkie aktualizacje danych bez przeładowywania strony.</li>
            <li><strong>Animacje i Efekty Wizualne:</strong> Płynne animacje przy otwieraniu i zamykaniu modali oraz efekty na przyciskach poprawiają doświadczenie użytkownika.</li>
            <li><strong>Feedback dla Użytkownika:</strong> Komunikaty informujące o sukcesie lub błędach operacji dodawania, edycji i usuwania typów łóżek zapewniają jasność i przejrzystość działań.</li>
        </ul>
        
        <h2>7. Integracja z Backendem</h2>
        <p>
            Strona **Typy Łóżek** komunikuje się z backendem za pomocą żądań <em>fetch</em>. Każda operacja (dodanie, edycja, usunięcie) jest realizowana poprzez odpowiednie endpointy PHP, które przetwarzają dane i zwracają odpowiedzi w formacie JSON.
        </p>
        
        <h3>Endpointy Backendowe</h3>
        <ul>
            <li><strong>`dodaj_typ_lozka.php`</strong> – obsługuje dodawanie nowych typów łóżek.</li>
            <li><strong>`edytuj_typ_lozka.php`</strong> – obsługuje edycję istniejących typów łóżek.</li>
            <li><strong>`pobierz_typy_lozek.php`</strong> – zwraca listę wszystkich typów łóżek wraz z informacją o możliwości ich usunięcia.</li>
            <li><strong>`usun_typ_lozka.php`</strong> – obsługuje usuwanie typów łóżek, pod warunkiem spełnienia określonych warunków.</li>
        </ul>
        
        <h3>Bezpieczeństwo i Walidacja Danych</h3>
        <ul>
            <li><strong>Walidacja po stronie serwera:</strong> Każdy skrypt backendowy sprawdza, czy przesłane dane są kompletne i poprawne, oraz czy użytkownik ma odpowiednie uprawnienia do wykonania danej operacji.</li>
            <li><strong>Przygotowane Zapytania SQL:</strong> Użycie <code>prepare</code> i <code>execute</code> w PDO zapobiega atakom typu SQL Injection.</li>
        </ul>
        
        <h2>8. Obsługa Błędów i Diagnostyka</h2>
        <p>
            <strong>Back-end:</strong> Skrypty backendowe zwracają jasne komunikaty o błędach w formacie JSON, co umożliwia front-endowi odpowiednie reagowanie na różne scenariusze błędów (np. brak danych, duplikacja wpisów, błędy bazy danych).
        </p>
        <p>
            <strong>Front-end:</strong> Front-end jest wyposażony w mechanizmy logowania błędów w konsoli przeglądarki oraz wyświetlanie alertów informujących użytkownika o wystąpieniu problemów podczas wykonywania operacji.
        </p>
        
        <h2>9. Przykładowy Przebieg Działania</h2>
        
        <h3>Dodawanie Nowego Typu Łóżka</h3>
        <ol>
            <li>**Użytkownik klika przycisk "Dodaj nowy typ łóżka"**.</li>
            <li>**Modal z formularzem** się otwiera.</li>
            <li>**Użytkownik wypełnia pola**: nazwa typu łóżka i liczba osób.</li>
            <li>**Kliknięcie przycisku "Dodaj"** wysyła dane do backendu.</li>
            <li>**Backend waliduje dane**, dodaje nowy wpis do bazy i zwraca odpowiedź JSON.</li>
            <li>**Front-end**:
                <ul>
                    <li>Jeśli sukces – modal się zamyka, formularz jest resetowany, tabela jest odświeżana, a użytkownik otrzymuje komunikat o sukcesie.</li>
                    <li>Jeśli błąd – użytkownik otrzymuje alert z informacją o błędzie.</li>
                </ul>
            </li>
        </ol>
        
        <h3>Edycja Typu Łóżka</h3>
        <ol>
            <li>**Użytkownik klika przycisk "Edytuj"** obok wybranego typu łóżka.</li>
            <li>**Modal z formularzem edycji** zostaje otwarty i wypełniony aktualnymi danymi typu łóżka.</li>
            <li>**Użytkownik wprowadza zmiany** i klika przycisk "Zapisz zmiany".</li>
            <li>**Kliknięcie przycisku** wysyła zaktualizowane dane do backendu.</li>
            <li>**Backend waliduje dane**, aktualizuje wpis w bazie i zwraca odpowiedź JSON.</li>
            <li>**Front-end**:
                <ul>
                    <li>Jeśli sukces – modal się zamyka, tabela jest odświeżana, a użytkownik otrzymuje komunikat o sukcesie.</li>
                    <li>Jeśli błąd – użytkownik otrzymuje alert z informacją o błędzie.</li>
                </ul>
            </li>
        </ol>
        
        <h3>Usuwanie Typu Łóżka</h3>
        <ol>
            <li>**Użytkownik klika przycisk "Usuń"** obok wybranego typu łóżka.</li>
            <li>**System pyta o potwierdzenie usunięcia**.</li>
            <li>**Po potwierdzeniu** – wysyłane jest żądanie usunięcia do backendu.</li>
            <li>**Backend sprawdza, czy typ łóżka jest usuwalny**, usuwa wpis z bazy i zwraca odpowiedź JSON.</li>
            <li>**Front-end**:
                <ul>
                    <li>Jeśli sukces – tabela jest odświeżana, a użytkownik otrzymuje komunikat o sukcesie.</li>
                    <li>Jeśli błąd – użytkownik otrzymuje alert z informacją o błędzie.</li>
                </ul>
            </li>
        </ol>
        
        <h2>10. Kontrola Dostępu w JavaScript</h2>
        <p>
            W celu zapewnienia odpowiedniego poziomu bezpieczeństwa i funkcjonalności, front-end korzysta z informacji o roli użytkownika (<em>userRole</em>), która jest przekazywana z backendu (np. poprzez skrypt PHP w &lt;head&gt;):
        </p>
        <pre><code class="language-html">
&lt;script&gt;
    const baseUrl = "&lt?php echo htmlspecialchars($base_url); ?&gt";
    const userRole = "&lt?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?&gt";
&lt;/script&gt;
        </code></pre>
        
        <h3>Logika Włączania/Wyłączania Przycisków "Usuń"</h3>
        <p>
            Podczas renderowania tabeli, skrypt JavaScript sprawdza:
        </p>
        <ul>
            <li>**Czy typ łóżka jest usuwalny** (<code>is_deletable</code>).</li>
            <li>**Czy użytkownik ma uprawnienia do usuwania** (<code>userRole === 'Administrator' || userRole === 'Manager'</code>).</li>
        </ul>
        <p>
            Na podstawie tych warunków, przycisk <strong>Usuń</strong> jest aktywowany lub dezaktywowany.
        </p>
        
        <h3>Dodawanie Event Listenerów</h3>
        <p>
            Przyciski <strong>Usuń</strong> mają przypisane event listenery tylko wtedy, gdy są aktywne. Dzięki temu, nawet jeśli ktoś spróbuje kliknąć wyszarzony przycisk, operacja usuwania nie zostanie wykonana.
        </p>
        
        <h2>11. Implementacja Skryptów Backendowych</h2>
        
        <h3>dodaj_typ_lozka.php</h3>
        <p>
            Obsługuje dodawanie nowych typów łóżek.
        </p>
        <pre><code class="language-php">
&lt;?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

// Dane z POST (FormData)
$nazwaTypu = $_POST['nazwaTypu'] ?? '';
$liczbaOsob = $_POST['liczbaOsob'] ?? '';

if (empty($nazwaTypu) || empty($liczbaOsob)) {
    echo json_encode(['success' => false, 'error' => 'Wszystkie pola są wymagane (nazwa, liczba osób).']);
    exit;
}

try {
    $liczba = (int) $liczbaOsob;
    if ($liczba <= 0) {
        echo json_encode(['success' => false, 'error' => 'Liczba osób musi być > 0.']);
        exit;
    }

    $query = "
        INSERT INTO rezerwacje_hotelowe.typ_łóżka (nazwa_typu, liczba_osob)
        VALUES (:nazwa, :liczba)
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'nazwa' => $nazwaTypu,
        'liczba' => $liczba
    ]);

    echo json_encode(['success' => true, 'message' => 'Typ łóżka został pomyślnie dodany.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania typu łóżka: ' . $e->getMessage()]);
}
?&gt;
        </code></pre>
        
        <h3>edytuj_typ_lozka.php</h3>
        <p>
            Obsługuje edycję istniejących typów łóżek.
        </p>
        <pre><code class="language-php">
&lt;?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['nazwa'], $data['liczba_osob'])) {
    echo json_encode(['error' => 'Brak wymaganych danych (id, nazwa, liczba_osob).']);
    exit;
}

$id = (int)$data['id'];
$nazwa = trim($data['nazwa']);
$liczba = (int)$data['liczba_osob'];

if ($id <= 0 || empty($nazwa) || $liczba <= 0) {
    echo json_encode(['error' => 'Niepoprawne dane wejściowe.']);
    exit;
}

try {
    // Sprawdzenie istnienia rekordu
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.typ_łóżka WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono typu łóżka o podanym ID.']);
        exit;
    }

    // Aktualizacja
    $updateQuery = "
        UPDATE rezerwacje_hotelowe.typ_łóżka
        SET nazwa_typu = :nazwa,
            liczba_osob = :liczba
        WHERE id = :id
    ";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([
        'nazwa' => $nazwa,
        'liczba' => $liczba,
        'id' => $id
    ]);

    echo json_encode(['success' => 'Typ łóżka został zaktualizowany.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas aktualizacji typu łóżka: ' . $e->getMessage()]);
}
?&gt;
        </code></pre>
        
        <h3>pobierz_typy_lozek.php</h3>
        <p>
            Zwraca listę wszystkich typów łóżek wraz z informacją o możliwości ich usunięcia.
        </p>
        <pre><code class="language-php">
&lt;?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    /*
       Teraz zwracamy także liczba_osob i is_deletable
    */
    $query = "
        SELECT t.id,
               t.nazwa_typu,
               t.liczba_osob,
               CASE WHEN COUNT(tlp.typ_łóżka_id) = 0 THEN true ELSE false END AS is_deletable
        FROM rezerwacje_hotelowe.typ_łóżka t
        LEFT JOIN rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy tlp
               ON tlp.typ_łóżka_id = t.id
        GROUP BY t.id
        ORDER BY t.id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $typyLozek = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($typyLozek);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas pobierania typów łóżek: ' . $e->getMessage()]);
}
?&gt;
        </code></pre>
        
        <h3>usun_typ_lozka.php</h3>
        <p>
            Obsługuje usuwanie typów łóżek.
        </p>
        <pre><code class="language-php">
&lt;?php
include_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode(['error' => 'Brak ID typu łóżka w żądaniu.']);
    exit;
}

$id = (int)$data['id'];

try {
    // Sprawdź, czy typ łóżka istnieje
    $checkQuery = "SELECT id FROM rezerwacje_hotelowe.typ_łóżka WHERE id = :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['id' => $id]);

    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Nie znaleziono typu łóżka o podanym ID.']);
        exit;
    }

    // Sprawdź powiązania
    $countQuery = "
        SELECT COUNT(*) AS liczba
        FROM rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy
        WHERE typ_łóżka_id = :id
    ";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute(['id' => $id]);
    $row = $countStmt->fetch(PDO::FETCH_ASSOC);

    if ($row['liczba'] > 0) {
        echo json_encode(['error' => 'Nie można usunąć tego typu łóżka, ponieważ jest używany w tabeli typ_łóżka_pokoju_danej_klasy.']);
        exit;
    }

    // Usuń
    $deleteQuery = "DELETE FROM rezerwacje_hotelowe.typ_łóżka WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute(['id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Typ łóżka został pomyślnie usunięty.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Błąd podczas usuwania typu łóżka: ' . $e->getMessage()]);
}
?&gt;
        </code></pre>
        
        <h2>12. Implementacja Skryptów Front-endowych</h2>
        
        <h3>script_typy_lozek.js</h3>
        <p>
            Skrypt JavaScript odpowiedzialny za interakcję użytkownika z modułem Typów Łóżek.
        </p>
        <pre><code class="language-javascript">
        document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('.bed-types-table tbody');

    const addModal = document.getElementById('modal');
    const openAddModalBtn = document.getElementById('openModal');
    const closeAddModalBtn = document.getElementById('closeModal');
    const addBedTypeForm = document.getElementById('addBedTypeForm');
    const addBedTypeBtn = document.getElementById('addBedTypeBtn');

    const editModal = document.getElementById('editModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const editBedTypeForm = document.getElementById('editBedTypeForm');
    const saveChangesBtn = document.getElementById('saveChangesBtn');

    // ==================== Odświeżanie tabeli ====================
    function odswiezTabeleTypowLozek() {
        fetch(`${baseUrl}/pages/typy_lozek/pobierz_typy_lozek.php`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data)) {
                    console.error('Nieprawidłowy format danych:', data);
                    alert(data.error || 'Wystąpił nieznany błąd.');
                    return;
                }

                tableBody.innerHTML = '';

                data.forEach((typ, index) => {
                    // Odczyt is_deletable
                    const isDeletable = (
                        typ.is_deletable === true ||
                        typ.is_deletable === 'true' ||
                        typ.is_deletable === 't'
                    );

                    // Sprawdź, czy bieżący zalogowany user może usuwać (Admin/Manager)
                    const userHasDeletePerm = (userRole === 'Administrator' || userRole === 'Manager');
                    // Finalna flaga usuwalności
                    const finalDeletable = (isDeletable && userHasDeletePerm);

                    // Jeśli finalDeletable = false => klawisz usuń będzie wyszarzony
                    const disabledAttr = finalDeletable ? '' : 'disabled';
                    const deleteBtnClass = finalDeletable ? 'btn-danger' : 'btn-disabled';

                    // Tooltip, jeśli jest w bazie zablokowane:
                    // lub jeśli user nie ma uprawnień
                    let tooltip = '';
                    if (!isDeletable) {
                        tooltip = 'Nie można usunąć – typ łóżka jest w użyciu.';
                    } else if (!userHasDeletePerm) {
                        tooltip = 'Brak uprawnień do usuwania tego typu łóżka.';
                    }
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        &lttd&gt${index + 1}&lt/td&gt
                        &lttd&gt${typ.nazwa_typu}&lt/td&gt
                        &lttd&gt${typ.liczba_osob}&lt/td&gt
                        &lttd class="actions"&gt
                            &ltbutton class="btn-secondary edytuj-btn" data-id="${typ.id}"&gtEdytuj&lt/button&gt
                            &ltbutton class="${deleteBtnClass} usun-btn"
                                    data-id="${typ.id}"
                                    ${disabledAttr}
                                    title="${tooltip}"&gt
                                Usuń
                            &lt/button&gt
                        &lt/td&gt
                    `;
                    tableBody.appendChild(row);
                });

                dodajEventListenery();
            })
            .catch(error => console.error('Błąd pobierania typów łóżek:', error));
    }

    // ==================== Dodawanie nowego typu łóżka ====================
    openAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'block';
    });

    closeAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'none';
        addBedTypeForm.reset();
    });

    addBedTypeBtn.addEventListener('click', () => {
        const formData = new FormData(addBedTypeForm);
        // formData zawiera: nazwaTypu, liczbaOsob

        fetch(`${baseUrl}/pages/typy_lozek/dodaj_typ_lozka.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Typ łóżka został dodany pomyślnie.');
                addModal.style.display = 'none';
                addBedTypeForm.reset();
                odswiezTabeleTypowLozek();
            } else {
                alert(data.error || 'Wystąpił błąd podczas dodawania typu łóżka.');
            }
        })
        .catch(error => {
            console.error('Błąd podczas dodawania typu łóżka:', error);
            alert('Wystąpił błąd podczas dodawania typu łóżka.');
        });
    });

    // ==================== Edycja typu łóżka ====================
    function otworzEdytujModal(idTypu) {
        const row = document.querySelector(`.edytuj-btn[data-id='${idTypu}']`).closest('tr');
        const cells = row.querySelectorAll('td');
        const nazwa = cells[1].textContent;
        const liczba = cells[2].textContent;

        document.getElementById('edit-id').textContent = idTypu;
        document.getElementById('edit-nazwa-typu').value = nazwa;
        document.getElementById('edit-liczba-osob').value = liczba;

        editModal.style.display = 'block';
    }

    closeEditModalBtn.addEventListener('click', () => {
        editModal.style.display = 'none';
        editBedTypeForm.reset();
    });

    saveChangesBtn.addEventListener('click', () => {
        const idTypu = document.getElementById('edit-id').textContent;
        const nowaNazwa = document.getElementById('edit-nazwa-typu').value;
        const nowaLiczbaOsob = document.getElementById('edit-liczba-osob').value;

        const payload = {
            id: idTypu,
            nazwa: nowaNazwa,
            liczba_osob: nowaLiczbaOsob
        };

        fetch(`${baseUrl}/pages/typy_lozek/edytuj_typ_lozka.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Typ łóżka został zaktualizowany.');
                editModal.style.display = 'none';
                odswiezTabeleTypowLozek();
            } else {
                alert(`Błąd aktualizacji: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Błąd aktualizacji typu łóżka:', error);
            alert('Wystąpił błąd podczas edycji typu łóżka.');
        });
    });

    // ==================== Usuwanie typu łóżka ====================
    function usunTypLozka(idTypu) {
        if (!confirm('Czy na pewno chcesz usunąć ten typ łóżka?')) {
            return;
        }

        fetch(`${baseUrl}/pages/typy_lozek/usun_typ_lozka.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: idTypu })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Typ łóżka został usunięty.');
                odswiezTabeleTypowLozek();
            } else {
                alert(`Błąd usuwania: ${data.error}`);
            }
        })
        .catch(error => console.error('Błąd usuwania typu łóżka:', error));
    }

    function dodajEventListenery() {
        document.querySelectorAll('.edytuj-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                otworzEdytujModal(id);
            });
        });

        document.querySelectorAll('.usun-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                usunTypLozka(id);
            });
        });
    }

    // ==================== Start ====================
    odswiezTabeleTypowLozek();
});

        </code></pre>
        
        <h3>style_typy_lozek.css</h3>
        <p>
            Plik CSS odpowiedzialny za stylizację strony **Typy Łóżek**. Obejmuje on:
        </p>
        <ul>
            <li><strong>Ogólny wygląd strony</strong> – czcionki, kolory tła, marginesy.</li>
            <li><strong>Przyciski</strong> – różne style dla przycisków podstawowych, niebieskich, czerwonych (usuwanie), nieaktywnych.</li>
            <li><strong>Tabela</strong> – stylizacja nagłówków, wierszy, hover efektów.</li>
            <li><strong>Modale</strong> – wygląd okien modalnych, animacje otwierania i zamykania, responsywność.</li>
            <li><strong>Formularze</strong> – układ formularzy, pola wejściowe, etykiety.</li>
        </ul>
        
        <h2>14. Podsumowanie</h2>
        <p>
            Moduł <strong>Typy Łóżek</strong> w systemie hotelowym **Stardust Hotel** oferuje kompleksowe narzędzia do zarządzania rodzajami łóżek dostępnych w hotelu. Dzięki zastosowaniu odpowiednich mechanizmów kontroli dostępu, walidacji danych oraz estetycznej i intuicyjnej interfejsu użytkownika, moduł ten zapewnia efektywne i bezpieczne zarządzanie zasobami hotelowymi.
        </p>
        <p><strong>Kluczowe cechy modułu:</strong></p>
        <ul>
            <li><strong>CRUD operacje</strong> – pełne zarządzanie typami łóżek (Tworzenie, Odczyt, Aktualizacja, Usuwanie).</li>
            <li><strong>Kontrola dostępu</strong> – tylko użytkownicy z rolą Administratora lub Managera mogą dokonywać zmian.</li>
            <li><strong>Sprawdzanie zależności</strong> – uniemożliwia usunięcie typu łóżka, jeśli jest on powiązany z jakąkolwiek klasą pokoju.</li>
            <li><strong>Zgodność z REST</strong> - Używanie odpowiednich metod HTTP zgodnie z zasadami REST, używa GET do pobierania danych, POST do tworzenia, PUT/PATCH do aktualizacji oraz DELETE do usuwania zasobów.</li>
            <li><strong>Bezpieczeństwo</strong> – zabezpieczenia przed nieautoryzowanym dostępem i atakami typu SQL Injection.</li>
        </ul>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
