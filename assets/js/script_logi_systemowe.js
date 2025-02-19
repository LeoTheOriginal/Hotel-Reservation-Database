document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('.logs-table tbody');
    const clearLogsBtn = document.getElementById('clearLogsBtn');

    const detailsModal = document.getElementById('detailsModal');
    const closeDetailsModalBtn = document.getElementById('closeDetailsModal');
    const detailsContent = document.getElementById('detailsContent');

    // =============== Funkcja pobierania i odświeżania tabeli logów ===============
    function odswiezTabeleLogow() {
        fetch(`${baseUrl}/pages/logi_systemowe/pobierz_logi_systemowe.php`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data)) {
                    console.error('Nieprawidłowy format danych logów:', data);
                    alert(data.error || 'Wystąpił nieznany błąd podczas pobierania logów.');
                    return;
                }

                tableBody.innerHTML = '';

                data.forEach(log => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${log.id}</td>
                        <td>${log.data}</td>
                        <td>${log.opis}</td>
                        <td>
                            <button class="btn-secondary szczegoly-btn" data-json='${JSON.stringify(log.szczegóły)}'>
                                Zobacz
                            </button>
                        </td>
                        <td class="actions">
                            <button class="btn-danger usun-log-btn" data-id="${log.id}">
                                Usuń
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });

                dodajEventListenery();
            })
            .catch(error => console.error('Błąd pobierania logów:', error));
    }

    // =============== Usuwanie pojedynczego loga ===============
    function usunLog(idLoga) {
        if (!confirm('Czy na pewno chcesz usunąć ten log?')) {
            return;
        }

        fetch(`${baseUrl}/pages/logi_systemowe/usun_log_systemowy.php`, {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: idLoga })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Log został usunięty.');
                odswiezTabeleLogow();
            } else {
                alert(`Błąd usuwania loga: ${data.error}`);
            }
        })
        .catch(error => console.error('Błąd usuwania loga:', error));
    }

    // =============== Wyczyść wszystkie logi ===============
    function wyczyscWszystkieLogi() {
        if (!confirm('Czy na pewno chcesz wyczyścić wszystkie logi systemowe?')) {
            return;
        }

        fetch(`${baseUrl}/pages/logi_systemowe/wyczysc_logi_systemowe.php`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Wyczyszczono logi.');
                    odswiezTabeleLogow();
                } else {
                    alert(data.error || 'Wystąpił błąd podczas czyszczenia logów.');
                }
            })
            .catch(error => console.error('Błąd czyszczenia logów:', error));
    }

    // =============== Wyświetlanie szczegółów (JSON) w modalu ===============
    function pokazSzczegoly(jsonString) {
        // Próbujemy ładnie sformatować JSON:
        try {
            const obj = JSON.parse(jsonString);
            detailsContent.textContent = JSON.stringify(obj, null, 2);
        } catch (err) {
            // jeśli nie jest poprawny JSON
            detailsContent.textContent = jsonString;
        }

        detailsModal.style.display = 'block';
    }

    // =============== Dodawanie eventów do przycisków w tabeli ===============
    function dodajEventListenery() {
        document.querySelectorAll('.usun-log-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                usunLog(id);
            });
        });

        // Obsługa wyświetlania szczegółów
        document.querySelectorAll('.szczegoly-btn').forEach(button => {
            button.addEventListener('click', function() {
                const jsonStr = this.getAttribute('data-json');
                pokazSzczegoly(jsonStr);
            });
        });
    }

    // Obsługa modalowego wyjścia
    closeDetailsModalBtn.addEventListener('click', () => {
        detailsModal.style.display = 'none';
        detailsContent.textContent = '';
    });

    // Obsługa czyszczenia wszystkich logów
    if (clearLogsBtn) {
        clearLogsBtn.addEventListener('click', wyczyscWszystkieLogi);
    }

    // =============== Inicjalizacja ===============
    odswiezTabeleLogow();
});
