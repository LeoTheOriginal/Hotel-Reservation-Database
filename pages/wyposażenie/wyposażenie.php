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
    <title>Wyposażenie</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_wyposazenie.css">
</head>
<body>
<div class="equipment-section">
    <h2 class="section-title">Lista Wyposażenia</h2>
    <button id="openModal" class="btn-primary">Dodaj nowe wyposażenie</button>

    <div class="table-container">
        <table class="equipment-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nazwa wyposażenia</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>

<!-- Modal dodawania nowego wyposażenia -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Dodaj nowe wyposażenie</h2>
        <form id="addEquipmentForm">
            <div class="form-group">
                <label for="nazwaWyposazenia">Nazwa wyposażenia:</label>
                <input type="text" id="nazwaWyposazenia" name="nazwaWyposazenia" required>
            </div>
            <button type="button" id="addEquipmentBtn" class="btn-primary">Dodaj</button>
        </form>
    </div>
</div>

<!-- Modal edycji wyposażenia -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edytuj wyposażenie</h2>
        <form id="editEquipmentForm">
            <div class="form-group">
                <label for="edit-id">ID wyposażenia:</label>
                <span id="edit-id" class="non-editable"></span>
            </div>
            <div class="form-group">
                <label for="edit-nazwa-wyposazenia">Nazwa wyposażenia:</label>
                <input type="text" id="edit-nazwa-wyposazenia" name="editNazwaWyposazenia" required>
            </div>
            <button type="button" id="saveChangesBtn" class="btn-primary">Zapisz zmiany</button>
        </form>
    </div>
</div>

<script src="<?php echo $base_url; ?>/assets/js/script_wyposazenie.js"></script>
<?php
include_once __DIR__ . '/../../includes/footer.php';
?>
</body>
</html>
