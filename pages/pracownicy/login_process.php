<?php
session_start();
include_once __DIR__ . '/../../config.php';

// Sprawdzenie, czy formularz został wysłany metodą POST
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Pobranie danych z formularza i sanitizacja
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    if(empty($login) || empty($password)){
        $_SESSION['error'] = "Proszę wypełnić wszystkie pola.";
        header("Location: login.php");
        exit;
    }

    try {
        // Połączenie z bazą danych
        $pdo = new PDO($dsn, $db_user, $db_password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Przygotowanie zapytania do bazy danych
        $stmt = $pdo->prepare("
            SELECT p.*, r.nazwa_roli 
            FROM rezerwacje_hotelowe.pracownik p
            JOIN rezerwacje_hotelowe.rola r ON p.rola_id = r.id
            WHERE p.login = :login
        ");
        $stmt->bindParam(':login', $login);
        $stmt->execute();

        // Pobranie wyniku
        $pracownik = $stmt->fetch(PDO::FETCH_ASSOC);

        if($pracownik){
            // Sprawdzenie hasła
            if(md5($password) === $pracownik['hasło']){
                // Ustawienie zmiennych sesyjnych
                $_SESSION['logged_in'] = true;
                $_SESSION['pracownik_id'] = $pracownik['id'];
                $_SESSION['pracownik_login'] = $pracownik['login'];
                $_SESSION['rola_id'] = $pracownik['rola_id'];
                $_SESSION['role'] = $pracownik['nazwa_roli'];

                // Dodanie komunikatu sukcesu
                $_SESSION['success'] = "Zalogowano pomyślnie.";

                // Przekierowanie do dashboardu
                header("Location: dashboard.php");
                exit;
            } else {
                // Nieprawidłowe hasło
                $_SESSION['error'] = "Nieprawidłowy login lub hasło.";
                header("Location: login.php");
                exit;
            }
        } else {
            // Nie znaleziono użytkownika
            $_SESSION['error'] = "Nieprawidłowy login lub hasło.";
            header("Location: login.php");
            exit;
        }
    } catch(PDOException $e){
        // Obsługa błędów bazy danych
        $_SESSION['error'] = "Wystąpił błąd: " . $e->getMessage();
        header("Location: login.php");
        exit;
    }
} else {
    // Jeśli nie został wysłany metodą POST, przekieruj na stronę logowania
    header("Location: login.php");
    exit;
}
?>
