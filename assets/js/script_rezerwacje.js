document.addEventListener('DOMContentLoaded', function () {
    const addModal = document.getElementById('modal');
    const editModal = document.getElementById('editModal');
    const openAddModalBtn = document.getElementById('openModal');
    const closeAddModalBtn = document.getElementById('closeModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const addReservationBtn = document.getElementById('addReservationBtn');
    const addReservationForm = document.getElementById('addReservationForm');
    const tableBody = document.querySelector('.reservations-table tbody');
    let selectedRoomIds = new Set();
    const guestSelect = document.getElementById('guest');
    const addGuestSection = document.getElementById('addGuestSection');
    const roomOptionsContainer = document.getElementById('roomOptionsContainer');
    const roomFilters = {
        adults: document.getElementById('adults'),
        children: document.getElementById('children'),
        checkIn: document.getElementById('check_in'),
        checkOut: document.getElementById('check_out'),
        roomClass: document.getElementById('room_class')
    };

    // Wyświetlanie modala dodawania
    openAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'block';
        // Załaduj listę gości
        populateSelect(guestSelect, `${baseUrl}/pages/rezerwacje/pobierz_gosci.php`);
        loadAddons('addonsOptionsContainer', [], 'addons_add[]');
        fetchAvailableRooms();
        loadRoomClasses();
    });

    // Zamykanie modala dodawania
    closeAddModalBtn.addEventListener('click', () => {
        closeModal();
    });

    // Zamykanie modala dodawania po kliknięciu poza modalem
    window.addEventListener('click', (event) => {
        if (event.target === addModal) {
            closeModal();
        }
    });

    // Zamknięcie modala edycji
    closeEditModalBtn.addEventListener('click', () => {
        closeEditModal();
    });

    // Zamykanie modala edycji po kliknięciu poza modalem
    window.addEventListener('click', (event) => {
        if (event.target === editModal) {
            closeEditModal();
        }
    });

    // Funkcja zamykająca modal dodawania
    function closeModal() {
        addModal.style.display = 'none'; // Ukryj modal dodawania
        addReservationForm.reset();      // Zresetuj formularz dodawania
        addGuestSection.style.display = 'none'; // Ukryj sekcję dodawania nowego gościa
        roomOptionsContainer.innerHTML = '';    // Wyczyść opcje pokojów
        document.getElementById('amount').value = ''; // Wyczyść pole kwoty
    }

    // Funkcja zamykająca modal edycji
    function closeEditModal() {
        const editReservationForm = document.getElementById('editReservationForm');

        if (editModal) {
            editModal.style.display = 'none'; // Ukrycie modala
        }
        if (editReservationForm) {
            editReservationForm.reset(); // Resetowanie formularza edycji
        }
    }

    // Rozszerzenie modala o sekcję dodawania gościa
    guestSelect.addEventListener('change', function () {
        if (guestSelect.value === 'new') {
            addGuestSection.style.display = 'block';
        } else {
            addGuestSection.style.display = 'none';
        }
    });

    // Obsługa przycisku dodawania rezerwacji
    addReservationBtn.addEventListener('click', () => {
        // Pobierz dane z formularza
        const formData = new FormData(addReservationForm);

        const gosc_id = formData.get('guest'); // 'guest_add[]' lub 'guest_edit[]'?
        const pokoje = Array.from(document.querySelectorAll('input[name="rooms[]"]:checked')).map(input => parseInt(input.value));
        const start_date = formData.get('start_date');        // 'start_date'
        const end_date = formData.get('end_date');            // 'end_date'
        const liczba_doroslych = parseInt(formData.get('liczba_doroslych')) || 0;
        const liczba_dzieci = parseInt(formData.get('liczba_dzieci')) || 0;
        const dodatki = Array.from(document.querySelectorAll('input[name="addons_add[]"]:checked')).map(input => parseInt(input.value));

        // Logowanie wartości pól przed utworzeniem payload
        console.log('gosc_id:', gosc_id);
        console.log('pokoje:', pokoje);
        console.log('start_date:', start_date);
        console.log('end_date:', end_date);
        console.log('liczba_doroslych:', liczba_doroslych);
        console.log('liczba_dzieci:', liczba_dzieci);
        console.log('dodatki:', dodatki);

        if (gosc_id === 'new') {
            // Dane nowego gościa
            const newGuestData = {
                imie: formData.get('imie'),
                nazwisko: formData.get('nazwisko'),
                telefon: formData.get('telefon'),
                email: formData.get('email'),
                pokoje: pokoje,
                start_date: start_date,
                end_date: end_date,
                liczba_doroslych: liczba_doroslych,
                liczba_dzieci: liczba_dzieci,
                dodatki: dodatki
            };

            // Walidacja danych nowego gościa
            if (!newGuestData.imie || !newGuestData.nazwisko || !newGuestData.telefon || !newGuestData.email || pokoje.length === 0 || !start_date || !end_date || liczba_doroslych === 0) {
                alert('Proszę uzupełnić wszystkie wymagane pola.');
                return;
            }

            // Wywołanie funkcji dodania nowego gościa i rezerwacji
            fetch(`${baseUrl}/pages/rezerwacje/dodaj_goscia_oraz_rezerwacje.php`, { // Upewnij się, że baseUrl jest zdefiniowane
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(newGuestData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Nowy gość i rezerwacja zostały pomyślnie dodane.');
                        closeModal();
                        refreshReservationTable();
                    } else {
                        alert(`Błąd: ${data.error || 'Wystąpił problem podczas dodawania gościa i rezerwacji.'}`);
                    }
                })
                .catch(error => {
                    console.error('Błąd podczas dodawania gościa i rezerwacji:', error);
                    alert('Wystąpił błąd podczas dodawania gościa i rezerwacji.');
                });
        } else {
            // Logika dla istniejącego gościa
            const payload = {
                gosc_id: parseInt(gosc_id, 10),
                pokoje: pokoje,
                start_date: start_date,
                end_date: end_date,
                liczba_doroslych: liczba_doroslych,
                liczba_dzieci: liczba_dzieci,
                dodatki: dodatki
            };

            // Walidacja wymaganych pól
            if (!payload.gosc_id || !payload.pokoje.length || !payload.start_date || !payload.end_date || payload.liczba_doroslych === 0) {
                alert('Proszę uzupełnić wszystkie wymagane pola.');
                return;
            }

            // Logowanie payload
            console.log('Payload:', payload);

            // Wysłanie danych na backend
            fetch(`${baseUrl}/pages/rezerwacje/dodaj_rezerwacje.php`, { // Upewnij się, że baseUrl jest zdefiniowane
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rezerwacja została pomyślnie dodana.');
                        closeModal();
                        addReservationForm.reset();
                        refreshReservationTable();
                    } else {
                        alert(`Błąd: ${data.error || 'Wystąpił problem z dodaniem rezerwacji.'}`);
                    }
                })
                .catch(error => {
                    console.error('Wystąpił błąd podczas dodawania rezerwacji:', error);
                    alert('Wystąpił błąd podczas dodawania rezerwacji.');
                });
        }
    });

    // Funkcja do ładowania klas pokoi
    function loadRoomClasses() {
        fetch(`${baseUrl}/pages/pokoje/pobierz_klasy_pokoi.php`)
            .then(response => response.json())
            .then(data => {
                const roomClassSelect = roomFilters.roomClass;
                roomClassSelect.innerHTML = '<option value="">Wybierz klasę pokoju</option>';
                data.forEach(roomClass => {
                    const option = document.createElement('option');
                    option.value = roomClass.id;
                    option.textContent = roomClass.nazwa;
                    roomClassSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Błąd ładowania klas pokojów:', error));
    }

    /**
     * Funkcja do ładowania dodatków do określonego kontenera
     * @param {string} containerId - ID elementu kontenera
     * @param {Array} selectedAddons - Tablica ID wybranych dodatków (opcjonalne)
     * @param {string} addonName - Nazwa atrybutu 'name' dla checkboxów
     */
    function loadAddons(containerId, selectedAddons = [], addonName = 'addons[]') {
        fetch(`${baseUrl}/pages/rezerwacje/pobierz_dodatki.php`)
            .then(response => response.json())
            .then(data => {
                const addonsContainer = document.getElementById(containerId);
                addonsContainer.innerHTML = '';
                if (data.error) {
                    console.error('Błąd ładowania dodatków:', data.error);
                    addonsContainer.innerHTML = '<p>Błąd ładowania dodatków.</p>';
                    return;
                }
                if (data.length > 0) {
                    data.forEach(addon => {
                        const addonOption = document.createElement('div');
                        addonOption.className = 'addon-option';
                        addonOption.innerHTML = `
                            <input type="checkbox" id="addon-${addon.id}" name="${addonName}" value="${addon.id}" ${selectedAddons.includes(addon.id) ? 'checked' : ''}>
                            <label for="addon-${addon.id}">${addon.nazwa_dodatku} (${addon.cena} PLN)</label>
                        `;
                        addonsContainer.appendChild(addonOption);

                        // Dodanie event listenera do checkboxa
                        const checkbox = addonOption.querySelector(`input[name="${addonName}"]`);
                        if (containerId === 'editAddonsOptionsContainer') {
                            // Event listener dla modalu edycji
                            checkbox.addEventListener('change', updateAmountForEditModal);
                        } else {
                            // Event listener dla modalu dodawania
                            checkbox.addEventListener('change', calculateCost);
                        }
                    });
                } else {
                    addonsContainer.innerHTML = '<p>Brak dostępnych dodatków.</p>';
                }
            })
            .catch(error => {
                console.error('Błąd pobierania dodatków:', error);
                const addonsContainer = document.getElementById(containerId);
                addonsContainer.innerHTML = '<p>Błąd pobierania dodatków.</p>';
            });
    }

    function handleRoomSelectionChange(event) {
        const roomId = event.target.value;

        if (event.target.checked) {
            selectedRoomIds.add(roomId);
        } else {
            selectedRoomIds.delete(roomId);
        }

        // Zaktualizuj koszty po każdej zmianie
        calculateCost();
    }

    // Funkcja do odświeżania dostępnych pokoi
    function fetchAvailableRooms() {
        const filters = {
            adults: parseInt(roomFilters.adults.value || 0, 10),
            children: parseInt(roomFilters.children.value || 0, 10),
            checkIn: roomFilters.checkIn.value || null,
            checkOut: roomFilters.checkOut.value || null,
            roomClass: roomFilters.roomClass.value || null
        };

        fetch(`${baseUrl}/pages/rezerwacje/fetch_dostepne_pokoje.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(filters)
        })
            .then(response => response.json())
            .then(data => {
                roomOptionsContainer.innerHTML = '';
                if (data.rooms && data.rooms.length > 0) {
                    data.rooms.forEach(room => {
                        const roomOption = document.createElement('div');
                        roomOption.className = 'room-option';
                        roomOption.innerHTML = `
                            <input type="checkbox" id="room-${room.pokoj_id}" name="rooms[]" value="${room.pokoj_id}">
                            <label for="room-${room.pokoj_id}">
                                ${room.numer_pokoju} (${room.nazwa_klasy}, max ${room.max_liczba_osob} osób)
                            </label>
                        `;
                        roomOptionsContainer.appendChild(roomOption);
                    });

                    // Dodaj event listener do każdego checkboxa
                    document.querySelectorAll('input[name="rooms[]"]').forEach(checkbox => {
                        checkbox.addEventListener('change', handleRoomSelectionChange);
                        if (checkbox.checked) {
                            selectedRoomIds.add(checkbox.value); // Dodaj już zaznaczone do zbioru
                        }
                    });
                } else {
                    roomOptionsContainer.innerHTML = '<p>Brak dostępnych pokoi spełniających kryteria.</p>';
                }
            })
            .catch(error => console.error('Błąd pobierania dostępnych pokoi:', error));
    }

    // Wyszukiwanie pokoi po zmianie filtrów
    Object.values(roomFilters).forEach(filter => {
        filter.addEventListener('change', fetchAvailableRooms);
    });

    roomFilters.checkIn.addEventListener('change', validateDates);
    roomFilters.checkOut.addEventListener('change', validateDates);

    function validateDates() {
        const checkInDate = new Date(roomFilters.checkIn.value);
        const checkOutDate = new Date(roomFilters.checkOut.value);

        if (checkOutDate < checkInDate) {
            alert('Data wymeldowania nie może być wcześniejsza niż data zameldowania. Proszę poprawić daty.');
            roomFilters.checkOut.value = '';
        }
    }

    document.getElementById('edit_check_in').addEventListener('change', validateEditDates);
    document.getElementById('edit_check_out').addEventListener('change', validateEditDates);

    function validateEditDates() {
        const editCheckInDate = new Date(document.getElementById('edit_check_in').value);
        const editCheckOutDate = new Date(document.getElementById('edit_check_out').value);

        if (editCheckOutDate < editCheckInDate) {
            alert('Data wymeldowania nie może być wcześniejsza niż data zameldowania. Proszę poprawić daty.');
            document.getElementById('edit_check_out').value = '';
        }
    }


    /**
     * Funkcja do ładowania dostępnych pokoi w modalu edycji
     * @param {string} checkIn - Data zameldowania
     * @param {string} checkOut - Data wymeldowania
     * @param {number} roomClass - ID klasy pokoju
     * @param {number} selectedRoomId - ID aktualnie przypisanego pokoju
     * @param {string} roomOptionsContainerId - ID kontenera na opcje pokoi
     */
    function fetchAvailableRoomsForEdit(checkIn, checkOut, roomClass, selectedRoomId, roomOptionsContainerId) {
        const filters = {
            checkIn: checkIn || null,
            checkOut: checkOut || null,
            roomClass: roomClass || null
        };

        fetch(`${baseUrl}/pages/rezerwacje/pobierz_dostepne_pokoje.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(filters)
        })
            .then(response => response.json())
            .then(data => {
                const roomOptionsContainer = document.getElementById(roomOptionsContainerId);
                roomOptionsContainer.innerHTML = '';

                // Tablica na wszystkie pokoje (w tym aktualnie zarezerwowany)
                const allRooms = [];

                // Dodaj wszystkie dostępne pokoje do tablicy
                if (data.rooms && data.rooms.length > 0) {
                    allRooms.push(...data.rooms.map(room => ({
                        pokoj_id: Number(room.pokoj_id),
                        numer_pokoju: room.numer_pokoju,
                        nazwa_klasy: room.nazwa_klasy,
                        max_liczba_osob: room.max_liczba_osob,
                        selected: Number(room.pokoj_id) === Number(selectedRoomId)
                    })));
                }

                console.log('Dane zwrócone z backendu dla dostępnych pokoi:', data.rooms);


                // Pobierz dane aktualnie zarezerwowanego pokoju, jeśli nie jest wśród dostępnych
                const fetchSelectedRoom = selectedRoomId && !data.rooms.find(room => Number(room.pokoj_id) === Number(selectedRoomId))
                    ? fetch(`${baseUrl}/pages/rezerwacje/pobierz_szczegoly_pokoju.php?id=${selectedRoomId}`)
                          .then(response => response.json())
                          .then(selectedRoomData => {
                              if (selectedRoomData.success) {
                                  const room = selectedRoomData.room;
                                  console.log('Fetched selected room:', room); // Debugging
                                  allRooms.push({
                                      pokoj_id: Number(room.id),
                                      numer_pokoju: room.numer_pokoju,
                                      nazwa_klasy: room.nazwa_klasy,
                                      max_liczba_osob: room.max_liczba_osob,
                                      selected: true
                                  });
                              } else {
                                  console.error('Błąd pobierania szczegółów pokoju:', selectedRoomData.error);
                              }
                          })
                          .catch(error => console.error('Błąd pobierania szczegółów pokoju:', error))
                    : Promise.resolve();

                // Po zakończeniu pobierania danych
                fetchSelectedRoom.then(() => {
                    // Sortuj pokoje według numeru pokoju
                    allRooms.sort((a, b) => a.numer_pokoju - b.numer_pokoju);

                    // Logowanie wszystkich pokoi przed renderowaniem
                    console.log('All rooms to render:', allRooms);

                    // Renderuj posortowane pokoje
                    allRooms.forEach(room => {
                        const isChecked = room.selected ? 'checked' : '';
                        const roomOption = document.createElement('div');
                        roomOption.className = 'room-option';
                        roomOption.innerHTML = `
                            <input type="radio" id="room-${room.pokoj_id}" name="edit-room" value="${room.pokoj_id}" ${isChecked}>
                            <label for="room-${room.pokoj_id}">
                                ${room.numer_pokoju} (${room.nazwa_klasy}, max ${room.max_liczba_osob} osób)
                            </label>
                        `;

                        roomOptionsContainer.appendChild(roomOption);
                    });

                    // Dodaj event listener do pokoi w edycji
                    // Używamy delegacji zdarzeń zamiast dodawania listenerów do każdego radio
                    roomOptionsContainer.addEventListener('change', function(event) {
                        if (event.target && event.target.matches('input[name="edit-room"]')) {
                            updateAmountForEditModal();
                        }
                    });

                    // Sprawdź, czy pokój został zaznaczony
                    const isAnyRoomSelected = allRooms.some(room => room.selected);
                    if (!isAnyRoomSelected && allRooms.length > 0) {
                        // Jeśli żaden pokój nie jest zaznaczony, zaznacz pierwszy z listy
                        const firstRadio = roomOptionsContainer.querySelector('input[name="edit-room"]');
                        if (firstRadio) {
                            firstRadio.checked = true;
                        }
                    }

                    // Opcjonalnie: Aktualizuj koszty po zaznaczeniu pokoju
                    updateAmountForEditModal();
                });
            })
            .catch(error => console.error('Błąd pobierania dostępnych pokoi dla edycji:', error));
    }

    // Funkcja do obliczania kosztów dla modalu dodawania
    function calculateCost() {
        const roomIds = Array.from(document.querySelectorAll('input[name="rooms[]"]:checked')).map(input => parseInt(input.value));
        const checkInDate = roomFilters.checkIn.value;
        const checkOutDate = roomFilters.checkOut.value;
        const selectedAddons = Array.from(document.querySelectorAll('input[name="addons_add[]"]:checked')).map(input => parseInt(input.value));

        // Sprawdź, czy wymagane dane są dostępne
        if (!roomIds.length || !checkInDate || !checkOutDate) {
            document.getElementById('amount').value = "Brak danych do obliczenia";
            return;
        }

        // Pokaż wskaźnik ładowania
        document.getElementById('amount').value = "Ładowanie...";

        // Wysłanie żądania do backendu
        fetch(`${baseUrl}/pages/rezerwacje/oblicz_koszty.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ roomIds, checkInDate, checkOutDate, addons: selectedAddons })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formattedCost = parseFloat(data.totalCost).toFixed(2);
                    document.getElementById('amount').value = `${formattedCost} PLN`;
                } else {
                    document.getElementById('amount').value = "Błąd obliczania";
                    console.error('Błąd obliczania kosztów:', data.error);
                }
            })
            .catch(error => {
                document.getElementById('amount').value = "Błąd serwera";
                console.error('Błąd serwera:', error);
            });
    }

    // Funkcja do obliczania kosztów w modalu edycji
    function updateAmountForEditModal() {
        const selectedRoomId = document.querySelector('input[name="edit-room"]:checked')?.value;
        const checkInDate = document.getElementById('edit_check_in').value;
        const checkOutDate = document.getElementById('edit_check_out').value;
        const selectedAddons = Array.from(document.querySelectorAll('#editAddonsOptionsContainer input[name="addons_edit[]"]:checked')).map(input => parseInt(input.value, 10));

        console.log('Selected Room ID:', selectedRoomId);
        const selectedRoomRadio = document.querySelector('input[name="edit-room"]:checked');
        console.log('Znaleziony element pokoju:', selectedRoomRadio);

        console.log('Check-In Date:', checkInDate);
        console.log('Check-Out Date:', checkOutDate);
        console.log('Selected Addons:', selectedAddons);

        // Sprawdź, czy wymagane dane są dostępne
        if (!selectedRoomId || !checkInDate || !checkOutDate) {
            document.getElementById('edit_amount').value = "Brak danych do obliczenia";
            return;
        }

        // Pokaż wskaźnik ładowania
        document.getElementById('edit_amount').value = "Ładowanie...";

        // Wysłanie żądania do backendu
        fetch(`${baseUrl}/pages/rezerwacje/oblicz_koszty_edit.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                roomIds: [Number(selectedRoomId)],
                checkInDate: checkInDate,
                checkOutDate: checkOutDate,
                addons: selectedAddons
            }),
        })
            .then(response => response.json())
            .then(costData => {
                console.log('Cost Data:', costData); // Debugowanie
                if (costData.success) {
                    const formattedCost = parseFloat(costData.totalCost).toFixed(2);
                    document.getElementById('edit_amount').value = `${formattedCost} PLN`;
                } else {
                    document.getElementById('edit_amount').value = 'Nie udało się obliczyć kosztu';
                    console.error('Błąd obliczania:', costData.error);
                }
            })
            .catch(error => {
                document.getElementById('edit_amount').value = 'Błąd serwera';
                console.error('Błąd serwera:', error);
            });
    }

    // Funkcja do odświeżania tabeli rezerwacji
    function refreshReservationTable() {
        fetch(`${baseUrl}/pages/rezerwacje/odswiez_tabele_rezerwacji.php`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data)) {
                    console.error('Nieprawidłowy format danych:', data);    
                    alert(data.error || 'Wystąpił nieznany błąd.');
                    return;
                }

                tableBody.innerHTML = ''; // Czyszczenie tabeli

                data.forEach((reservation, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${reservation.id}</td>
                        <td>${reservation.guest}</td>
                        <td>${reservation.room}</td>
                        <td>${reservation.check_in}</td>
                        <td>${reservation.check_out}</td>
                        <td>${reservation.reservation_status}</td>
                        <td>${reservation.payment_status}</td>
                        <td>${reservation.amount} PLN</td>
                        <td class="actions">
                            ${generateActions(reservation)}
                        </td>
                    `;
                    tableBody.appendChild(row);
                });

                // Dodanie event listenerów do przycisków akcji
                attachEventListeners();
            })
            .catch(error => console.error('Błąd odświeżania tabeli rezerwacji:', error));
    }
    
    // Funkcja generująca przyciski akcji w zależności od warunków
    function generateActions(reservation) {
        const today = new Date();
        const checkInDate = new Date(reservation.check_in);
        const daysDifference = Math.floor((checkInDate - today) / (1000 * 60 * 60 * 24)); // Różnica w dniach

        // Tworzenie przycisków
        let actions = `
            <button class="btn btn-secondary edit-btn" data-id="${reservation.id}">Edytuj</button>
        `;

        // Warunek dla przycisku "Wykwateruj": Status rezerwacji to "W trakcie" i status płatności to "Zrealizowana"
        if (reservation.reservation_status === "W trakcie" && reservation.payment_status === "Zrealizowana") {
            actions += `
                <button class="btn btn-success checkout-btn" data-id="${reservation.id}">Wykwateruj</button>
            `;
        } else {
            actions += `
                <button class="btn btn-success checkout-btn disabled" data-id="${reservation.id}" disabled>Wykwateruj</button>
            `;
        }
        
        // Warunek dla przycisku "Usuń": Dostępny tylko 30 dni przed datą zameldowania
        if (daysDifference >= 31) {
            actions += `
                <button class="btn btn-danger delete-btn" data-id="${reservation.id}">Usuń</button>
            `;
        } else {
            actions += `
                <button class="btn btn-danger delete-btn disabled" data-id="${reservation.id}" disabled>Usuń</button>
            `;
        }    
        return actions;
    }

    // Funkcja otwierająca modal edycji rezerwacji
    function openEditModal(reservationId) {
        fetch(`${baseUrl}/pages/rezerwacje/pobierz_szczegoly_rezerwacji.php?id=${reservationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const reservation = data.reservation;

                    // Poprawne mapowanie selected_addons
                    const selectedAddons = reservation.selected_addons ? reservation.selected_addons.map(addon => Number(addon.id)) : [];

                    // Pobierz elementy formularza edycji
                    const reservationIdElement = document.getElementById('edit-reservation-id');
                    const checkInInput = document.getElementById('edit_check_in');
                    const checkOutInput = document.getElementById('edit_check_out');
                    const guestSelect = document.getElementById('edit_guest');
                    const roomOptionsContainer = document.getElementById('modalRoomOptionsContainer');
                    const paymentStatusSelect = document.getElementById('edit_payment_status');
                    const amountInput = document.getElementById('edit_amount');
                    const reservationStatusSelect = document.getElementById('edit_reservation_status');
                    const addonsOptionsContainer = document.getElementById('editAddonsOptionsContainer');

                    // Ustaw wartości w polach formularza
                    if (reservationIdElement) reservationIdElement.textContent = reservation.id || '';
                    if (checkInInput) checkInInput.value = reservation.check_in || '';
                    if (checkOutInput) checkOutInput.value = reservation.check_out || '';
                    if (guestSelect) {
                        populateSelectWithDefault(
                            guestSelect,
                            `${baseUrl}/pages/rezerwacje/pobierz_gosci.php`,
                            reservation.guest_id
                        );
                    }
                    if (roomOptionsContainer) {
                        // Pobierz room_id z obiektu room
                        const selectedRoom = reservation.room;
                        console.log('Selected Room:', selectedRoom); // Debugging

                        fetchAvailableRoomsForEdit(
                            reservation.check_in,
                            reservation.check_out,
                            null, // Jeśli nie masz room_class_id, możesz przekazać null lub pobrać go osobno
                            selectedRoom.room_id,       // Pobierz room_id z obiektu room
                            'modalRoomOptionsContainer'
                        );
                    }
                    if (reservationStatusSelect) {
                        populateSelectWithDefault(
                            reservationStatusSelect,
                            `${baseUrl}/pages/rezerwacje/pobierz_status_rezerwacji.php`,
                            reservation.reservation_status_id
                        );
                    }
                    if (paymentStatusSelect) {
                        populateSelectWithDefault(
                            paymentStatusSelect,
                            `${baseUrl}/pages/rezerwacje/pobierz_status_platnosci.php`,
                            reservation.payment_status_id
                        );
                    }
                    if (amountInput) {
                        amountInput.value = reservation.amount ? `${parseFloat(reservation.amount).toFixed(2)} PLN` : '0.00 PLN';
                    }

                    // Załaduj dodatki przypisane do rezerwacji
                    loadAddons('editAddonsOptionsContainer', selectedAddons, 'addons_edit[]');

                    // Pokaż modal
                    editModal.style.display = 'block';
                } else {
                    alert(`Błąd pobierania szczegółów rezerwacji: ${data.error}`);
                }
            })
            .catch(error => console.error('Błąd pobierania szczegółów rezerwacji:', error));
    }

    // Funkcja do ładowania gości
    function populateSelect(selectElement, url) {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Sprawdź, czy są błędy w odpowiedzi
                if (data.error) {
                    console.error('Błąd ładowania danych:', data.error);
                    alert('Wystąpił błąd ładowania listy gości.');
                    return;
                }

                // Wyczyść istniejące opcje
                selectElement.innerHTML = `
                    <option value="">Wybierz gościa</option>
                    <option value="new">Dodaj nowego gościa</option>
                `;

                // Dodaj opcje z backendu
                data.forEach(guest => {
                    const option = document.createElement('option');
                    option.value = guest.id;
                    option.textContent = guest.nazwa;
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Błąd pobierania danych:', error));
    }

    // Funkcja do ładowania opcji z domyślną wartością
    function populateSelectWithDefault(selectElement, url, defaultValue) {
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

                // Dodaj opcje do selecta
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nazwa || item.name;
                    if (item.id === defaultValue) {
                        option.selected = true; // Zaznacz domyślną wartość
                    }
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Błąd pobierania danych:', error));
    }    

    // Funkcja do usuwania rezerwacji
    function deleteReservation(id) {
        if (confirm('Czy na pewno chcesz usunąć tę rezerwację?')) {
            fetch(`${baseUrl}/pages/rezerwacje/usun_rezerwacje.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rezerwacja została usunięta.');
                        refreshReservationTable();
                    } else {
                        alert(`Błąd usuwania: ${data.error}`);
                    }
                })
                .catch(error => console.error('Błąd usuwania rezerwacji:', error));
        }
    }

    // Funkcja zapisywania zmian w rezerwacji
    function saveReservationChanges() {
        const reservationId = document.getElementById('edit-reservation-id').textContent.trim();
        const guestId = document.getElementById('edit_guest').value;
        const roomId = document.querySelector('input[name="edit-room"]:checked')?.value;
        const checkInDate = document.getElementById('edit_check_in').value;
        const checkOutDate = document.getElementById('edit_check_out').value;
        const paymentStatus = document.getElementById('edit_payment_status').value;
        const amount = parseFloat(document.getElementById('edit_amount').value.replace(' PLN', '').trim()) || 0;
        const reservationStatus = document.getElementById('edit_reservation_status').value;
        const selectedAddons = Array.from(document.querySelectorAll('#editAddonsOptionsContainer input[name="addons_edit[]"]:checked')).map(input => parseInt(input.value));

        // Walidacja wymaganych pól
        if (!reservationId || !guestId || !roomId || !checkInDate || !checkOutDate || !paymentStatus || !reservationStatus) {
            alert('Proszę uzupełnić wszystkie wymagane pola.');
            return;
        }

        // Przygotowanie danych do wysłania
        const payload = {
            reservation_id: parseInt(reservationId, 10),
            guest_id: parseInt(guestId, 10),
            room_id: parseInt(roomId, 10),
            check_in_date: checkInDate,
            check_out_date: checkOutDate,
            payment_status_id: parseInt(paymentStatus, 10),
            reservation_status_id: parseInt(reservationStatus, 10),
            amount: amount,
            addons: selectedAddons // Dodanie dodatków do payload
        };

        // Logowanie payload
        console.log('Payload:', payload);

        // Wysłanie danych na backend
        fetch(`${baseUrl}/pages/rezerwacje/zaktualizuj_rezerwacje.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert('Rezerwacja została pomyślnie zaktualizowana.');
                    closeEditModal();
                    refreshReservationTable(); // Odświeżenie tabeli rezerwacji
                } else {
                    alert(`Błąd aktualizacji: ${data.error || 'Nie udało się zaktualizować rezerwacji.'}`);
                    closeEditModal();
                    refreshReservationTable();
                }
            })
            .catch((error) => {
                console.error('Błąd podczas aktualizacji rezerwacji:', error);
                alert('Wystąpił błąd podczas aktualizacji rezerwacji.');
            });
    }    

    // Podłącz zdarzenie kliknięcia do przycisku "Zapisz zmiany"
    document.getElementById('saveEditBtn').addEventListener('click', saveReservationChanges);    

    // Funkcja do obsługi przycisku "Wykwateruj"
    function checkoutReservation(reservationId) {
        if (confirm('Czy na pewno chcesz wykwaterować tę rezerwację?')) {
            // Wywołanie funkcji na backendzie
            fetch(`${baseUrl}/pages/rezerwacje/wykwateruj_rezerwacje.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reservationId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rezerwacja została pomyślnie wykwaterowana.');
                        refreshReservationTable(); // Odśwież tabelę rezerwacji
                    } else {
                        alert(`Błąd wykwaterowania: ${data.error}`);
                    }
                })
                .catch(error => {
                    console.error('Błąd wykwaterowania:', error);
                    alert('Wystąpił problem podczas wykwaterowywania rezerwacji.');
                });
        }
    }


    // Funkcja do dodawania event listenerów do przycisków akcji
    function attachEventListeners() {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                openEditModal(id);
            });
        });

        document.querySelectorAll('.checkout-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                checkoutReservation(id);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                if (!this.classList.contains('disabled')) {
                    deleteReservation(id);
                }
            });
        });

        document.querySelectorAll('input[name="rooms[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', handleRoomSelectionChange);
            if (checkbox.checked) {
                selectedRoomIds.add(checkbox.value); // Dodaj już zaznaczone do zbioru
            }
        });
        
        roomFilters.checkIn.addEventListener('change', calculateCost);
        roomFilters.checkOut.addEventListener('change', calculateCost);
        
    }

    // Delegacja zdarzeń dla checkboxów dodatków w modalu dodawania i edycji
    // Dla modalu dodawania
    document.getElementById('addonsOptionsContainer').addEventListener('change', function(event) {
        if (event.target && event.target.matches('input[name="addons_add[]"]')) {
            const selectedAddons = Array.from(
                document.querySelectorAll('input[name="addons_add[]"]:checked')
            ).map(input => parseInt(input.value, 10));
            console.log('Zaznaczone dodatki w dodawaniu:', selectedAddons);
            calculateCost();
        }
    });

    // Dla modalu edycji
    document.getElementById('editAddonsOptionsContainer').addEventListener('change', function(event) {
        if (event.target && event.target.matches('input[name="addons_edit[]"]')) {
            const selectedAddons = Array.from(
                document.querySelectorAll('#editAddonsOptionsContainer input[name="addons_edit[]"]:checked')
            ).map(input => parseInt(input.value, 10));
            console.log('Zaznaczone dodatki w edycji:', selectedAddons);
            updateAmountForEditModal();
        }
    });

    // Odświeżenie tabeli na starcie
    refreshReservationTable();
});
