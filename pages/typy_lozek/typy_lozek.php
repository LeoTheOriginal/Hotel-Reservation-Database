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
    <title>Typy łóżek</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_typy_lozek.css">
</head>
<body>
<div class="bed-types-section">
    <h2 class="section-title">Lista Typów Łóżek</h2>
    <button id="openModal" class="btn-primary">Dodaj nowy typ łóżka</button>

    <div class="table-container">
        <table class="bed-types-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nazwa typu łóżka</th>
                    <th>Liczba osób</th> 
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: dodawanie nowego typu łóżka -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Dodaj nowy typ łóżka</h2>
        <form id="addBedTypeForm">
            <div class="form-group">
                <label for="nazwaTypu">Nazwa typu łóżka:</label>
                <input type="text" id="nazwaTypu" name="nazwaTypu" required>
            </div>
            <div class="form-group">
                <label for="liczbaOsob">Liczba osób:</label>
                <input type="number" id="liczbaOsob" name="liczbaOsob" min="1" required>
            </div>
            <button type="button" id="addBedTypeBtn" class="btn-primary">Dodaj</button>
        </form>
    </div>
</div>

<!-- Modal: edycja typu łóżka -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edytuj typ łóżka</h2>
        <form id="editBedTypeForm">
            <div class="form-group">
                <label for="edit-id">ID typu łóżka:</label>
                <span id="edit-id" class="non-editable"></span>
            </div>
            <div class="form-group">
                <label for="edit-nazwa-typu">Nazwa typu łóżka:</label>
                <input type="text" id="edit-nazwa-typu" name="editNazwaTypu" required>
            </div>
            <div class="form-group">
                <label for="edit-liczba-osob">Liczba osób:</label>
                <input type="number" id="edit-liczba-osob" name="editLiczbaOsob" min="1" required>
            </div>
            <button type="button" id="saveChangesBtn" class="btn-primary">Zapisz zmiany</button>
        </form>
    </div>
</div>

<script src="<?php echo $base_url; ?>/assets/js/script_typy_lozek.js"></script>
<?php
include_once __DIR__ . '/../../includes/footer.php';
?>
</body>
</html>
