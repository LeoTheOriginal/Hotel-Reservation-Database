<?php
require_once __DIR__ . '/../../config.php';

// Włącz wyświetlanie błędów na czas testów
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sprawdzamy, czy formularz został wysłany
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = $_POST['imię'] ?? '';
    $nazwisko = $_POST['nazwisko'] ?? '';
    $telefon = $_POST['numer_telefonu'] ?? '';
    $email = $_POST['adres_email'] ?? '';

    // Sprawdzamy czy wszystkie dane zostały podane
    if (empty($imie) || empty($nazwisko) || empty($telefon) || empty($email)) {
        echo "Wypełnij wszystkie pola.<br>";
    } else {
        try {
            // Sprawdzałem z cudzysłowami
            $stmt = $pdo->prepare('INSERT INTO rezerwacje_hotelowe."gość" ("imię", "nazwisko", "numer_telefonu", "adres_email") 
                                   VALUES (:imie, :nazwisko, :tel, :email)');
            $stmt->execute([
                ':imie' => $imie,
                ':nazwisko' => $nazwisko,
                ':tel' => $telefon,
                ':email' => $email
            ]);
            echo "Gość został dodany pomyślnie!<br><br>";
        } catch (PDOException $e) {
            echo "Błąd dodawania gościa: " . $e->getMessage() . "<br><br>";
        }
    }
}

// Wyświetlamy listę gości z bazy, aby sprawdzić, czy nowy został dodany
try {
    $stmt = $pdo->query('SELECT "id", "imię", "nazwisko", "numer_telefonu", "adres_email" 
                         FROM rezerwacje_hotelowe."gość" 
                         ORDER BY "nazwisko", "imię"');
    $goscie = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($goscie) {
        echo "<h2>Lista gości:</h2>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Imię</th><th>Nazwisko</th><th>Numer Telefonu</th><th>Adres Email</th></tr>";
        foreach ($goscie as $g) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($g['id']) . "</td>";
            echo "<td>" . htmlspecialchars($g['imię']) . "</td>";
            echo "<td>" . htmlspecialchars($g['nazwisko']) . "</td>";
            echo "<td>" . htmlspecialchars($g['numer_telefonu']) . "</td>";
            echo "<td>" . htmlspecialchars($g['adres_email']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Brak gości w bazie.";
    }

} catch (PDOException $e) {
    echo "Błąd zapytania: " . $e->getMessage();
}

?>

<hr>

<h2>Dodaj nowego gościa - test</h2>
<form method="post">
    <label>Imię: <input type="text" name="imię" required></label><br><br>
    <label>Nazwisko: <input type="text" name="nazwisko" required></label><br><br>
    <label>Numer Telefonu: <input type="text" name="numer_telefonu" required></label><br><br>
    <label>Adres Email: <input type="email" name="adres_email" required></label><br><br>
    <button type="submit">Dodaj</button>
</form>
