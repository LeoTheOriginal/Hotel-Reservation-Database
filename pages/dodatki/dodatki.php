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
    <title>Dodatki</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_dodatki.css">
</head>
<body>
<div class="addons-section">
    <h2 class="section-title">Lista Dodatków</h2>
    <button id="openModal" class="btn-primary">Dodaj nowy dodatek</button>

    <div class="table-container">
        <table class="addons-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nazwa dodatku</th>
                    <th>Cena (PLN)</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: dodawanie nowego dodatku -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Dodaj nowy dodatek</h2>
        <form id="addAddonForm">
            <div class="form-group">
                <label for="nazwaDodatku">Nazwa dodatku:</label>
                <input type="text" id="nazwaDodatku" name="nazwaDodatku" required>
            </div>
            <div class="form-group">
                <label for="cena">Cena (PLN):</label>
                <input type="number" step="0.01" id="cena" name="cena" required>
            </div>
            <button type="button" id="addAddonBtn" class="btn-primary">Dodaj</button>
        </form>
    </div>
</div>

<!-- Modal: edycja dodatku -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edytuj dodatek</h2>
        <form id="editAddonForm">
            <div class="form-group">
                <label for="edit-id">ID dodatku:</label>
                <span id="edit-id" class="non-editable"></span>
            </div>
            <div class="form-group">
                <label for="edit-nazwa-dodatku">Nazwa dodatku:</label>
                <input type="text" id="edit-nazwa-dodatku" name="editNazwaDodatku" required>
            </div>
            <div class="form-group">
                <label for="edit-cena">Cena (PLN):</label>
                <input type="number" step="0.01" id="edit-cena" name="editCena" required>
            </div>
            <button type="button" id="saveChangesBtn" class="btn-primary">Zapisz zmiany</button>
        </form>
    </div>
</div>

<script src="<?php echo $base_url; ?>/assets/js/script_dodatki.js"></script>
<?php
include_once __DIR__ . '/../../includes/footer.php';
?>
</body>
</html>
