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
    <title>Funkcjonalność: Rezerwacje - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
<div class="funkcjonalnosci-wrapper">
    <h1>Funkcjonalność: Rezerwacje</h1>

    <p>
        Moduł <strong>Rezerwacje</strong> obsługuje pełny cykl życia rezerwacji w systemie Stardust Hotel. 
        Administrator i Manager mają dostęp do wszystkich funkcji zarządzania rezerwacjami w panelu wewnętrznym, 
        a klienci hotelu mogą samodzielnie składać rezerwacje przez stronę publiczną.
    </p>

    <h2>1. Przeglądanie Listy Rezerwacji</h2>
    <p>
        W widoku <strong>Lista Rezerwacji</strong> prezentowana jest tabela zawierająca podstawowe informacje o każdej 
        rezerwacji, m.in.:
    </p>
    <ul>
        <li><strong>ID rezerwacji</strong>,</li>
        <li><strong>Gość</strong> (imię i nazwisko),</li>
        <li><strong>Pokój</strong> (numer pokoju),</li>
        <li><strong>Daty pobytu</strong> (zameldowania i wymeldowania),</li>
        <li><strong>Status rezerwacji</strong> (np. Oczekująca, W trakcie, Zrealizowana, Anulowana),</li>
        <li><strong>Status płatności</strong> (np. Oczekująca, Zrealizowana),</li>
        <li><strong>Kwota</strong> końcowa rezerwacji (uwzględniając m.in. klasę pokoju i dodatki).</li>
    </ul>
    <p>
        W kolumnie <em>Akcje</em> dostępne są przyciski umożliwiające <strong>edycję</strong>, 
        <strong>wykwaterowanie</strong> (jeśli dotyczy) oraz <strong>usunięcie</strong> rezerwacji 
        (pod warunkiem spełnienia określonych warunków).
    </p>

    <h2>2. Dodawanie Nowej Rezerwacji w Panelu Wewnętrznym</h2>
    <p>
        Aby dodać rezerwację, użytkownik (Administrator lub Manager, ewentualnie Recepcjonista) klika przycisk 
        <strong>Dodaj nową rezerwację</strong>. Otwiera się modal z formularzem zawierającym m.in.:
    </p>
    <ul>
        <li><strong>Wybór istniejącego gościa</strong> lub <strong>dodanie nowego gościa</strong> (łącznie z uzupełnieniem danych kontaktowych).</li>
        <li><strong>Liczbę dorosłych</strong> i <strong>dzieci</strong>.</li>
        <li>Daty zameldowania i wymeldowania.</li>
        <li><strong>Klasę pokoju</strong>, a następnie wybór konkretnego pokoju z listy dostępnych (wyfiltrowane na podstawie klasy i terminów).</li>
        <li>Możliwość zaznaczenia <strong>dodatków</strong> (np. śniadanie, parking, SPA).</li>
        <li>Pole <strong>Kwota</strong> obliczane dynamicznie dzięki funkcji 
            <code>oblicz_koszt_rezerwacji_z_parametrami</code> w bazie danych.</li>
    </ul>
    <p>
        Po wypełnieniu formularza i zatwierdzeniu (przycisk <em>Dodaj</em>), system wywołuje odpowiedni skrypt:
    </p>
    <ul>
        <li><strong>dodaj_rezerwacje.php</strong> – gdy wybieramy istniejącego gościa,</li>
        <li><strong>dodaj_goscia_oraz_rezerwacje.php</strong> – gdy najpierw tworzymy nowego gościa, a następnie rezerwację.</li>
    </ul>
    <p>
        Przy pomyślnym dodaniu wyświetla się komunikat sukcesu, a tabela rezerwacji zostaje odświeżona.
    </p>

    <h2>3. Dodawanie Rezerwacji przez Stronę Publiczną</h2>
    <p>
        Dla klientów hotelu dostępna jest osobna strona <strong>rezerwacje_public.php</strong>, 
        gdzie goście bez logowania mogą dokonać rezerwacji:
    </p>
    <ul>
        <li>Uzupełniając dane osobowe (imię, nazwisko, telefon, e-mail),</li>
        <li>Wybierając daty pobytu, klasę pokoju, opcjonalne dodatki,</li>
        <li>System sprawdza dostępność i pozwala wskazać konkretny pokój,</li>
        <li>Następnie wywoływany jest skrypt <strong>dodaj_rezerwacje_public.php</strong>, który tworzy 
            gościa (jeśli jeszcze nie istnieje) i rezerwację.</li>
    </ul>

    <h2>4. Edycja Rezerwacji</h2>
    <p>
        Każda rezerwacja może być <strong>edytowana</strong> poprzez przycisk <em>Edytuj</em> w kolumnie Akcje:
    </p>
    <ul>
        <li>Po kliknięciu otwiera się modal z formularzem, w którym można zmienić daty pobytu, wybrać inny pokój 
            (jeśli jest dostępny), zmienić gościa, statusy (rezerwacji i płatności), a także zestaw dodatków.</li>
        <li>Koszt rezerwacji aktualizuje się dynamicznie dzięki skryptowi <strong>oblicz_koszty_edit.php</strong>, 
            korzystającemu z tej samej funkcji SQL co przy dodawaniu.</li>
        <li>Wysyłanie zmian do bazy odbywa się przez skrypt <strong>zaktualizuj_rezerwacje.php</strong>, który 
            internie wywołuje procedurę <code>zaktualizuj_rezerwacje</code> w bazie danych PostgreSQL.</li>
    </ul>

    <h2>5. Wykwaterowanie</h2>
    <p>
        Rezerwacja o statusie <strong>"W trakcie"</strong> i z opłaconą płatnością (<strong>"Zrealizowana"</strong>) 
        może zostać <em>wykwaterowana</em>, co w interfejsie jest reprezentowane przyciskiem <strong>Wykwateruj</strong>. 
        Po kliknięciu:
    </p>
    <ul>
        <li>System wywołuje funkcję <code>wykwateruj_rezerwacje</code> w bazie danych, ustawiając status rezerwacji na "Zrealizowana" 
            i zmieniając status pokoju na "Dostępny".</li>
        <li>Przycisk wykwaterowania jest aktywny tylko wtedy, gdy obydwa warunki są spełnione (status rezerwacji to "W trakcie" oraz status płatności to "Zrealizowana").</li>
        <li>Jeśli warunki nie są spełnione, przycisk jest wyszarzony.</li>
    </ul>

    <h2>6. Usuwanie Rezerwacji</h2>
    <p>
        Funkcja <strong>Usuń rezerwację</strong> jest dostępna tylko wtedy, gdy do daty zameldowania zostało co najmniej 31 dni. 
        Mechanizm ten zapobiega bezpośredniemu usunięciu rezerwacji "w ostatniej chwili". 
        Logika działa na dwóch poziomach:
    </p>
    <ol>
        <li><strong>Front-end</strong> – przycisk <em>Usuń</em> jest aktywny tylko, jeżeli obliczona różnica dni między 
            bieżącą datą a datą zameldowania przekracza 30.</li>
        <li><strong>Back-end</strong> – 
            <code>usun_rezerwacje</code> (w pliku <em>usun_rezerwacje.php</em> i w funkcji PostgreSQL) 
            również weryfikuje różnicę dat i blokuje usunięcie, jeśli do zameldowania < 31 dni.</li>
    </ol>
    <p>
        Po potwierdzeniu usunięcia (okno dialogowe) wykonywane jest polecenie <strong>DELETE</strong> 
        na rezerwacji oraz na powiązaniach (np. rezerwacja_pokój, rezerwacja_dodatek). 
        Ostatecznie tabela rezerwacji w interfejsie jest odświeżana.
    </p>

    <h2>7. Obliczanie Kosztów</h2>
    <p>
        Zarówno podczas dodawania, jak i edycji rezerwacji, w tle wykorzystywana jest funkcja SQL 
        <code>oblicz_koszt_rezerwacji_z_parametrami</code>:
    </p>
    <ul>
        <li><strong>Wysyłane dane</strong>: ID wybranych pokoi, daty (zameldowania i wymeldowania), ewentualne dodatki.</li>
        <li><strong>Zwracany wynik</strong>: Całkowity koszt obliczony na bazie ceny podstawowej klasy pokoju, liczby dni i wybranych dodatków (oraz ewentualnie innych parametrów).</li>
        <li>
            Front-end, korzystając z endpointów 
            <code>oblicz_koszty.php</code> (w modalu dodawania) 
            i <code>oblicz_koszty_edit.php</code> (w modalu edycji), 
            odświeża pole <em>Kwota</em> po każdej zmianie w wyborze pokoi/dodatków.
        </li>
    </ul>

    <h2>8. Public vs. Wewnętrzny Panel</h2>
    <p>
        System rezerwacji składa się z dwóch głównych widoków:
    </p>
    <ul>
        <li>
            <strong>Panel Wewnętrzny (rezerwacje.php)</strong> – przeznaczony dla pracowników hotelu 
            (z rolą Administrator, Manager, lub ewentualnie Recepcjonista). 
            Oferuje pełny zakres funkcji CRUD, edycje, usuwanie rezerwacji, zmiany statusów, 
            obliczanie kosztów, podgląd wszystkich rezerwacji.
        </li>
        <li>
            <strong>Strona Publiczna (rezerwacje_public.php)</strong> – pozwala gościom hotelu dokonać samodzielnej rezerwacji 
            bez konieczności logowania. Po stronie backendu stosowane są funkcje 
            <code>dodaj_rezerwacje_przez_goscia_public</code> w PostgreSQL, 
            automatycznie zakładająca gościa i łącząca go z nową rezerwacją.
        </li>
    </ul>
    <p>
        Wszelkie rezerwacje, niezależnie od tego, czy zostały złożone publicznie czy przez panel, 
        pojawiają się wspólnie na liście w panelu wewnętrznym, skąd można nimi dalej zarządzać.
    </p>

    <h2>9. Podsumowanie</h2>
    <p>
        Moduł <strong>Rezerwacje</strong> w hotelu Stardust obsługuje wszystkie etapy cyklu życia rezerwacji. 
        Najważniejsze funkcjonalności to:
    </p>
    <ul>
        <li><strong>Dodawanie rezerwacji</strong> dla istniejącego lub nowego gościa (w panelu) bądź przez stronę publiczną.</li>
        <li><strong>Edycja rezerwacji</strong> – zmiana pokoi, dat, dodatków, gościa i statusów.</li>
        <li><strong>Usuwanie rezerwacji</strong> – z ograniczeniem czasowym (minimum 31 dni przed datą zameldowania).</li>
        <li><strong>Wykwaterowanie</strong> – dla rezerwacji w trakcie, które zostały opłacone (status płatności „Zrealizowana”).</li>
        <li><strong>Dynamiczne obliczanie kosztów</strong> – za pomocą zapytań do funkcji PostgreSQL w trakcie tworzenia/edycji.</li>
        <li><strong>Sprawdzanie dostępności pokoi</strong> – zarówno w panelu, jak i w formularzu publicznym, w oparciu o okres pobytu i wybraną klasę pokoju.</li>
    </ul>
    <p>
        Całość opiera się o szereg plików backendowych (m.in. <em>dodaj_rezerwacje.php, dodaj_goscia_oraz_rezerwacje.php, 
        pobierz_szczegoly_rezerwacji.php</em>, etc.) i metod w bazie PostgreSQL (funkcje <em>dodaj_rezerwacje, 
        zaktualizuj_rezerwacje, wykwateruj_rezerwacje, usun_rezerwacje</em>).
        Front-end zapewnia responsywny interfejs, dynamiczne ładowanie danych przez <code>fetch</code>, 
        prezentację tabeli, modale do dodawania/edycji i reagowanie na akcje użytkowników.
    </p>
</div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
