document.addEventListener('DOMContentLoaded', function () {
    const generateReportBtn = document.getElementById('generateReportBtn');
    const reportTypeSelect = document.getElementById('reportType');
    const chartContainer = document.getElementById('chartContainer');
    const exportButtons = document.getElementById('exportButtons');
    const tableContainer = document.getElementById('tableContainer');
    const reportTable = document.getElementById('reportTable').getElementsByTagName('tbody')[0];
    let currentChart = null;

    generateReportBtn.addEventListener('click', generateReport);

    // Eksport
    document.getElementById('exportPdf').addEventListener('click', exportToPDF);
    document.getElementById('exportExcel').addEventListener('click', exportToExcel);
    document.getElementById('exportCsv').addEventListener('click', exportToCSV);

    function generateReport() {
        const selectedType = reportTypeSelect.value;
        if (!selectedType) {
            alert('Proszę wybrać typ raportu.');
            return;
        }

        fetch(`generuj_raport.php?type=${encodeURIComponent(selectedType)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderChart(data.chart, selectedType);
                    renderTable(data.table, selectedType);
                    chartContainer.style.display = 'block';
                    tableContainer.style.display = 'block';
                    exportButtons.style.display = 'block';
                } else {
                    alert(`Błąd generowania raportu: ${data.error}`);
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
                alert('Wystąpił błąd podczas generowania raportu.');
            });
    }

    function renderChart(chartData, reportType) {
        const ctx = document.getElementById('reportChart').getContext('2d');

        // Zniszcz poprzedni wykres, jeśli istnieje
        if (currentChart) {
            currentChart.destroy();
        }

        // Przygotuj dane dla wykresu
        if (reportType === 'guests') {
            currentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Raport dotyczący gości'
                        }
                    }
                },
            });
        } else if (reportType === 'financial') {
            currentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: chartData.label,
                        data: chartData.data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Rezerwacje Finansowe'
                        }
                    }
                },
            });
        } else if (reportType === 'rooms') {
            currentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: chartData.label,
                        data: chartData.data,
                        backgroundColor: 'rgba(153, 102, 255, 0.6)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        title: {
                            display: true,
                            text: 'Raport dotyczący pokoi'
                        }
                    }
                },
            });
        }
    }

    function renderTable(tableData, reportType) {
        // Wyczyść istniejącą tabelę
        const thead = reportTable.parentElement.querySelector('thead');
        const tbody = reportTable;
        thead.innerHTML = '';
        tbody.innerHTML = '';

        if (reportType === 'financial') {
            // Nagłówki
            thead.innerHTML = `
                <tr>
                    <th>Miesiąc</th>
                    <th>Przychód (PLN)</th>
                </tr>
            `;
            // Dane
            tableData.forEach(row => {
                const tr = document.createElement('tr');
                const przychod = parseFloat(row.przychód);
                tr.innerHTML = `
                    <td>${formatDate(row.miesiąc)}</td>
                    <td>${isNaN(przychod) ? '0.00' : przychod.toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
            });
        } else if (reportType === 'rooms') {
            // Nagłówki
            thead.innerHTML = `
                <tr>
                    <th>Numer Pokoju</th>
                    <th>Liczba Rezerwacji</th>
                </tr>
            `;
            // Dane
            tableData.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.numer_pokoju}</td>
                    <td>${row.liczba_rezerwacji}</td>
                `;
                tbody.appendChild(tr);
            });
        } else if (reportType === 'guests') {
            // Nagłówki
            thead.innerHTML = `
                <tr>
                    <th>ID Gościa</th>
                    <th>Imię i Nazwisko</th>
                    <th>Liczba Aktywnych Rezerwacji</th>
                    <th>Liczba Wszystkich Rezerwacji</th>
                </tr>
            `;
            // Dane
            tableData.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.gość_id}</td>
                    <td>${row.imię} ${row.nazwisko}</td>
                    <td>${row.liczba_aktywnych_rezerwacji}</td>
                    <td>${row.liczba_wszystkich_rezerwacji}</td>
                `;
                tbody.appendChild(tr);
            });
        }
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long' };
        const date = new Date(dateString);
        return date.toLocaleDateString('pl-PL', options);
    }

    // Eksport do PDF
    function exportToPDF() {
        const reportType = reportTypeSelect.value;
        const chartCanvas = document.getElementById('reportChart');
        const tableHTML = document.getElementById('reportTable').outerHTML;

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Dodaj tytuł
        doc.setFontSize(18);
        doc.text('Raport', 14, 22);

        // Dodaj wykres
        const imgData = chartCanvas.toDataURL('image/png', 1.0);
        doc.addImage(imgData, 'PNG', 14, 30, 180, 100);

        // Dodaj tabelę
        doc.autoTable({ html: '#reportTable', startY: 140 });

        doc.save(`raport_${reportType}_${new Date().toISOString().slice(0,10)}.pdf`);
    }

    // Eksport do Excel
    function exportToExcel() {
        const reportType = reportTypeSelect.value;
        const table = document.getElementById('reportTable');
        const workbook = XLSX.utils.book_new();
        const worksheet = XLSX.utils.table_to_sheet(table);

        XLSX.utils.book_append_sheet(workbook, worksheet, 'Raport');
        XLSX.writeFile(workbook, `raport_${reportType}_${new Date().toISOString().slice(0,10)}.xlsx`);
    }

    // Eksport do CSV
    function exportToCSV() {
        const reportType = reportTypeSelect.value;
        const table = document.getElementById('reportTable');
        const csv = tableToCSV(table);
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", `raport_${reportType}_${new Date().toISOString().slice(0,10)}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function tableToCSV(table) {
        const rows = Array.from(table.querySelectorAll("tr"));
        return rows.map(row => {
            const cells = Array.from(row.querySelectorAll("th, td"));
            return cells.map(cell => `"${cell.textContent.replace(/"/g, '""')}"`).join(",");
        }).join("\n");
    }
});
