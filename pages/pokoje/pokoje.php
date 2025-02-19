<?php
include_once __DIR__ . '/../../includes/header.php'; 
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
    <title>Lista Pokoi</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_pokoje.css">
</head>
<body>
<div class="rooms-section">
    <h2 class="section-title">Lista Pokoi</h2>
    <button id="openModal" class="btn-primary">Dodaj nowy pokój</button>

    <div class="table-container">
        <table class="rooms-table">
            <thead>
                <tr>
                    <th>Nr</th>
                    <th>Numer Pokoju</th>
                    <th>Klasa Pokoju</th>
                    <th>Status Pokoju</th>
                    <th>Piętro</th>
                    <th>Maks. liczba osób</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table> 
    </div>
</div>

<!-- Modal do dodawania pokoju -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Dodaj nowy pokój</h2>
        <form id="addRoomForm">
            <div class="form-group">
                <label for="room_number">Numer Pokoju:</label>
                <input type="text" id="room_number" name="room_number" required>
            </div>
            <div class="form-group">
                <label for="floor">Piętro:</label>
                <select id="floor" name="floor" required></select>
            </div>
            <div class="form-group">
                <label for="room_class">Klasa Pokoju:</label>
                <select id="room_class" name="room_class" required></select>
            </div>
            <div class="form-group">
                <label for="room_status">Status Pokoju:</label>
                <select id="room_status" name="room_status" required></select>
            </div>
            <button type="button" id="addRoomBtn" class="btn-primary">Dodaj</button>
        </form>
    </div>
</div>

<!-- Modal do edycji pokoju -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edytuj dane pokoju</h2>
        <table class="edit-table">
            <tr>
                <td>ID:</td>
                <td id="edit-id"></td>
            </tr>
            <tr>
                <td>Numer Pokoju:</td>
                <td><input type="text" id="edit-room-number" value=""></td>
            </tr>
            <tr>
                <td>Klasa Pokoju:</td>
                <td><select id="edit-room-class"></select></td>
            </tr>
            <tr>
                <td>Status Pokoju:</td>
                <td><select id="edit-room-status"></select></td>
            </tr>
            <tr>
                <td>Piętro:</td>
                <td><select id="edit-floor"></select></td>
            </tr>
            <tr>
                <td>Maks. liczba osób:</td>
                <td id="edit-max-osob" class="non-editable"></td>
            </tr>
        </table>
        <div class="modal-buttons">
            <button id="save-changes" class="btn-primary">Zatwierdź</button>
        </div>
    </div>
</div>


<script src="<?php echo $base_url; ?>/assets/js/script_pokoje.js"></script>
<?php
include_once __DIR__ . '/../../includes/footer.php';
?>
