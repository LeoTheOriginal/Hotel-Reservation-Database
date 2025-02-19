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
    <title>Funkcjonalność: Pracownicy - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Pracownicy</h1>

        <p>
            Moduł <strong>Pracownicy</strong> umożliwia kompleksowe zarządzanie zasobami personalnymi w hotelu 
            <em>Stardust Hotel</em>. Dzięki niemu osoby z odpowiednimi uprawnieniami (Administrator lub Manager) 
            mogą:
        </p>
        <ul>
            <li>Dodawać nowych pracowników, definiując imię, nazwisko, login, hasło oraz rolę w systemie,</li>
            <li>Przeglądać listę pracowników w hotelu,</li>
            <li>Edytować dane istniejących pracowników (zmieniać np. nazwisko, login, przypisaną rolę),</li>
            <li>Usuwać pracowników (z pewnymi ograniczeniami),</li>
            <li>Zarządzać hasłem pracowników (w tym zmieniać je na nowe).</li>
        </ul>

        <h2>1. Dostęp do strony i Kontrola Ról</h2>
        <p>
            Dostęp do strony <strong>Pracownicy</strong> mają wyłącznie użytkownicy o rolach:
        </p>
        <ul>
            <li><strong>Administrator</strong></li>
            <li><strong>Manager</strong></li>
        </ul>
        <p>
            Użytkownicy o rolach niższych, np. <em>Recepcjonista</em>, nie mogą wejść na tę stronę. 
            W kodzie jest to zabezpieczone sprawdzeniem sesji i roli:
        </p>
        <pre><code class="language-php">
