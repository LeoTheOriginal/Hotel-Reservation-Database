<?php
// Inkluzje PHP
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
    <title>Lista Rezerwacji</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_rezerwacje.css">
</head>
<body>
    <div class="reservations-section">
        <h2 class="section-title">Lista Rezerwacji</h2>
        <button id="openModal" class="btn btn-primary">Dodaj nową rezerwację</button>

        <div class="table-container">
            <table class="reservations-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gość</th>
                        <th>Pokój</th>
                        <th>Data zameldowania</th>
                        <th>Data wymeldowania</th>
                        <th>Status rezerwacji</th>
                        <th>Status płatności</th>
                        <th>Kwota</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table> 
        </div>
    </div>

    <!-- Modal do dodawania rezerwacji -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Dodaj nową rezerwację</h2>
            <form id="addReservationForm">
                <div class="form-group">
                    <label for="guest">Gość:</label>
                    <select id="guest" name="guest" required>
                        <option value="">Wybierz gościa</option>
                        <option value="new">Dodaj nowego gościa</option>
                    </select>
                </div>
                <div id="addGuestSection" style="display: none;">
                    <div class="form-group">
                        <label for="new_guest_name">Imię:</label>
                        <input type="text" id="new_guest_name" name="imie" required>
                    </div>
                    <div class="form-group">
                        <label for="new_guest_surname">Nazwisko:</label>
                        <input type="text" id="new_guest_surname" name="nazwisko" required>
                    </div>
                    <div class="form-group">
                        <label for="new_guest_phone">Telefon:</label>
                        <input type="text" id="new_guest_phone" name="telefon" required>
                    </div>
                    <div class="form-group">
                        <label for="new_guest_email">Email:</label>
                        <input type="email" id="new_guest_email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="adults">Liczba dorosłych:</label>
                    <input type="number" id="adults" name="liczba_doroslych" min="1" required>
                </div>
                <div class="form-group">
                    <label for="children">Liczba dzieci:</label>
                    <input type="number" id="children" name="liczba_dzieci" min="0" value="0">
                </div>
                <div class="form-group">
                    <label for="check_in">Data zameldowania:</label>
                    <input type="date" id="check_in" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="check_out">Data wymeldowania:</label>
                    <input type="date" id="check_out" name="end_date" required>
                </div>
                <div class="form-group">
                    <label for="room_class">Klasa pokoju:</label>
                    <select id="room_class" name="klasa_pokoju_id" required>
                        <option value="">Wybierz klasę pokoju</option>
                        
                    </select>
                </div>
                <div id="roomOptionsContainer" class="form-group">
                    
                </div>
                <div class="form-group">
                    <label for="addons">Dodatki:</label>
                    <div id="addonsOptionsContainer">
                       
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount">Kwota:</label>
                    <input type="text" id="amount" name="amount" readonly>
                </div>
                <button type="button" id="addReservationBtn" class="btn btn-primary">Dodaj</button>
            </form>
        </div>
    </div>

    <!-- Modal do edycji rezerwacji -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeEditModal">&times;</span>
            <h2>Edytuj rezerwację</h2>
            <form id="editReservationForm">
                <div class="form-group">
                    <label for="edit-reservation-id">ID rezerwacji:</label>
                    <span id="edit-reservation-id" class="non-editable"></span>
                </div>
                <div class="form-group">
                    <label for="edit_check_in">Data zameldowania:</label>
                    <input type="date" id="edit_check_in" name="check_in" required>
                </div>
                <div class="form-group">
                    <label for="edit_check_out">Data wymeldowania:</label>
                    <input type="date" id="edit_check_out" name="check_out" required>
                </div>
                <div class="form-group">
                    <label for="edit_guest">Gość:</label>
                    <select id="edit_guest" name="guest_id" required>
                        <option value="">Wybierz gościa</option>
                        
                    </select>
                </div>
                <div id="modalRoomOptionsContainer" class="form-group">
                   
                </div>
                <div class="form-group">
                    <label for="edit_reservation_status">Status rezerwacji:</label>
                    <select id="edit_reservation_status" name="reservation_status_id" required>
                        <option value="">Wybierz status rezerwacji</option>
                        
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_payment_status">Status płatności:</label>
                    <select id="edit_payment_status" name="payment_status_id" required>
                        <option value="">Wybierz status płatności</option>
                        
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_addons">Dodatki:</label>
                    <div id="editAddonsOptionsContainer">
                        
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_amount">Kwota (PLN):</label>
                    <input type="text" id="edit_amount" name="amount" readonly>
                </div>
                <button type="button" id="saveEditBtn" class="btn btn-primary">Zapisz zmiany</button>
            </form>
        </div>
    </div>

    <script src="<?php echo $base_url; ?>/assets/js/script_rezerwacje.js"></script>
    <?php
    include_once __DIR__ . '/../../includes/footer.php';
    ?>
</body>
</html>
