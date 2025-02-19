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
    <title>Funkcjonalność: Dodatki - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style_funkcjonalnosci.css">
</head>
<body>
    <div class="funkcjonalnosci-wrapper">
        <h1>Funkcjonalność: Dodatki</h1>

        <p>
            Moduł <strong>Dodatki</strong> pozwala na zarządzanie dodatkowymi usługami lub produktami oferowanymi gościom hotelu 
            <em>Stardust Hotel</em>, takimi jak śniadania, spa, transport, wycieczki itp. Dzięki temu Administratorzy i Managerowie mogą 
            łatwo dodawać, edytować, usuwać oraz przeglądać dostępne dodatki, co pozwala na elastyczne dostosowywanie oferty hotelu do potrzeb gości.
        </p>

        <h2>1. Dostęp do strony i Kontrola Ról</h2>
        <p>
            Dostęp do modułu <strong>Dodatki</strong> mają wyłącznie użytkownicy zalogowani.
        </p>
        <p>
           Kontrola dostępu jest realizowana poprzez sprawdzenie sesji użytkownika:
        </p>
        <pre><code class="language-php">
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
        </code></pre>

        <h2>2. Przeglądanie listy dodatków</h2>
        <p>
            Na stronie <strong>Dodatki</strong> znajduje się tabela prezentująca listę wszystkich dostępnych dodatków:
        </p>
        <ul>
            <li><strong>#</strong> – kolejny numer wiersza (dla lepszej czytelności),</li>
            <li><strong>Nazwa dodatku</strong> – opis/nazwa dodatku (np. „Śniadanie Continental”),</li>
            <li><strong>Cena (PLN)</strong> – koszt dodatku w polskich złotych,</li>
            <li><strong>Akcje</strong> – przyciski <em>Edytuj</em> i <em>Usuń</em>.</li>
        </ul>
        <p>
            Tabela jest dynamicznie wypełniana danymi za pomocą JavaScript, który pobiera dane z pliku <em>pobierz_dodatki.php</em>. 
            Dodatkowo, przyciski <em>Usuń</em> są wyłączone (wyszarzone) dla dodatków, które są obecnie używane w rezerwacjach (oznaczone flagą <code>is_deletable</code>).
        </p>

        <h2>3. Dodawanie nowego dodatku</h2>
        <p>
            Aby dodać nowy dodatek, kliknij przycisk <strong>Dodaj nowy dodatek</strong>. 
            Pojawi się modal z formularzem, w którym należy uzupełnić:
        </p>
        <ul>
            <li><strong>Nazwa dodatku</strong> – np. „Spa Day”, „Transport na lotnisko”.</li>
            <li><strong>Cena (PLN)</strong> – koszt dodatku, może zawierać wartości dziesiętne.</li>
        </ul>
        <p>
            Po wypełnieniu formularza i kliknięciu przycisku <em>Dodaj</em>, dane są przesyłane metodą <code>POST</code> do pliku <em>dodaj_dodatek.php</em>, 
            gdzie następuje walidacja (sprawdzenie, czy pola nie są puste) oraz zapis danych w bazie. 
            Jeśli operacja się powiedzie, tabela zostaje odświeżona, a użytkownik otrzymuje komunikat o sukcesie.
        </p>

        <h2>4. Edycja istniejącego dodatku</h2>
        <p>
            W tabeli przy każdym dodatku znajduje się przycisk <strong>Edytuj</strong>. 
            Kliknięcie go otwiera modal z formularzem, w którym można zmienić:
        </p>
        <ul>
            <li><strong>ID dodatku</strong> – pole niewybrane do edycji, tylko do podglądu.</li>
            <li><strong>Nazwa dodatku</strong> – możliwa do zmiany.</li>
            <li><strong>Cena (PLN)</strong> – możliwa do zmiany.</li>
        </ul>
        <p>
            Po wprowadzeniu zmian i kliknięciu przycisku <em>Zapisz zmiany</em>, dane są przesyłane metodą <code>PUT</code> do pliku <em>edytuj_dodatek.php</em>, 
            który aktualizuje odpowiedni rekord w bazie danych. Jeśli operacja się powiedzie, tabela zostaje odświeżona, a użytkownik otrzymuje komunikat o sukcesie.
        </p>

        <h2>5. Usuwanie dodatku</h2>
        <p>
            Przyciski <strong>Usuń</strong> są dostępne tylko dla dodatków, które nie są obecnie używane w rezerwacjach (oznaczone <code>is_deletable = true</code>). 
            Dla takich dodatków przycisk jest aktywny, natomiast dla pozostałych jest wyłączony (wyszarzony) i nie można go kliknąć.
        </p>
        <p>
            Po kliknięciu przycisku <em>Usuń</em> wyświetla się okno dialogowe <em>confirm</em>. 
            Jeśli użytkownik potwierdzi, następuje wywołanie metody <code>DELETE</code> na endpoint <em>usun_dodatek.php</em>, 
            który sprawdza, czy dodatek jest nadal niepowiązany z żadną rezerwacją. 
            Jeśli tak, rekord jest usuwany z bazy danych, a tabela zostaje odświeżona. W przeciwnym razie użytkownik otrzymuje komunikat o błędzie.
        </p>

        <h2>6. Bezpieczeństwo i Walidacja</h2>
        <p>
            Operacje w module <strong>Dodatki</strong> są chronione zarówno na front-endzie, jak i w backendzie:
        </p>
        <ol>
            <li><strong>Front-end:</strong> 
                <ul>
                    <li>Kontrola dostępności przycisków <em>Usuń</em> w zależności od flagi <code>is_deletable</code>.</li>
                    <li>Walidacja danych w formularzach (np. czy pola nie są puste, czy cena jest prawidłowa).</li>
                </ul>
            </li>
            <li><strong>Backend:</strong>
                <ul>
                    <li>Walidacja poprawności danych (np. czy pola nie są puste, czy cena jest liczbową wartością).</li>
                    <li>Ochrona przed SQL Injection poprzez użycie przygotowanych zapytań <code>prepare/execute</code>.</li>
                    <li>Kontrola uprawnień – w plikach takich jak <em>dodaj_dodatek.php</em>, <em>edytuj_dodatek.php</em>, 
                        <em>usun_dodatek.php</em> dodatkowo można sprawdzić, czy użytkownik ma odpowiednie uprawnienia do wykonania danej operacji.</li>
                </ul>
            </li>
        </ol>

        <h2>7. Najważniejsze Pliki i Endpointy</h2>
        <ul>
            <li><strong>dodatki.php</strong> – główny widok z tabelą dodatków oraz modale do dodawania i edycji.</li>
            <li><strong>dodaj_dodatek.php</strong> – obsługuje dodawanie nowego dodatku do bazy danych (<code>POST</code>).</li>
            <li><strong>pobierz_dodatki.php</strong> – zwraca JSON z listą wszystkich dodatków oraz flagą <em>is_deletable</em>.</li>
            <li><strong>edytuj_dodatek.php</strong> – aktualizuje dane istniejącego dodatku w bazie danych (<code>PUT</code>).</li>
            <li><strong>usun_dodatek.php</strong> – usuwa dodatek, jeśli nie jest powiązany z żadną rezerwacją (<code>DELETE</code>).</li>
            <li><strong>script_dodatki.js</strong> – obsługuje interakcje front-endu, takie jak dodawanie, edycja i usuwanie dodatków.</li>
            <li><strong>style_dodatki.css</strong> – stylizuje wygląd strony <strong>Dodatki</strong>, tabeli, przycisków oraz modali.</li>
        </ul>

        <h2>8. Przykładowy Przebieg Dodania Dodatku</h2>
        <ol>
            <li><strong>Użytkownik</strong> (Administrator lub Manager) klika przycisk "Dodaj nowy dodatek".</li>
            <li><strong>Modal z formularzem</strong> pojawia się: wypełnia się nazwę dodatku i cenę.</li>
            <li>Po kliknięciu przycisku <em>Dodaj</em>, dane przesyłane są metodą <code>POST</code> do <em>dodaj_dodatek.php</em>.</li>
            <li><strong>Backend</strong> waliduje dane (sprawdzenie, czy pola nie są puste, czy cena jest prawidłowa). 
                Jeśli poprawne – tworzy nowy rekord w bazie danych.</li>
            <li><strong>Front-end</strong> odświeża tabelę dodatków i wyświetla komunikat o sukcesie.</li>
        </ol>

        <h2>9. Przykładowy Przebieg Edycji Dodatku</h2>
        <ol>
            <li>W kolumnie <strong>Akcje</strong> kliknięcie przycisku <em>Edytuj</em> dla danego dodatku.</li>
            <li><strong>Modal edycji</strong> wypełnia się aktualnymi danymi dodatku pobranymi z tabeli.</li>
            <li>Użytkownik zmienia nazwę dodatku lub cenę.</li>
            <li><em>Zapisz zmiany</em> – dane są przesyłane metodą <code>PUT</code> do <em>edytuj_dodatek.php</em>.</li>
            <li>Backend aktualizuje rekord w bazie. Front-end odświeża tabelę.</li>
        </ol>

        <h2>10. Przykładowy Przebieg Usunięcia Dodatku</h2>
        <ol>
            <li>W kolumnie <strong>Akcje</strong> znajduje się przycisk <em>Usuń</em> tylko dla dodatków, które są niepowiązane z żadną rezerwacją (<em>is_deletable = true</em>).</li>
            <li>Po kliknięciu przycisku <em>Usuń</em> pojawia się okienko dialogowe <em>confirm</em>.</li>
            <li>Jeśli użytkownik potwierdzi, front-end wywołuje metodę <code>DELETE</code> na endpoint <em>usun_dodatek.php</em> z ID danego dodatku.</li>
            <li><strong>Backend</strong> sprawdza ponownie, czy dodatek jest niepowiązany z żadną rezerwacją. Jeśli tak – usuwa rekord z bazy.</li>
            <li>Front-end odświeża tabelę i usuwa dany dodatek z widoku, a użytkownik otrzymuje komunikat o sukcesie.</li>
        </ol>

        <h2>12. Podsumowanie</h2>
        <p>
            Moduł <strong>Dodatki</strong> w systemie <em>Stardust Hotel</em> umożliwia efektywne zarządzanie dodatkowymi usługami oferowanymi gościom. 
            Dzięki intuicyjnym interfejsom i dobrze zdefiniowanej logice CRUD (Create, Read, Update, Delete), Administratorzy i Managerowie mogą łatwo dostosowywać ofertę hotelu 
            do aktualnych potrzeb i trendów rynkowych. 
            <strong>Bezpieczeństwo</strong> oraz <strong>walidacja danych</strong> zapewniają, że operacje są przeprowadzane poprawnie i bezpiecznie.
        </p>
    </div>
</body>
</html>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
