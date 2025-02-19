document.addEventListener('DOMContentLoaded', function () {
    const publicReservationForm = document.getElementById('publicReservationForm');
    const publicRoomClassSelect = document.getElementById('public_room_class');
    const publicRoomOptionsContainer = document.getElementById('public_roomOptionsContainer');
    const publicAddReservationBtn = document.getElementById('public_addReservationBtn');
    const publicAmountInput = document.getElementById('public_amount');

    // Funkcja do ładowania klas pokoi
    function loadRoomClasses(selectElement) {
        fetch(`${baseUrl}/pages/pokoje/pobierz_klasy_pokoi.php`)
            .then(response => response.json())
            .then(data => {
                selectElement.innerHTML = '<option value="">Wybierz klasę pokoju</option>';
                data.forEach(roomClass => {
                    const option = document.createElement('option');
                    option.value = roomClass.id;
                    option.textContent = roomClass.nazwa;
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Błąd ładowania klas pokojów:', error));
    }

    // Funkcja do ładowania dodatków
    function loadAddons(containerId, selectedAddons = [], addonName = 'addons_add[]') {
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
                        checkbox.addEventListener('change', calculatePublicCost);
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

    // Funkcja do ładowania dostępnych pokoi na podstawie filtrów
    function fetchAvailableRooms() {
        const filters = {
            adults: parseInt(document.getElementById('public_adults').value || 0, 10),
            children: parseInt(document.getElementById('public_children').value || 0, 10),
            checkIn: document.getElementById('public_check_in').value || null,
            checkOut: document.getElementById('public_check_out').value || null,
            roomClass: document.getElementById('public_room_class').value || null
        };

        fetch(`${baseUrl}/pages/rezerwacje/fetch_dostepne_pokoje.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(filters)
        })
            .then(response => response.json())
            .then(data => {
                publicRoomOptionsContainer.innerHTML = '';
                if (data.rooms && data.rooms.length > 0) {
                    data.rooms.forEach(room => {
                        const roomOption = document.createElement('div');
                        roomOption.className = 'room-option';
                        roomOption.innerHTML = `
                            <input type="checkbox" id="public_room-${room.pokoj_id}" name="rooms[]" value="${room.pokoj_id}">
                            <label for="public_room-${room.pokoj_id}">
                                ${room.numer_pokoju} (${room.nazwa_klasy}, max ${room.max_liczba_osob} osób)
                            </label>
                        `;
                        publicRoomOptionsContainer.appendChild(roomOption);
                    });

                    // Dodaj event listener do każdego checkboxa
                    document.querySelectorAll('input[name="rooms[]"]').forEach(checkbox => {
                        checkbox.addEventListener('change', calculatePublicCost);
                    });
                } else {
                    publicRoomOptionsContainer.innerHTML = '<p>Brak dostępnych pokoi spełniających kryteria.</p>';
                }
            })
            .catch(error => console.error('Błąd pobierania dostępnych pokoi:', error));
    }

    // Funkcja do obliczania kosztów rezerwacji
    function calculatePublicCost() {
        const roomIds = Array.from(document.querySelectorAll('input[name="rooms[]"]:checked')).map(input => parseInt(input.value));
        const checkInDate = document.getElementById('public_check_in').value;
        const checkOutDate = document.getElementById('public_check_out').value;
        const selectedAddons = Array.from(document.querySelectorAll('input[name="addons_add[]"]:checked')).map(input => parseInt(input.value));

        // Sprawdź, czy wymagane dane są dostępne
        if (!roomIds.length || !checkInDate || !checkOutDate) {
            publicAmountInput.value = "Brak danych do obliczenia";
            return;
        }

        // Pokaż wskaźnik ładowania
        publicAmountInput.value = "Ładowanie...";

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
                    publicAmountInput.value = `${formattedCost} PLN`;
                } else {
                    publicAmountInput.value = "Błąd obliczania";
                    console.error('Błąd obliczania kosztów:', data.error);
                }
            })
            .catch(error => {
                publicAmountInput.value = "Błąd serwera";
                console.error('Błąd serwera:', error);
            });
    }

    // Funkcja do obsługi przycisku dodawania rezerwacji publicznej
    publicAddReservationBtn.addEventListener('click', () => {
        // Pobierz dane z formularza
        const formData = new FormData(publicReservationForm);

        const imie = formData.get('imie').trim();
        const nazwisko = formData.get('nazwisko').trim();
        const telefon = formData.get('telefon').trim();
        const email = formData.get('email').trim();
        const liczba_doroslych = parseInt(formData.get('liczba_doroslych')) || 0;
        const liczba_dzieci = parseInt(formData.get('liczba_dzieci')) || 0;
        const start_date = formData.get('start_date');
        const end_date = formData.get('end_date');
        const klasa_pokoju_id = parseInt(formData.get('klasa_pokoju_id')) || null;
        const pokoje = Array.from(document.querySelectorAll('input[name="rooms[]"]:checked')).map(input => parseInt(input.value));
        const dodatki = Array.from(document.querySelectorAll('input[name="addons_add[]"]:checked')).map(input => parseInt(input.value));
        const amount = publicAmountInput.value.replace(' PLN', '').trim();

        // Walidacja danych
        if (!imie || !nazwisko || !telefon || !email || !liczba_doroslych || !start_date || !end_date || !klasa_pokoju_id || pokoje.length === 0) {
            alert('Proszę uzupełnić wszystkie wymagane pola.');
            return;
        }

        if (!validateEmail(email)) {
            alert('Proszę podać poprawny adres email.');
            return;
        }

        // Przygotowanie danych do wysłania
        const reservationData = {
            imie,
            nazwisko,
            telefon,
            email,
            liczba_doroslych,
            liczba_dzieci,
            start_date,
            end_date,
            klasa_pokoju_id,
            pokoje,
            dodatki,
            amount: parseFloat(amount) || 0
        };

        // Wysłanie danych do backendu
        fetch(`${baseUrl}/pages/rezerwacje/dodaj_rezerwacje_public.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(reservationData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Rezerwacja została pomyślnie dodana.');
                    publicReservationForm.reset();
                    publicRoomOptionsContainer.innerHTML = '';
                    // publicAddGuestSection.style.display = 'none';
                    publicAmountInput.value = '';
                } else {
                    alert(`Błąd: ${data.error || 'Wystąpił problem z dodaniem rezerwacji.'}`);
                }
            })
            .catch(error => {
                console.error('Błąd podczas dodawania rezerwacji:', error);
                alert('Wystąpił błąd podczas dodawania rezerwacji.');
            });
    });

    // Funkcja do walidacji adresu email
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@(([^<>()[\]\\.,;:\s@"]+\.)+[^<>()[\]\\.,;:\s@"]{2,})$/i;
        return re.test(String(email).toLowerCase());
    }

    // Inicjalizacja formularza
    loadRoomClasses(publicRoomClassSelect);
    loadAddons('public_addonsOptionsContainer', [], 'addons_add[]');

    // Obsługa zmiany klasy pokoju
    publicRoomClassSelect.addEventListener('change', fetchAvailableRooms);

    function validatePublicDates() {
        const checkInDate = new Date(document.getElementById('public_check_in').value);
        const checkOutDate = new Date(document.getElementById('public_check_out').value);
    
        if (checkOutDate < checkInDate) {
            alert('Data wymeldowania nie może być wcześniejsza niż data zameldowania. Proszę poprawić daty.');
            document.getElementById('public_check_out').value = ''; // Resetuje datę wymeldowania
        }
    }

    // Obsługa dat zameldowania i wymeldowania
    document.getElementById('public_check_in').addEventListener('change', () => {
        validatePublicDates();
        calculatePublicCost();
    });
    
    document.getElementById('public_check_out').addEventListener('change', () => {
        validatePublicDates();
        calculatePublicCost();
    });    
});
