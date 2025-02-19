<?php
session_start();

// Usunięcie wszystkich zmiennych sesyjnych
$_SESSION = array();

// Zniszczenie sesji
session_destroy();

// Przekierowanie do strony logowania z komunikatem
session_start();
$_SESSION['success'] = "Wylogowano pomyślnie.";
header("Location: login.php");
exit;
?>
