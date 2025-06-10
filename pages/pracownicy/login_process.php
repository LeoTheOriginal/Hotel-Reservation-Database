<?php
session_start();
include_once __DIR__ . '/../../config.php';

// Sprawdzenie, czy formularz został wysłany metodą POST
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Pobranie danych z formularza i sanityzacja
    $login = sanitizeInput(trim($_POST['login'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Walidacja danych wejściowych
    $errors = [];
    
    if(empty($login)){
        $errors[] = "Login jest wymagany.";
    }
    
    if(empty($password)){
        $errors[] = "Hasło jest wymagane.";
    }
    
    if(!empty($errors)){
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: login.php");
        exit;
    }

    try {
        // Użycie istniejącego połączenia z pliku config.php
        // zamiast tworzenia nowego
        
        // Przygotowanie bezpiecznego zapytania do bazy danych
        $stmt = prepareQuery($pdo, "
            SELECT p.*, r.nazwa_roli 
            FROM rezerwacje_hotelowe.pracownik p
            JOIN rezerwacje_hotelowe.rola r ON p.rola_id = r.id
            WHERE p.login = :login
        ", [':login' => $login]);

        // Pobranie wyniku
        $pracownik = $stmt->fetch(PDO::FETCH_ASSOC);

        if($pracownik){
            // Sprawdzenie hasła
            // Uwaga: md5 nie jest bezpieczną metodą hashowania haseł!
            // W produkcyjnym systemie należy użyć password_hash i password_verify
            if(md5($password) === $pracownik['hasło']){
                // Regeneracja ID sesji dla zapobiegania atakom session fixation
                session_regenerate_id(true);
                
                // Ustawienie zmiennych sesyjnych
                $_SESSION['logged_in'] = true;
                $_SESSION['pracownik_id'] = $pracownik['id'];
                $_SESSION['pracownik_login'] = $pracownik['login'];
                $_SESSION['rola_id'] = $pracownik['rola_id'];
                $_SESSION['role'] = $pracownik['nazwa_roli'];
                $_SESSION['last_activity'] = time(); // Dodanie czasu ostatniej aktywności

                // Dodanie komunikatu sukcesu
                $_SESSION['success'] = "Zalogowano pomyślnie.";

                // Przekierowanie do dashboardu
                header("Location: dashboard.php");
                exit;
            } else {
                // Nieprawidłowe hasło - opóźnienie aby zapobiegać atakom brute force
                sleep(1);
                $_SESSION['error'] = "Nieprawidłowy login lub hasło.";
                header("Location: login.php");
                exit;
            }
        } else {
            // Nie znaleziono użytkownika - opóźnienie aby zapobiegać atakom brute force
            sleep(1);
            $_SESSION['error'] = "Nieprawidłowy login lub hasło.";
            header("Location: login.php");
            exit;
        }
    } catch(PDOException $e){
        // Bezpieczna obsługa błędów - logowanie błędu bez ujawniania szczegółów technicznych
        error_log("Błąd bazy danych podczas logowania: " . $e->getMessage());
        $_SESSION['error'] = "Wystąpił błąd podczas logowania. Proszę spróbować ponownie.";
        header("Location: login.php");
        exit;
    }
} else {
    // Jeśli nie został wysłany metodą POST, przekieruj na stronę logowania
    header("Location: login.php");
    exit;
}
?>
