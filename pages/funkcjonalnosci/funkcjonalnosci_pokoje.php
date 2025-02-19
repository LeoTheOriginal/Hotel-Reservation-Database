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
    <title>Funkcjonalność: Pokoje - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Pokoje</h1>

        <p>
            Moduł <strong>Pokoje</strong> pozwala na kompleksowe zarządzanie listą dostępnych pokoi w hotelu. 
            Dzięki temu możemy rejestrować nowe pokoje, zmieniać ich parametry (klasę, status, piętro, numer) 
            oraz usuwać te, które nie są już potrzebne.
        </p>

        <h2>1. Przeglądanie Listy Pokoi</h2>
        <p>
            Na stronie <strong>Lista Pokoi</strong> wyświetlana jest tabela zawierająca informacje o:
        </p>
        <ul>
            <li><strong>Numer</strong> – kolejny numer porządkowy w wyświetlanej liście.</li>
            <li><strong>Numer pokoju</strong> – unikalny identyfikator dla gości (np. „101”).</li>
            <li><strong>Klasa pokoju</strong> – określa rodzaj (np. Standard, Deluxe) i wpływa na cenę podstawową.</li>
            <li><strong>Status pokoju</strong> – (np. Dostępny, Zajęty, W trakcie sprzątania).</li>
            <li><strong>Piętro</strong> – informacja, na którym piętrze znajduje się pokój.</li>
            <li><strong>Maks. liczba osób</strong> – wynik obliczeń opartych na przypisanym typie łóżek do danej klasy pokoju (wylicza się automatycznie w bazie danych).</li>
            <li><strong>Akcje</strong> – sekcja z przyciskami <em>Edytuj</em> i <em>Usuń</em>.</li>
        </ul>

        <p>
            Przy każdym wierszu wyświetlają się przyciski do edycji i usuwania pokoju. 
            Jeśli pokój jest powiązany z bieżącą lub przyszłą rezerwacją, 
            bądź status pokoju wskazuje na inne ograniczenia, przycisk <em>Usuń</em> może być wyszarzony (nieaktywny).
        </p>

        <h2>2. Dodawanie Nowego Pokoju</h2>
        <p>
            Aby dodać nowy pokój:
        </p>
        <ol>
            <li><strong>Kliknij przycisk „Dodaj nowy pokój”</strong> – otworzy się modal (okno dialogowe) z formularzem.</li>
            <li><strong>Wypełnij formularz</strong>:
                <ul>
                    <li><strong>Numer Pokoju</strong> – np. „101”.</li>
                    <li><strong>Piętro</strong> – wybór z listy pięter pobranych z bazy danych.</li>
                    <li><strong>Klasa Pokoju</strong> – wybór z listy istniejących klas (Standard, Deluxe itp.).</li>
                    <li><strong>Status Pokoju</strong> – np. „Dostępny” czy „Zajęty”.</li>
                </ul>
            </li>
            <li><strong>Zatwierdź</strong> – kliknięcie przycisku <em>Dodaj</em> powoduje wysłanie danych do skryptu <code>dodaj_pokoj_action.php</code>, który zapisze nowy rekord w tabeli <em>pokój</em> w bazie danych.</li>
        </ol>
        <p>
            Po udanym dodaniu nowy pokój pojawi się w tabeli pokoi.
        </p>

        <h2>3. Edycja Danych Pokoju</h2>
        <p>
            Aby edytować dane istniejącego pokoju:
        </p>
        <ol>
            <li><strong>Kliknij przycisk „Edytuj”</strong> – w wierszu, który odpowiada danemu pokojowi.</li>
            <li><strong>Otworzy się modal</strong> z formularzem edycji, gdzie można zmienić:
                <ul>
                    <li><strong>Numer pokoju</strong>,</li>
                    <li><strong>Klasę pokoju</strong>,</li>
                    <li><strong>Status pokoju</strong>,</li>
                    <li><strong>Piętro</strong>.</li>
                </ul>
            </li>
            <li><strong>Maks. liczba osób</strong> (jeżeli występuje) jest automatycznie wyliczana w bazie i często prezentowana jako pole tylko do odczytu.</li>
            <li><strong>Zatwierdź zmiany</strong> – kliknięcie przycisku <em>Zatwierdź</em> w oknie dialogowym wysyła dane do <code>zaktualizuj_pokoj.php</code>, który dokonuje aktualizacji rekordu w bazie.</li>
        </ol>

        <h3>Walidacja i Limitacje</h3>
        <p>
            Skrypt <em>zaktualizuj_pokoj.php</em> weryfikuje, czy wszystkie wymagane pola są podane oraz czy dane są spójne. 
            W razie błędów (np. numer pokoju już istnieje) zostanie zwrócony odpowiedni komunikat.
        </p>

        <h2>4. Zasady Usuwania Pokoju</h2>
        <p>
            Funkcjonalność <em>Usuń</em> może być ograniczona przez:
        </p>
        <ul>
            <li><strong>Powiązania z rezerwacjami</strong> – jeżeli w systemie istnieją bieżące lub przyszłe rezerwacje obejmujące dany pokój, 
                nie można go usunąć (przycisk jest wyszarzony).</li>
            <li><strong>Status pokoju</strong> – np. jeśli status to „Zajęty”, może nie być dozwolone usuwanie.</li>
            <li><strong>Uprawnienia użytkownika</strong> – w typowych scenariuszach do usuwania pokoi uprawnienia mają tylko 
                <strong>Administratorzy</strong> i <strong>Managerowie</strong>. 
                (W pliku <em>pokoje.php</em> widzimy jednak, że sprawdzane jest tylko „logged_in”, więc ewentualne 
                ograniczenia ról można wprowadzić analogicznie, np. weryfikując <code>$_SESSION['role']</code>.)
                Recjepcja ma natomiast tylko możliwość przeglądania i edycji pokoi, ale nie usuwania.
            </li>
        </ul>
        <p>
            Przycisk „Usuń” wywołuje skrypt <code>usun_pokoj.php</code>, który finalnie usuwa rekord z tabeli <em>pokój</em>, 
            jeżeli zostały spełnione wszystkie warunki. W przypadku błędu lub braku uprawnień, zostaje zwrócony stosowny komunikat.
        </p>

        <h3>Proces Usuwania Pokoju</h3>
        <ol>
            <li>Kliknięcie <em>Usuń</em> w wierszu pokoju.</li>
            <li>Potwierdzenie w oknie dialogowym (prompt JS).</li>
            <li>Wysłanie żądania do <code>usun_pokoj.php</code> metodą <strong>DELETE</strong> (zgodność z REST), 
                z ID pokoju w treści żądania.</li>
            <li><strong>Sprawdzenie w bazie danych</strong>, czy pokój da się usunąć. Jeśli tak, kasuje rekord; jeśli nie, 
                zwraca komunikat błędu.</li>
            <li>Front-end odświeża tabelę pokoi i pokazuje ewentualny komunikat (sukces/błąd).
            </li>
        </ol>

        <h2>5. Dostęp do Modułu „Pokoje”</h2>
        <p>
            W pliku <code>pokoje.php</code> widzimy następującą kontrolę dostępu:
        </p>
        <pre><code class="language-php">
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
        </code></pre>
        <p>
            Oznacza to, że stronę mogą wyświetlać wyłącznie zalogowani użytkownicy. 
            Jeśli chcemy wprowadzić rozróżnienie na role (np. tylko Administrator, Manager, Recepcjonista), 
            możemy dodać warunek sprawdzający <code>$_SESSION['role']</code>.
        </p>

        <h2>6. Struktura i Komunikacja z Backendem</h2>
        <p>
            Strona **Lista Pokoi** (plik <code>pokoje.php</code>) komunikuje się z backendem za pomocą JavaScript (fetch) 
            w następujących operacjach:
        </p>
        <ul>
            <li><strong>odswiez_tabele_pokoi.php</strong> – pobiera listę pokoi (z informacjami o możliwości usunięcia),</li>
            <li><strong>dodaj_pokoj_action.php</strong> – dodawanie nowego pokoju,</li>
            <li><strong>zaktualizuj_pokoj.php</strong> – edycja (aktualizacja) istniejącego pokoju,</li>
            <li><strong>usun_pokoj.php</strong> – usunięcie pokoju, jeśli to dozwolone.</li>
            <li>Ponadto pliki <em>pobierz_klasy_pokoi.php</em>, <em>pobierz_status_pokoju.php</em>, <em>pobierz_pietra.php</em> zwracają dane referencyjne (listy klas, statusów, pięter), które wypełniają formularze w modalu.</li>
        </ul>

        <h3>Flow Obsługi Żądania (Dodawanie/Edycja/Usuwanie)</h3>
        <ol>
            <li><strong>Front-end</strong> (JavaScript) zbiera dane z formularza lub klikniętego wiersza w tabeli.</li>
            <li>Wysyła żądanie do odpowiedniego skryptu PHP (np. <code>dodaj_pokoj_action.php</code> / <code>zaktualizuj_pokoj.php</code> / <code>usun_pokoj.php</code>) metodą <code>POST</code>, <code>PUT</code>, czy <code>DELETE</code> (zgodność z REST).</li>
            <li><strong>Back-end</strong> (skrypt PHP) wykonuje operacje na bazie danych (INSERT/UPDATE/DELETE), sprawdza warunki i zwraca odpowiedź JSON z kluczami <code>success</code> / <code>error</code>.</li>
            <li><strong>Front-end</strong> odbiera odpowiedź JSON i w zależności od wartości <code>success</code> i ewentualnych komunikatów <code>error</code> pokazuje odpowiedni alert użytkownikowi, a także odświeża tabelę pokoi (w razie sukcesu).</li>
        </ol>

        <h2>7. Wsparcie Animacji i Walidacji</h2>
        <ul>
            <li><strong>Okna modalne</strong> otwierają się płynnie i pozwalają użytkownikowi wprowadzić dane do formularza (np. numer pokoju, piętro). Po kliknięciu w tło lub przycisk „X” – modal się zamyka.</li>
            <li><strong>Weryfikacja pól</strong> – wstępna walidacja jest wykonywana w JavaScript (np. sprawdzenie, czy wartość <em>Numer Pokoju</em> nie jest pusta). Dodatkowo kluczem jest walidacja w PHP (zapobiegająca wstawieniu błędnych danych).</li>
        </ul>

        <h2>8. Przykładowy Scenariusz</h2>

        <h3>Dodawanie Nowego Pokoju</h3>
        <ol>
            <li>Użytkownik klika „Dodaj nowy pokój”.</li>
            <li>Wyświetla się modal z pustym formularzem.</li>
            <li>Użytkownik wybiera Piętro, Klasę Pokoju, Status Pokoju i wpisuje Numer Pokoju.</li>
            <li>Kliknięcie „Dodaj” wysyła dane do <em>dodaj_pokoj_action.php</em>.</li>
            <li>Jeżeli sukces – alert z komunikatem, modal się zamyka, a tabela zostaje odświeżona.</li>
            <li>Jeżeli błąd – użytkownik dostaje komunikat o błędzie (np. numer już istnieje).</li>
        </ol>

        <h3>Edycja Pokoju</h3>
        <ol>
            <li>Użytkownik klika „Edytuj” przy wybranym pokoju.</li>
            <li>Otwiera się modal wypełniony aktualnymi danymi pokoju (np. numer, klasa, status, piętro).</li>
            <li>Użytkownik wprowadza zmiany (np. zmienia klasę z Standard na Deluxe).</li>
            <li>Kliknięcie „Zatwierdź” wysyła dane do <em>zaktualizuj_pokoj.php</em>.</li>
            <li>Jeżeli sukces – alert i odświeżenie tabeli. Jeżeli błąd – alert z przyczyną błędu (np. brak wymaganych pól).</li>
        </ol>

        <h3>Usuwanie Pokoju</h3>
        <ol>
            <li>Jeżeli przycisk „Usuń” nie jest wyszarzony, użytkownik może go kliknąć.</li>
            <li>System pyta w oknie dialogowym: „Czy na pewno chcesz usunąć ten pokój?”.</li>
            <li>Po potwierdzeniu wysyłane jest żądanie <strong>DELETE</strong> do <em>usun_pokoj.php</em> z ID pokoju.</li>
            <li>Skrypt usuwa rekord z bazy (o ile brak rezerwacji powiązanych, status jest odpowiedni, itp.) 
                i zwraca JSON z rezultatem.</li>
            <li>Front-end w razie sukcesu odświeża listę pokoi i wyświetla komunikat. 
                W razie błędu – alert z treścią błędu.</li>
        </ol>

        <h2>9. Podsumowanie</h2>
        <p>
            Moduł <strong>Pokoje</strong> to kluczowa część systemu rezerwacji hotelowych w <em>Stardust Hotel</em>. 
            Pozwala na bieżące zarządzanie zasobami noclegowymi, monitorowanie stanu pokoi (dostępne, zajęte, w trakcie sprzątania) 
            oraz szybkie reagowanie na potrzeby gości. 
        </p>
        <p><strong>Najważniejsze cechy:</strong></p>
        <ul>
            <li>Dodawanie nowych pokoi z przypisaniem do odpowiedniego piętra, klasy i statusu.</li>
            <li>Edycja danych istniejących pokoi (np. zmiana statusu, zmiana klasy).</li>
            <li>Możliwość usuwania pokoi (o ile nie są powiązane z bieżącymi/future rezerwacjami).</li>
            <li>Wyliczanie maksymalnej liczby osób na podstawie logiki w bazie (typy łóżek klas pokoju).</li>
            <li>Prosty mechanizm kontroli dostępu (tylko zalogowani, a w pełnej wersji – restrykcja do roli <em>Admin</em> / <em>Manager</em>).</li>
        </ul>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
