<?php
include_once __DIR__ . '/../../includes/header.php'; 

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Gości</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_goscie.css">
</head>
<body>
<div class="guests-section">
    <h2 class="section-title">Lista Gości</h2>
    <button id="openModal" class="btn-primary">Dodaj nowego gościa</button>
    
    <div class="table-container">
        <table class="guests-table">
            <thead>
                <tr>
                    <th>Nr</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Numer Telefonu</th>
                    <th>Adres Email</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table> 
    </div>
</div>

<!-- Modal do dodawania gościa -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Dodaj nowego gościa</h2>
        <form id="addGuestForm">
            <div class="form-group">
                <label for="prefix">Prefiks:</label>
                <select id="prefix" name="prefix">
                    <option value="+48">+48 Polska</option>
                    <option value="+49">+49 Niemcy</option>
                    <option value="+44">+44 Wielka Brytania</option>
                    <option value="+1">+1 USA</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numer_telefonu">Numer Telefonu:</label>
                <input type="text" id="numer_telefonu" name="numer_telefonu" required placeholder="Podaj numer telefonu">
                <span id="phoneError" class="error-message"></span>
            </div>
            <div class="form-group">
                <label for="imię">Imię:</label>
                <input type="text" id="imię" name="imię" required placeholder="Podaj imię">
            </div>
            <div class="form-group">
                <label for="nazwisko">Nazwisko:</label>
                <input type="text" id="nazwisko" name="nazwisko" required placeholder="Podaj nazwisko">
            </div>
            <div class="form-group">
                <label for="adres_email">Adres Email:</label>
                <input type="email" id="adres_email" name="adres_email" required placeholder="Podaj adres email">
                <span id="emailError" class="error-message"></span>
            </div>
            <button type="button" id="addGuestBtn" class="btn-primary">Dodaj</button>
        </form>
    </div>
</div>

<!-- Modal do edycji gościa -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edytuj dane gościa</h2>
        <table class="edit-table">
            <tr>
                <td>ID:</td>
                <td id="edit-id"></td>
                <td></td>
            </tr>
            <tr>
                <td>Imię:</td>
                <td><input type="text" id="edit-name" value=""></td>
            </tr>
            <tr>
                <td>Nazwisko:</td>
                <td><input type="text" id="edit-surname" value=""></td>
            </tr>
            <tr>
                <td>Numer Telefonu:</td>
                <td><input type="text" id="edit-phone" value=""></td>
            </tr>
            <tr>
                <td>Adres Email:</td>
                <td><input type="email" id="edit-email" value=""></td>
            </tr>
        </table>
        <div class="modal-buttons">
            <button id="save-changes" class="btn-primary">Zatwierdź</button>
            <button id="cancel-changes" class="btn-danger">Anuluj</button>
        </div>
    </div>
</div>

<script src="<?php echo $base_url; ?>/assets/js/script_goscie.js"></script>
<?php
include_once __DIR__ . '/../../includes/footer.php';
?>