<?php
session_start();
include_once __DIR__ . '/../../config.php';

// Sprawdzenie, czy użytkownik jest zalogowany
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    header("Location: login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Pobranie danych z formularza i sanitizacja
    $current_password = trim($_POST['current_password']);
    $new_login = trim($_POST['new_login']);
    $confirm_password = trim($_POST['confirm_password']);

    // Walidacja danych
    if(empty($current_password) || empty($new_login) || empty($confirm_password)){
        $_SESSION['error'] = "Proszę wypełnić wszystkie pola.";
        header("Location: dashboard.php");
        exit;
    }

    // if($new_login !== $new_login){
        
    // }

    if(empty($current_password) || empty($confirm_password)){
        $_SESSION['error'] = "Proszę wprowadzić aktualne hasło.";
        header("Location: dashboard.php");
        exit;
    }

    try {
        $pdo = new PDO($dsn, $db_user, $db_password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Pobranie aktualnego hasła z bazy danych
        $stmt = $pdo->prepare("SELECT hasło FROM rezerwacje_hotelowe.pracownik WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['pracownik_id'], PDO::PARAM_INT);
        $stmt->execute();
        $pracownik = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$pracownik){
            $_SESSION['error'] = "Nie znaleziono pracownika.";
            header("Location: dashboard.php");
            exit;
        }

        // Weryfikacja aktualnego hasła
        if(md5($current_password) !== $pracownik['hasło']){
            $_SESSION['error'] = "Nieprawidłowe aktualne hasło.";
            header("Location: dashboard.php");
            exit;
        }

        // Sprawdzenie czy nowy login jest już zajęty
        $stmt = $pdo->prepare("SELECT id FROM rezerwacje_hotelowe.pracownik WHERE login = :login AND id != :id");
        $stmt->bindParam(':login', $new_login, PDO::PARAM_STR);
        $stmt->bindParam(':id', $_SESSION['pracownik_id'], PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $_SESSION['error'] = "Nowy login jest już zajęty.";
            header("Location: dashboard.php");
            exit;
        }

        // Aktualizacja loginu
        $stmt = $pdo->prepare("UPDATE rezerwacje_hotelowe.pracownik SET login = :new_login WHERE id = :id");
        $stmt->bindParam(':new_login', $new_login, PDO::PARAM_STR);
        $stmt->bindParam(':id', $_SESSION['pracownik_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Aktualizacja sesji z nowym loginem
        $_SESSION['pracownik_login'] = $new_login;

        $_SESSION['success'] = "Login został pomyślnie zaktualizowany.";
        header("Location: dashboard.php");
        exit;

    } catch(PDOException $e){
        $_SESSION['error'] = "Wystąpił błąd: " . $e->getMessage();
        header("Location: dashboard.php");
        exit;
    }
} else {
    // Jeśli nie został wysłany metodą POST, przekieruj na dashboard
    header("Location: dashboard.php");
    exit;
}
?>
