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
    <title>Funkcjonalność: Goście - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Goście</h1>
        
        <p>
            Moduł <strong>Goście</strong> odpowiada za zarządzanie informacjami o gościach hotelu. Poniżej przedstawiono szczegółowy opis funkcjonalności tego modułu oraz podstawowe zasady usuwania rekordów gościa z bazy danych.
        </p>

        <h2>1. Przeglądanie listy gości</h2>
        <p>
            W widoku <strong>Lista Gości</strong> wyświetlana jest tabela zawierająca następujące informacje o każdym gościu:
        </p>
        <ul>
            <li><strong>ID</strong> – unikalny identyfikator gościa w bazie danych,</li>
            <li><strong>Imię</strong> i <strong>Nazwisko</strong>,</li>
            <li><strong>Numer telefonu</strong>,</li>
            <li><strong>Adres e-mail</strong>.</li>
        </ul>
        <p>
            Przy każdym wierszu (rekordzie) wyświetlane są także przyciski akcji, np. <em>Edytuj</em> i <em>Usuń</em>. 
            <br><strong>Uwaga:</strong> Przycisk <em>Usuń</em> jest aktywny wyłącznie dla 
            Administratora i Managera — w innym przypadku będzie wyszarzony (zablokowany).
        </p>

        <h2>2. Dodawanie nowego gościa</h2>
        <p>
            Aby dodać nowego gościa, kliknij przycisk <strong>Dodaj nowego gościa</strong>. Pojawi się modal z formularzem, w którym należy uzupełnić:
        </p>
        <ul>
            <li>Imię i nazwisko,</li>
            <li>Prefiks numeru telefonu oraz właściwy numer telefonu,</li>
            <li>Adres e-mail.</li>
        </ul>
        <p>
            Po wypełnieniu formularza i zatwierdzeniu, rekord o nowym gościu zostaje zapisany w bazie danych.
        </p>

        <h2>3. Edycja danych gościa</h2>
        <p>
            Kliknięcie przycisku <strong>Edytuj</strong> w wierszu odpowiadającym danemu gościowi wyświetla formularz edycji. 
            Administrator (lub inna osoba z odpowiednimi uprawnieniami) może zmienić np. telefon lub adres e-mail i zatwierdzić zmiany. 
            Po ich zapisaniu w bazie danych tabela zostaje automatycznie odświeżona.
        </p>

        <h2>4. Zasady usuwania rekordu gościa</h2>
        <p>
            Funkcja <strong>Usuń gościa</strong> pozwala na trwałe usunięcie rekordu z bazy danych. Jednak z punktu widzenia logiki biznesowej i spójności danych, 
            w typowej implementacji należy uwzględnić następujące ograniczenia:
        </p>
        <ul>
            <li>
                <strong>Można usuwać gościa</strong>, jeśli:
                <ul>
                    <li>nie jest on powiązany z żadnymi przeszłymi, bieżącymi lub przyszłymi rezerwacjami (czyli gość nie ma aktywnych, niezakończonych rezerwacji),</li>
                    <li>nie jest stroną w jakimś innym istotnym powiązaniu w bazie (np. nie jest wystawcą faktury, jeśli system by to przewidywał).</li>
                </ul>
            </li>
            <li>
                <strong>Nie można usuwać gościa</strong>, jeśli:
                <ul>
                    <li>posiada aktywne rezerwacje w przyszłości (rekord jest powiązany z rezerwacją, której data zameldowania jeszcze nie nadeszła),</li>
                    <li>system przewiduje archiwizację danych i blokuje usuwanie rekordów gości, którzy mają historię rezerwacji (w niektórych hotelach obowiązuje dłuższa retencja danych i taka operacja jest zabroniona).</li>
                </ul>
            </li>
        </ul>
        <p>
            W praktyce kontrolę nad tym sprawują reguły w bazie danych lub wewnętrzna logika aplikacji (sprawdzanie, czy gość nie ma żadnych rezerwacji).
            <br>Dodatkowo w naszej implementacji przycisk <em>Usuń</em> będzie aktywny wyłącznie dla 
            Administratora i Managera, co dodatkowo ogranicza wykonywanie tej operacji.
        </p>

        <h2>Podsumowanie</h2>
        <p>
            Moduł <strong>Goście</strong> jest kluczowym elementem systemu rezerwacji, pozwalającym na:
        </p>
        <ul>
            <li>Przechowywanie i aktualizację danych osobowych klientów hotelu,</li>
            <li>Wgląd w listę gości i szybkie wyszukiwanie,</li>
            <li>Dodawanie nowych rekordów i edycję istniejących,</li>
            <li>Usuwanie gości, przy uwzględnieniu ograniczeń wynikających z aktywnych rezerwacji i polityki retencji danych.</li>
        </ul>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
