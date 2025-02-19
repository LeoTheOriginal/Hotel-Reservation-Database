<?php
include_once __DIR__ . '/../../includes/header.php'; 
include_once __DIR__ . '/../../includes/header.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['role'] !== ROLE_ADMINISTRATOR && $_SESSION['role'] !== ROLE_MANAGER)) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Pracowników</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_pracownicy.css">
</head>
<body>
<div class="employees-section">
    <h2 class="section-title">Lista Pracowników</h2>
    <button id="openModal" class="btn-primary">Dodaj nowego pracownika</button>

    <div class="table-container">
        <table class="employees-table">
            <thead>
                <tr>
                    <th>Nr</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Login</th>
                    <th>Rola</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table> 
    </div>
</div>

<!-- Modal do dodawania pracownika -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Dodaj nowego pracownika</h2>
        <form id="addEmployeeForm">
            <div class="form-group">
                <label for="first_name">Imię:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Nazwisko:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Rola:</label>
                <select id="role" name="role" required>
                    <option value="">Wybierz rolę</option>
                    <!-- Role zostaną załadowane przez JS -->
                </select>
            </div>
            <button type="button" id="addEmployeeBtn" class="btn-primary">Dodaj</button>
        </form>
    </div>
</div>


<!-- Modal do edycji pracownika -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edytuj dane pracownika</h2>
        <form id="editEmployeeForm">
            <div class="form-group">
                <label for="edit-id">ID Pracownika:</label>
                <span id="edit-id" class="non-editable"></span>
            </div>
            <div class="form-group">
                <label for="edit-first-name">Imię:</label>
                <input type="text" id="edit-first-name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="edit-last-name">Nazwisko:</label>
                <input type="text" id="edit-last-name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="edit-login">Login:</label>
                <input type="text" id="edit-login" name="login" required>
            </div>
            <div class="form-group">
                <label for="edit-role">Rola:</label>
                <select id="edit-role" name="role" required>
                    
                </select>
            </div>
            <div class="actions">
                <button type="button" id="saveChangesBtn" class="btn-primary">Zapisz zmiany</button>
                <button type="button" id="changePasswordBtn" class="btn-secondary disabled">Zmień hasło</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal do zmiany hasła -->
<div id="changePasswordModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeChangePasswordModal">&times;</span>
        <h2>Zmień hasło</h2>
        <form id="changePasswordForm">
            <div class="form-group">
                <label for="current-password">Aktualne hasło:</label>
                <input type="password" id="current-password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new-password">Nowe hasło:</label>
                <input type="password" id="new-password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Potwierdź nowe hasło:</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
            </div>
            <button type="button" id="savePasswordBtn" class="btn-primary">Zmień</button>
        </form>
    </div>
</div>


<script src="<?php echo $base_url; ?>/assets/js/script_pracownicy.js"></script>
<?php
include_once __DIR__ . '/../../includes/footer.php';
?>
