<?php
session_start();
include_once __DIR__ . '/../../config.php';

// Włącz wyświetlanie błędów
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sprawdzenie, czy użytkownik jest zalogowany
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    header("Location: login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0){
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_tmp_path = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_size = $_FILES['profile_picture']['size'];
        $file_type = $_FILES['profile_picture']['type'];

        // Sprawdzenie rozszerzenia pliku
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if(!in_array($file_extension, $allowed_extensions)){
            $_SESSION['error'] = "Dozwolone formaty zdjęć: " . implode(', ', $allowed_extensions);
            header("Location: dashboard.php");
            exit;
        }

        // Sprawdzenie rzeczywistego typu MIME pliku
        $check = getimagesize($file_tmp_path);
        if($check === false){
            $_SESSION['error'] = "Przesłany plik nie jest prawidłowym obrazem.";
            header("Location: dashboard.php");
            exit;
        }

        // Sprawdzenie rozmiaru pliku (max 2MB)
        if($file_size > 2 * 1024 * 1024){
            $_SESSION['error'] = "Zdjęcie nie może przekraczać 2MB.";
            header("Location: dashboard.php");
            exit;
        }

        // Generowanie unikalnej nazwy pliku
        $new_file_name = uniqid('profile_', true) . '.' . $file_extension;

        $upload_file_dir = __DIR__ . '/../../assets/images/uploads/';

        if(!is_dir($upload_file_dir)){
            if(!mkdir($upload_file_dir, 0755, true)){
                $_SESSION['error'] = "Nie udało się utworzyć folderu uploads.";
                header("Location: dashboard.php");
                exit;
            }
        }

        if(!is_writable($upload_file_dir)){
            $_SESSION['error'] = "Folder uploads nie ma uprawnień do zapisu.";
            header("Location: dashboard.php");
            exit;
        }

        $dest_path = $upload_file_dir . $new_file_name;

        // Przeniesienie pliku
        if(move_uploaded_file($file_tmp_path, $dest_path)){
            try {
                $pdo = new PDO($dsn, $db_user, $db_password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);

                // Pobranie aktualnego zdjęcia profilowego
                $stmt = $pdo->prepare("SELECT profile_picture FROM rezerwacje_hotelowe.pracownik WHERE id = :id");
                $stmt->bindParam(':id', $_SESSION['pracownik_id'], PDO::PARAM_INT);
                $stmt->execute();
                $pracownik = $stmt->fetch(PDO::FETCH_ASSOC);

                // Jeśli istnieje poprzednie zdjęcie, usuń je
                if($pracownik && !empty($pracownik['profile_picture'])){
                    $old_picture_path = $upload_file_dir . $pracownik['profile_picture'];
                    if(file_exists($old_picture_path)){
                        unlink($old_picture_path);
                    }
                }

                // Aktualizacja ścieżki do zdjęcia w bazie danych
                $stmt = $pdo->prepare("UPDATE rezerwacje_hotelowe.pracownik SET profile_picture = :profile_picture WHERE id = :id");
                $stmt->bindParam(':profile_picture', $new_file_name, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_SESSION['pracownik_id'], PDO::PARAM_INT);
                $stmt->execute();

                $_SESSION['success'] = "Zdjęcie profilowe zostało pomyślnie zaktualizowane.";
                header("Location: dashboard.php");
                exit;

            } catch(PDOException $e){
                if(file_exists($dest_path)){
                    unlink($dest_path);
                }
                $_SESSION['error'] = "Wystąpił błąd: " . $e->getMessage();
                header("Location: dashboard.php");
                exit;
            }
        } else {
            // Pobierz ostatni błąd PHP
            $error = error_get_last();
            $_SESSION['error'] = "Wystąpił błąd podczas uploadu zdjęcia: " . $error['message'];
            header("Location: dashboard.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Proszę wybrać zdjęcie do uploadu.";
        header("Location: dashboard.php");
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>
