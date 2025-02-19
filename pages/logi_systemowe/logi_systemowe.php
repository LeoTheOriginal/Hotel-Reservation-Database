<?php
include_once __DIR__ . '/../../includes/header.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== ROLE_ADMINISTRATOR) {
    die('Nie masz uprawnień do przeglądania tej strony.');
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logi Systemowe</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_logi.css">
</head>
<body>
<div class="logs-section">
    <h2 class="section-title">Logi Systemowe</h2>

    <div class="logs-actions">
        <button id="clearLogsBtn" class="btn-danger">Wyczyść wszystkie logi</button>
       
    </div>

    <div class="table-container">
        <table class="logs-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Opis</th>
                    <th>Szczegóły</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
               
            </tbody>
        </table>
    </div>
</div>

<!-- Modal do wyświetlania szczegółów loga (opcjonalne) -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeDetailsModal">&times;</span>
        <h2>Szczegóły logu</h2>
        <pre id="detailsContent"></pre> 
    </div>
</div>

<script src="<?php echo $base_url; ?>/assets/js/script_logi_systemowe.js"></script>
<?php
include_once __DIR__ . '/../../includes/footer.php';
?>
</body>
</html>
