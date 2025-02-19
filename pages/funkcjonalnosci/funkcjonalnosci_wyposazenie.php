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
    <title>Funkcjonalność: Wyposażenie - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Wyposażenie</h1>

        <p>
            W module <strong>Wyposażenie</strong> zarządza się listą dostępnych elementów wyposażenia, 
            które mogą być przypisywane do pokoi (lub klas pokoi) w hotelu <em>Stardust Hotel</em>.
            Przykładami mogą być: Telewizor, Minibar, Klimatyzacja, Wi-Fi i wiele innych.
        </p>

        <h2>1. Przeglądanie i zarządzanie wyposażeniem</h2>
        <p>
            Na stronie <strong>Wyposażenie</strong> widoczna jest tabela zawierająca:
        </p>
        <ul>
            <li><strong>#</strong> – kolejny numer wiersza (dla ułatwienia przeglądania),</li>
            <li><strong>Nazwa wyposażenia</strong> – opis/nazwa elementu (np. Telewizor),</li>
            <li><strong>Akcje</strong> – dostępne przyciski <em>Edytuj</em> i <em>Usuń</em> (w zależności od tego, czy dane wyposażenie jest używane przez jakąkolwiek klasę pokoju).</li>
        </ul>
        <p>
            Tabela jest ładowana dynamicznie przez JavaScript, który pobiera dane z pliku 
            <em>pobierz_wyposazenie.php</em>. Ten zwraca JSON z informacją <code>is_deletable</code>, 
            mówiącą, czy dany element wyposażenia może zostać usunięty.
        </p>

        <h2>2. Dodawanie nowego wyposażenia</h2>
        <p>
            Aby dodać nowy element wyposażenia, kliknij przycisk <strong>Dodaj nowe wyposażenie</strong>. 
            W modalu należy wypełnić:
        </p>
        <ul>
            <li><strong>Nazwa wyposażenia</strong> – np. „Telewizor”, „Klimatyzacja” itp.</li>
        </ul>
        <p>
            Po zatwierdzeniu dane są przesyłane metodą <code>POST</code> do <em>dodaj_wyposazenie.php</em>, 
            gdzie następuje walidacja (sprawdzenie, czy pole nie jest puste) i wpis zostaje zapisany w bazie danych. 
            Jeśli wszystko przebiegnie pomyślnie, tabela jest odświeżana.
        </p>

        <h2>3. Edycja istniejącego elementu wyposażenia</h2>
        <p>
            W tabeli przy każdym wierszu widoczny jest przycisk <strong>Edytuj</strong>, 
            który otwiera modal z formularzem:
        </p>
        <ul>
            <li><strong>ID wyposażenia</strong> – pole niewybrane do edycji (tylko do wglądu),</li>
            <li><strong>Nazwa wyposażenia</strong> – możliwa do zmiany i zatwierdzenia.</li>
        </ul>
        <p>
            Zmienione dane przesyłane są metodą <code>PUT</code> do skryptu <em>edytuj_wyposazenie.php</em>, 
            który aktualizuje rekord w bazie danych.
        </p>

        <h2>4. Usuwanie wyposażenia</h2>
        <p>
            Jeśli w kolumnie Akcje przy wyposażeniu dostępny jest przycisk <strong>Usuń</strong>, 
            to znaczy, że <strong>is_deletable = true</strong> – element nie jest powiązany z żadną klasą pokoju. 
            Można go wtedy usunąć. Jeśli jest wyszarzony / wyłączony, to dany element jest wciąż w użyciu 
            i nie wolno go usuwać.
        </p>
        <p>
            Po kliknięciu przycisku <em>Usuń</em> i potwierdzeniu w oknie dialogowym, wywoływana jest 
            metoda <code>DELETE</code> na endpoint <em>usun_wyposazenie.php</em>. 
            Tam skrypt weryfikuje, czy wciąż brak powiązań w tabeli 
            <em>wyposażenie_pokoju_danej_klasy</em> i jeśli wszystko jest w porządku, 
            usuwa rekord z bazy.
        </p>

        <h2>5. Kontrola uprawnień</h2>
        <p>
            Zależnie od systemu ról (np. <strong>Administrator</strong>, <strong>Manager</strong>), 
            wywołanie plików w module <em>Wyposażenie</em> może być ograniczone. 
            W pliku <em>wyposazenie.php</em> sprawdzane jest, czy użytkownik w ogóle ma prawo do zarządzania 
            takimi danymi. Kod przykładowo weryfikuje <pre><code>$_SESSION['logged_in']</code></pre> i ewentualnie 
            <pre><code>$_SESSION['role']</code></pre>.
        </p>
        <p>
            Wewnątrz skryptów (np. <em>usun_wyposazenie.php</em>) niekiedy można ponownie sprawdzić, 
            czy użytkownik ma uprawnienia do usunięcia (jeśli nie, można zwrócić błąd 
            <em>"Brak uprawnień"</em>).
        </p>

        <h2>6. Najważniejsze endpointy</h2>
        <ul>
            <li><strong>pobierz_wyposazenie.php</strong> – zwraca JSON z listą wyposażenia oraz flagą <em>is_deletable</em>.</li>
            <li><strong>dodaj_wyposazenie.php</strong> – obsługuje dodawanie nowego wyposażenia (<code>POST</code>).</li>
            <li><strong>edytuj_wyposazenie.php</strong> – aktualizuje nazwę istniejącego wyposażenia (<code>PUT</code>).</li>
            <li><strong>usun_wyposazenie.php</strong> – usuwa wyposażenie, o ile nie jest w użyciu (<code>DELETE</code>).</li>
        </ul>

        <h2>7. Przykładowy scenariusz dodania wyposażenia</h2>
        <ol>
            <li><strong>Użytkownik</strong> (posiadający uprawnienia) klika przycisk "Dodaj nowe wyposażenie".</li>
            <li><strong>Modal</strong> się pojawia – w polu <em>Nazwa wyposażenia</em> wpisuje np. "Sauna".</li>
            <li>Po wciśnięciu <em>Dodaj</em> dane lecą metodą <code>POST</code> do <em>dodaj_wyposazenie.php</em>.</li>
            <li><strong>Backend</strong> sprawdza poprawność danych i jeśli wszystko OK – zapisuje w bazie. Zwraca JSON z sukcesem.</li>
            <li><strong>Front-end</strong> odświeża listę wyposażenia, by pokazać nowy element.</li>
        </ol>

        <h2>8. Przykładowy scenariusz edycji wyposażenia</h2>
        <ol>
            <li><em>Edytuj</em> w danym wierszu – modal wypełnia się aktualną nazwą (pobieraną z wiersza w tabeli lub ewentualnie z backendu).</li>
            <li>Użytkownik zmienia nazwę np. z "Sauna" na "Sauna Infrared".</li>
            <li><em>Zapisz zmiany</em> – skrypt <em>edytuj_wyposazenie.php</em> metodą <code>PUT</code> aktualizuje rekord w bazie.</li>
            <li>Tabela jest odświeżana i nowa nazwa pojawia się w wierszu.</li>
        </ol>

        <h2>9. Przykładowy scenariusz usunięcia wyposażenia</h2>
        <ol>
            <li>W kolumnie <em>Akcje</em> jest przycisk <em>Usuń</em> tylko, jeśli <em>is_deletable = true</em> (tzn. wyposażenie nie jest w użyciu).</li>
            <li>Po kliknięciu i potwierdzeniu w <code>confirm</code>, front-end wywołuje <em>usun_wyposazenie.php</em> metodą <code>DELETE</code> z ID danego wyposażenia.</li>
            <li><strong>Backend</strong> ponownie weryfikuje, czy wciąż brak powiązań w <em>wyposażenie_pokoju_danej_klasy</em>. Jeśli tak – usuwa rekord z tabeli <em>wyposażenie</em>.</li>
            <li>Front-end, po sukcesie, wyświetla komunikat i odświeża tabelę.</li>
        </ol>

        <h2>Podsumowanie</h2>
        <p>
            Moduł <strong>Wyposażenie</strong> w systemie <em>Stardust Hotel</em> pozwala na elastyczne zarządzanie 
            wszystkim, co może się znajdować w pokojach lub zostać zaoferowane gościom w ramach komfortu pobytu. 
            Dzięki intuicyjnym formularzom w modalach i jasnej logice is_deletable, 
            nieusuwalne stają się tylko te elementy, które faktycznie są jeszcze wykorzystywane w hotelu.
        </p>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
