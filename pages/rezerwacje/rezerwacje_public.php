<?php
// Inkluzje PHP
include_once __DIR__ . '/../../config.php';
include_once __DIR__ . '/../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezerwacje - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_rezerwacje_public.css">
</head>
<body>
    <header class="stardust-header">
        
    </header>

    <div class="public-reservations-section">
        <h2 class="section-title">Dokonaj Rezerwacji</h2>
        
        <!-- Formularz rezerwacji -->
        <form id="publicReservationForm" class="reservation-form">
            <div class="form-group">
                <label for="public_imie">Imię:</label>
                <input type="text" id="public_imie" name="imie" required>
            </div>
            <div class="form-group">
                <label for="public_nazwisko">Nazwisko:</label>
                <input type="text" id="public_nazwisko" name="nazwisko" required>
            </div>
            <div class="form-group">
                <label for="public_telefon">Telefon:</label>
                <input type="text" id="public_telefon" name="telefon" required>
            </div>
            <div class="form-group">
                <label for="public_email">Email:</label>
                <input type="email" id="public_email" name="email" required>
            </div>
            <div class="form-group">
                <label for="public_adults">Liczba dorosłych:</label>
                <input type="number" id="public_adults" name="liczba_doroslych" min="1" required>
            </div>
            <div class="form-group">
                <label for="public_children">Liczba dzieci:</label>
                <input type="number" id="public_children" name="liczba_dzieci" min="0" value="0">
            </div>
            <div class="form-group">
                <label for="public_check_in">Data zameldowania:</label>
                <input type="date" id="public_check_in" name="start_date" required>
            </div>
            <div class="form-group">
                <label for="public_check_out">Data wymeldowania:</label>
                <input type="date" id="public_check_out" name="end_date" required>
            </div>
            <div class="form-group">
                <label for="public_room_class">Klasa pokoju:</label>
                <select id="public_room_class" name="klasa_pokoju_id" required>
                    <option value="">Wybierz klasę pokoju</option>
                    
                </select>
            </div>
            <div id="public_roomOptionsContainer" class="form-group">
                
            </div>
            <div class="form-group">
                <label for="public_addons">Dodatki:</label>
                <div id="public_addonsOptionsContainer">
                    
                </div>
            </div>
            <div class="form-group">
                <label for="public_amount">Kwota:</label>
                <input type="text" id="public_amount" name="amount" readonly>
            </div>
            <button type="button" id="public_addReservationBtn" class="btn btn-primary">Dodaj Rezerwację</button>
        </form>
    </div>

    <footer class="stardust-footer">
        
    </footer>

    <!-- Skrypty JavaScript -->
    <script src="<?php echo $base_url; ?>/assets/js/script_rezerwacje_public.js"></script>
    <?php include_once __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