if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    !in_array($_SESSION['role'], [ROLE_ADMINISTRATOR, ROLE_MANAGER], true)
) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
        </code></pre>

        <h3>Uprawnienia w zależności od roli</h3>
        <ul>
            <li><strong>Administrator</strong> – pełen zakres: może dodawać, edytować i usuwać <strong>wszystkich</strong> pracowników, także innych administratorów. Może też zmieniać hasła.</li>
            <li><strong>Manager</strong> – może również zarządzać pracownikami, jednak:
                <ul>
                    <li>nie może usuwać lub edytować danych osób z rolą <strong>Administrator</strong> (przyciski będą wyszarzone),</li>
                    <li>nie zobaczy przycisku „Zmień hasło” (bądź przycisk będzie wyłączony), jeśli w formularzu edycji jest pracownik o roli Administrator – a nawet jeśli jest wyświetlony, w warstwie JS jest on zablokowany.</li>
                </ul>
            </li>
        </ul>

        <h2>2. Przeglądanie listy pracowników</h2>
        <p>
            W widoku <strong>Lista Pracowników</strong> prezentowana jest tabela zawierająca:
        </p>
        <ul>
            <li><strong>Nr</strong>,</li>
            <li><strong>Imię</strong>,</li>
            <li><strong>Nazwisko</strong>,</li>
            <li><strong>Login</strong>,</li>
            <li><strong>Rola</strong>,</li>
            <li><strong>Akcje</strong> – przyciski <em>Edytuj</em> oraz <em>Usuń</em>.</li>
        </ul>
        <p>
            Tabela jest dynamicznie wypełniana danymi przy pomocy JavaScript, 
            na podstawie żądania do pliku <em>pobierz_pracownikow.php</em>. 
            Po stronie front-endu sprawdzana jest także rola zalogowanego użytkownika, 
            aby w przypadku <strong>Managera</strong> zablokować (wyszarzyć) przyciski edycji i usuwania 
            dla wierszy dotyczących pracowników z rolą <strong>Administrator</strong>.
        </p>

        <h2>3. Dodawanie nowego pracownika</h2>
        <p>
            Aby dodać nowego pracownika, kliknij przycisk <strong>Dodaj nowego pracownika</strong>. 
            Pojawi się modal z formularzem, w którym należy uzupełnić:
        </p>
        <ul>
            <li><strong>Imię</strong> i <strong>Nazwisko</strong>,</li>
            <li><strong>Login</strong> (musi być unikalny w systemie),</li>
            <li><strong>Hasło</strong> – domyślnie hashowane za pomocą MD5,</li>
            <li><strong>Rola</strong> – wybór z listy (np. Administrator, Manager, Recepcjonista).</li>
        </ul>
        <p>
            Po wypełnieniu formularza i wysłaniu danych do pliku <em>dodaj_pracownika.php</em>, 
            następuje walidacja (czy login już nie istnieje, czy pola nie są puste) i jeśli wszystko przebiegnie pomyślnie, 
            nowy pracownik jest zapisywany w bazie danych.
        </p>

        <h2>4. Edycja danych pracownika</h2>
        <p>
            W kolumnie <strong>Akcje</strong> przy każdym wierszu tabeli znajduje się przycisk <strong>Edytuj</strong>. 
            Kliknięcie go otwiera modal z formularzem do edycji:
        </p>
        <ul>
            <li><strong>Imię</strong> i <strong>Nazwisko</strong>,</li>
            <li><strong>Login</strong>,</li>
            <li><strong>Rola</strong>.</li>
        </ul>
        <p>
            Po kliknięciu przycisku <em>Zapisz zmiany</em>, dane są wysyłane metodą <code>PUT</code> do skryptu <em>edytuj_pracownika.php</em>, 
            który dokonuje aktualizacji rekordu w bazie danych. Jeżeli użytkownik 
            jest <strong>Managerem</strong> i próbuje edytować rolę pracownika o roli <strong>Administrator</strong>, 
            to przycisk będzie wyszarzony i edycja się nie powiedzie.
        </p>

        <h3>Zmiana hasła</h3>
        <p>
            W modalu edycji widnieje również przycisk <strong>Zmień hasło</strong>, ale jest on domyślnie wyłączony / wyszarzony dla osób, 
            które nie mają uprawnień (np. <strong>Manager</strong> w wierszu <strong>Administratora</strong>). 
            <br>
            Natomiast zalogowany <strong>Administrator</strong> ma pełny dostęp do zmiany hasła dowolnemu pracownikowi.
        </p>

        <h2>5. Usuwanie pracownika</h2>
        <p>
            Przyciskiem <strong>Usuń</strong> możliwe jest trwałe usunięcie danego pracownika z bazy danych, 
            jednak podlega to pewnym regułom:
        </p>
        <ul>
            <li><strong>Manager</strong> nie może usuwać <strong>Administratorów</strong> (przycisk będzie wyszarzony).</li>
            <li><strong>Administrator</strong> może usuwać wszystkie konta, w tym innych Administratorów.</li>
            <li>W kodzie backendu (<em>usun_pracownika.php</em>) następuje również potwierdzenie, czy istnieje dany pracownik o przekazanym ID.</li>
        </ul>
        <p>
            Po potwierdzeniu w okienku dialogowym <em>(confirm)</em>, jeżeli nie ma żadnych dodatkowych blokad, 
            wykonywana jest metoda <code>DELETE</code> i pracownik zostaje wymazany z bazy.
        </p>

        <h2>6. Bezpieczeństwo i Walidacja</h2>
        <p>
            Operacje w module <strong>Pracownicy</strong> są chronione zarówno na front-endzie, jak i w backendzie:
        </p>
        <ol>
            <li><strong>Front-end:</strong> 
                <ul>
                    <li>Dynamika przycisków – w zależności od roli zalogowanego użytkownika, 
                        przyciski są aktywne lub wyszarzone,</li>
                    <li>Walidacja danych w formularzach (np. czy pola nie są puste).</li>
                </ul>
            </li>
            <li><strong>Backend:</strong>
                <ul>
                    <li>Weryfikacja poprawności danych (np. czy login nie jest już zajęty),</li>
                    <li>Ochrona przed SQL Injection (przygotowane zapytania <code>prepare/execute</code>),</li>
                    <li>Kontrola uprawnień – 
                        w pliku <em>pracownicy.php</em> sprawdza się rolę użytkownika, 
                        a w skryptach typu <em>usun_pracownika.php</em> i <em>edytuj_pracownika.php</em> 
                        również można dodać ewentualne dodatkowe sprawdzenia.</li>
                </ul>
            </li>
        </ol>

        <h2>7. Najważniejsze Pliki i Endpointy</h2>
        <ul>
            <li><strong>pracownicy.php</strong> – główny widok z tabelą pracowników, modale do dodawania/edycji.</li>
            <li><strong>dodaj_pracownika.php</strong> – obsługa dodawania nowego konta pracownika.</li>
            <li><strong>pobierz_pracownikow.php</strong> – zwraca JSON z listą wszystkich pracowników.</li>
            <li><strong>pobierz_pracownika.php</strong> – zwraca JSON z danymi pojedynczego pracownika (do edycji).</li>
            <li><strong>pobierz_role.php</strong> – zwraca JSON z listą ról (Administrator, Manager, Recepcjonista itp.).</li>
            <li><strong>edytuj_pracownika.php</strong> – aktualizacja danych osobowych i roli pracownika.</li>
            <li><strong>usun_pracownika.php</strong> – usuwa konto pracownika.</li>
            <li><strong>zmien_haslo.php</strong> – zmiana hasła pracownika (wymaga starego i nowego hasła).</li>
        </ul>

        <h2>8. Przykładowy Przebieg Dodania Pracownika</h2>
        <ol>
            <li><strong>Użytkownik (Administrator lub Manager) klika "Dodaj nowego pracownika"</strong>.</li>
            <li><strong>Modal z formularzem</strong> pojawia się: wypełnia się imię, nazwisko, login, hasło i wybiera rolę.</li>
            <li>Po kliknięciu przycisku <em>Dodaj</em>, dane przesyłane są metodą <code>POST</code> do <em>dodaj_pracownika.php</em>.</li>
            <li><strong>Backend</strong> waliduje dane (m.in. unikalność loginu). Jeśli poprawne – tworzy nowy rekord w bazie.</li>
            <li><strong>Front-end</strong> odświeża tabelę pracowników i wyświetla komunikat o sukcesie.</li>
        </ol>

        <h2>9. Przykładowy Przebieg Edycji Pracownika</h2>
        <ol>
            <li>W kolumnie <strong>Akcje</strong> kliknięcie przycisku <em>Edytuj</em>.</li>
            <li><strong>Modal edycji</strong> wypełnia się aktualnymi danymi pobranymi z backendu.</li>
            <li>Użytkownik zmienia np. imię, nazwisko, rolę.</li>
            <li><em>Zapisz zmiany</em> – dane są przesyłane metodą <code>PUT</code> do <em>edytuj_pracownika.php</em>.</li>
            <li>Backend aktualizuje rekord w bazie. Front-end odświeża tabelę.</li>
            <li>Jeśli <strong>Manager</strong> próbuje edytować <strong>Administratora</strong>, przycisk będzie wyszarzony i edycja nie nastąpi.</li>
        </ol>

        <h3>Zmiana hasła</h3>
        <ol>
            <li>W modalu edycji pracownika <strong>Administrator</strong> może włączyć przycisk <em>Zmień hasło</em>, 
                co otwiera kolejny modal z prośbą o aktualne i nowe hasło.</li>
            <li><em>Zmień</em> – dane przesyłane metodą <code>PUT</code> do <em>zmien_haslo.php</em>.</li>
            <li>Po pomyślnej weryfikacji starego hasła w bazie nadpisywane jest zahashowane nowe hasło.</li>
        </ol>

        <h2>10. Usuwanie Pracownika</h2>
        <ol>
            <li>Kliknięcie przycisku <em>Usuń</em> w tabeli (o ile nie jest wyszarzony) wyświetla okienko <em>confirm</em>.</li>
            <li>Jeśli potwierdzono, wywoływane jest żądanie <code>DELETE</code> do <em>usun_pracownika.php</em>.</li>
            <li>Backend sprawdza istnienie pracownika i usuwa wpis z tabeli <em>pracownik</em>.</li>
            <li>Front-end odświeża listę, usunięty pracownik znika z tabeli.</li>
        </ol>

        <h2>Podsumowanie</h2>
        <p>
            Moduł <strong>Pracownicy</strong> umożliwia pełne zarządzanie personelem hotelu w systemie rezerwacyjnym 
            <em>Stardust Hotel</em>. Dobrze zdefiniowana logika ról (Administrator vs. Manager) 
            zapewnia odpowiedni poziom bezpieczeństwa i kontroli nad danymi kadrowymi.
        </p>
        <p><strong>Najważniejsze cechy:</strong></p>
        <ul>
            <li>Dodawanie, edycja i usuwanie pracowników przy użyciu intuicyjnych modali,</li>
            <li>Podział uprawnień, który uniemożliwia <strong>Managerowi</strong> modyfikację danych <strong>Administratora</strong>,</li>
            <li>Możliwość zmiany hasła (domyślnie MD5 – do rozważenia przejście na <code>password_hash</code>),</li>
            <li>Bezpieczne metody (<code>GET</code>/<code>POST</code>/<code>PUT</code>/<code>DELETE</code>) i walidacja w kodzie backendowym,</li>
            <li>Przyjazne komunikaty błędów i potwierdzeń (alerty) dla użytkownika.</li>
        </ul>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
