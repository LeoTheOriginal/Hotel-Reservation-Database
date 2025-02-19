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
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Walidacja danych
    if(empty($current_password) || empty($new_password) || empty($confirm_password)){
        $_SESSION['error'] = "Proszę wypełnić wszystkie pola.";
        header("Location: dashboard.php");
        exit;
    }

    if($new_password !== $confirm_password){
        $_SESSION['error'] = "Nowe hasła nie są zgodne.";
        header("Location: dashboard.php");
        exit;
    }

    // Dodatkowe walidacje
    if(strlen($new_password) < 6){
        $_SESSION['error'] = "Nowe hasło musi mieć co najmniej 6 znaków.";
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

        // Aktualizacja hasła
        $hashed_new_password = md5($new_password);

        $stmt = $pdo->prepare("UPDATE rezerwacje_hotelowe.pracownik SET hasło = :new_password WHERE id = :id");
        $stmt->bindParam(':new_password', $hashed_new_password, PDO::PARAM_STR);
        $stmt->bindParam(':id', $_SESSION['pracownik_id'], PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = "Hasło zostało pomyślnie zaktualizowane.";
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
