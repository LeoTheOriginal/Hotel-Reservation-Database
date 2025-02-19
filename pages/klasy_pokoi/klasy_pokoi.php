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
    <title>Klasy Pokoi</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_klasy_pokoi.css">
</head>
<body>
<div class="room-classes-section">
    <h2 class="section-title">Lista Klas Pokoi</h2>
    <button id="openModal" class="btn-primary">Dodaj nową klasę pokoju</button>

    <div class="table-container">
        <table class="room-classes-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nazwa klasy</th>
                    <th>Cena podstawowa (PLN)</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Dodawanie nowej klasy pokoju -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Dodaj nową klasę pokoju</h2>
        <form id="addRoomClassForm">
            <div class="form-group">
                <label for="nazwaKlasy">Nazwa klasy:</label>
                <input type="text" id="nazwaKlasy" name="nazwaKlasy" required>
            </div>
            <div class="form-group">
                <label for="cenaPodstawowa">Cena podstawowa (PLN):</label>
                <input type="number" step="0.01" id="cenaPodstawowa" name="cenaPodstawowa" required>
            </div>
            <div class="form-group">
                <label for="add-lozka-select">Rodzaje łóżek:</label>
                <select id="add-lozka-select" name="addLozkaSelect" multiple size="4">
                    
                </select>
            </div>
            <div class="form-group">
                <label>Wyposażenie:</label>
                <div id="add-equipment-list">
                    
                </div>
            </div>
            <button type="button" id="addRoomClassBtn" class="btn-primary">Dodaj</button>
        </form>
    </div>
</div>

<!-- Modal: Edycja klasy pokoju -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edytuj klasę pokoju</h2>
        <form id="editRoomClassForm">
            <div class="form-group">
                <label for="edit-id">ID klasy:</label>
                <span id="edit-id" class="non-editable"></span>
            </div>
            <div class="form-group">
                <label for="edit-nazwa-klasy">Nazwa klasy:</label>
                <input type="text" id="edit-nazwa-klasy" name="editNazwaKlasy" required>
            </div>
            <div class="form-group">
                <label for="edit-cena-podstawowa">Cena podstawowa (PLN):</label>
                <input type="number" step="0.01" id="edit-cena-podstawowa" name="editCenaPodstawowa" required>
            </div>
            <div class="form-group">
                <label for="edit-lozka-select">Rodzaje łóżek:</label>
                <select id="edit-lozka-select" name="editLozkaSelect" multiple size="4">
                </select>
            </div>
            <div class="form-group">
                <label>Wyposażenie:</label>
                <div id="edit-equipment-list">
                </div>
            </div>
            <button type="button" id="saveChangesBtn" class="btn-primary">Zapisz zmiany</button>
        </form>
    </div>
</div>

<script src="<?php echo $base_url; ?>/assets/js/script_klasy_pokoi.js"></script>
<?php
include_once __DIR__ . '/../../includes/footer.php';
?>
</body>
</html>
