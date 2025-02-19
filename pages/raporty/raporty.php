<?php
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
    <title>Raporty</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_raporty.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="reports-section">
    <h2 class="section-title">Raporty</h2>

    <!-- Formularz wyboru raportu -->
    <form id="reportForm" class="form-group">
        <label for="reportType">Wybierz raport:</label>
        <select id="reportType" name="report_type" required>
            <option value="">Wybierz...</option>
            <option value="financial">Rezerwacje finansowe</option>
            <option value="rooms">Raport dotyczący pokoi</option>
            <option value="guests">Raport dotyczący gości</option>
        </select>
        <button type="button" id="generateReportBtn" class="btn-primary">Generuj raport</button>
    </form>

    <!-- Kontener na wykres -->
    <div id="chartContainer" style="display:none;">
        <canvas id="reportChart"></canvas>
    </div>

    <!-- Przyciski eksportu -->
    <div id="exportButtons" style="display:none;">
        <button id="exportPdf" class="btn-secondary">Eksportuj do PDF</button>
        <button id="exportExcel" class="btn-secondary">Eksportuj do Excel</button>
        <button id="exportCsv" class="btn-secondary">Eksportuj do CSV</button>
    </div>

    <!-- Tabela z danymi -->
    <div id="tableContainer" style="display:none;">
        <table id="reportTable" class="report-table">
            <thead>
                
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>


<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- jsPDF-AutoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<!-- SheetJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script src="<?php echo $base_url; ?>/assets/js/script_raporty.js"></script>
</body>
</html>
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
