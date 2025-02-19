document.addEventListener('DOMContentLoaded', function () {
    const addModal = document.getElementById('modal');
    const editModal = document.getElementById('editModal');
    const openAddModalBtn = document.getElementById('openModal');
    const closeAddModalBtn = document.getElementById('closeModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const addRoomBtn = document.getElementById('addRoomBtn');
    const saveChangesBtn = document.getElementById('save-changes');
    const addRoomForm = document.getElementById('addRoomForm');

    // Funkcja do odświeżania tabeli pokoi
    function refreshRoomTable() {
        fetch(`${baseUrl}/pages/pokoje/odswiez_tabele_pokoi.php`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Błąd odświeżania tabeli pokoi:', data.error);
                    alert('Wystąpił błąd podczas ładowania danych pokoi.');
                    return;
                }

                const tbody = document.querySelector('.rooms-table tbody');
                tbody.innerHTML = ''; // Czyszczenie tabeli przed załadowaniem nowych danych
                data.forEach((room, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${room.numer_pokoju}</td>
                        <td>${room.nazwa_klasy}</td>
                        <td>${room.nazwa_statusu}</td>
                        <td>${room.numer_piętra}</td>
                        <td>${room.max_liczba_osob}</td>
                        <td class="actions">
                            <button class="btn btn-secondary edit-btn" data-room='${JSON.stringify(room)}'>Edytuj</button>
                            <button class="btn btn-danger delete-btn ${room.can_delete ? '' : 'disabled'}" data-id="${room.id}" ${room.can_delete ? '' : 'disabled'}>Usuń</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // Dodanie zdarzeń do przycisków edycji i usuwania
                document.querySelectorAll('.edit-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const room = JSON.parse(this.getAttribute('data-room'));
                        openEditModal(room);
                    });
                });

                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const roomId = this.getAttribute('data-id');
                        if (!this.classList.contains('disabled')) {
                            deleteRoom(roomId);
                        }
                    });
                });
            })
            .catch(error => console.error('Błąd odświeżania tabeli pokoi:', error));
    }

    // Funkcja otwierająca modal edycji
    function openEditModal(room) {
        document.getElementById('edit-id').textContent = room.id;
        document.getElementById('edit-room-number').value = room.numer_pokoju;

        const roomClassSelect = document.getElementById('edit-room-class');
        const roomStatusSelect = document.getElementById('edit-room-status');
        const floorSelect = document.getElementById('edit-floor');

        populateSelectWithDefault(roomClassSelect, `${baseUrl}/pages/pokoje/pobierz_klasy_pokoi.php`, room.klasa_pokoju_id);
        populateSelectWithDefault(roomStatusSelect, `${baseUrl}/pages/pokoje/pobierz_status_pokoju.php`, room.status_pokoju_id);
        populateSelectWithDefault(floorSelect, `${baseUrl}/pages/pokoje/pobierz_pietra.php`, room.piętro_id);

        // Ustawienie maksymalnej liczby osób
        document.getElementById('edit-max-osob').textContent = room.max_liczba_osob;

        editModal.style.display = 'block';
    }

    // Funkcja usuwania pokoju
    function deleteRoom(roomId) {
        if (confirm('Czy na pewno chcesz usunąć ten pokój?')) {
            fetch(`${baseUrl}/pages/pokoje/usun_pokoj.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: roomId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pokój został pomyślnie usunięty.');
                        refreshRoomTable();
                    } else {
                        alert(`Błąd podczas usuwania pokoju: ${data.error}`);
                    }
                })
                .catch(error => console.error('Błąd usuwania pokoju:', error));
        }
    }

    // Funkcja wypełniania select z domyślną wartością
    function populateSelectWithDefault(selectElement, url, defaultValue = null) {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Błąd ładowania danych:', data.error);
                    alert('Wystąpił błąd ładowania danych.');
                    return;
                }

                // Wyczyść istniejące opcje
                selectElement.innerHTML = '';

                // Dodaj domyślną opcję
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Wybierz...';
                selectElement.appendChild(defaultOption);

                // Dodaj opcje do selecta
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nazwa || item.numer_piętra;
                    if (item.id == defaultValue) {
                        option.selected = true; // Zaznacz domyślną wartość
                    }
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Błąd pobierania danych:', error));
    }

    // Obsługa przycisku "Dodaj nowy pokój"
    openAddModalBtn.addEventListener('click', () => {
        populateSelectWithDefault(document.getElementById('floor'), `${baseUrl}/pages/pokoje/pobierz_pietra.php`);
        populateSelectWithDefault(document.getElementById('room_class'), `${baseUrl}/pages/pokoje/pobierz_klasy_pokoi.php`);
        populateSelectWithDefault(document.getElementById('room_status'), `${baseUrl}/pages/pokoje/pobierz_status_pokoju.php`);
        addModal.style.display = 'block';
    });

    // Dodawanie pokoju
    addRoomBtn.addEventListener('click', () => {
        const formData = new FormData(addRoomForm);
        const payload = Object.fromEntries(formData.entries());

        fetch(`${baseUrl}/pages/pokoje/dodaj_pokoj_action.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pokój został pomyślnie dodany.');
                    addModal.style.display = 'none';
                    addRoomForm.reset();
                    refreshRoomTable();
                } else {
                    alert(`Błąd dodawania pokoju: ${data.error}`);
                }
            })
            .catch(error => console.error('Błąd dodawania pokoju:', error));
    });

    // Zapisywanie zmian w edytowanym pokoju
    saveChangesBtn.addEventListener('click', () => {
        const id = document.getElementById('edit-id').textContent;
        const roomNumber = document.getElementById('edit-room-number').value;
        const roomClass = document.getElementById('edit-room-class').value;
        const roomStatus = document.getElementById('edit-room-status').value;
        const floor = document.getElementById('edit-floor').value;

        const payload = {
            id,
            room_number: roomNumber,
            room_class: roomClass,
            room_status: roomStatus,
            floor
        };

        fetch(`${baseUrl}/pages/pokoje/zaktualizuj_pokoj.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Zmiany zostały zapisane.');
                    editModal.style.display = 'none';
                    refreshRoomTable();
                } else {
                    alert(`Błąd podczas zapisywania zmian: ${data.error}`);
                }
            })
            .catch(error => console.error('Błąd edycji pokoju:', error));
    });

    // Zamknięcie modali
    closeAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'none';
    });

    closeEditModalBtn.addEventListener('click', () => {
        editModal.style.display = 'none';
    });

    // Zamknięcie modali po kliknięciu poza nimi
    window.addEventListener('click', function(event) {
        if (event.target === addModal) {
            addModal.style.display = 'none';
        }
        if (event.target === editModal) {
            editModal.style.display = 'none';
        }
    });

    // Odświeżenie tabeli na starcie
    refreshRoomTable();
});
