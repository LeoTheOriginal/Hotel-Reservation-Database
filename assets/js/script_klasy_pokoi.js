document.addEventListener('DOMContentLoaded', function() {

    // Referencje do elementów:
    const addModal = document.getElementById('modal');
    const editModal = document.getElementById('editModal');
    const openAddModalBtn = document.getElementById('openModal');
    const closeAddModalBtn = document.getElementById('closeModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');

    const addRoomClassForm = document.getElementById('addRoomClassForm');
    const editRoomClassForm = document.getElementById('editRoomClassForm');

    const addRoomClassBtn = document.getElementById('addRoomClassBtn');
    const saveChangesBtn = document.getElementById('saveChangesBtn');

    const tableBody = document.querySelector('.room-classes-table tbody');

    // ================= FUNKCJE =================

    // (1) Odświeżenie tabeli klas pokoi
    function odswiezTabeleKlas() {
        fetch(`${baseUrl}/pages/klasy_pokoi/pobierz_klasy_pokoi.php`)
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = '';
                
                data.forEach((klasa, index) => {
                    // Ustalmy, czy klasa jest usuwalna (is_deletable == true)
                    // Zwróć uwagę, że w PostgreSQL przy CASE WHEN ... THEN true ELSE false END,
                    // może przyjść 't' / 'f' jako string, lub true/false jako boolean
                    const isDeletable = (klasa.is_deletable === true 
                                      || klasa.is_deletable === 'true' 
                                      || klasa.is_deletable === 't');

                    const userHasDeletePerm = (userRole === 'Administrator' || userRole === 'Manager');
                    const finalDeletable = (isDeletable && userHasDeletePerm);
                    
                    // Jeśli isDeletable = false => klasa zablokowana do usunięcia
                    const disabledAttr = finalDeletable ? '' : 'disabled';
                    
                    // Dodajemy osobną klasę stylizującą
                    const deleteBtnClass = finalDeletable ? 'btn-danger' : 'btn-disabled';
    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${klasa.nazwa_klasy}</td>
                        <td>${klasa.cena_podstawowa}</td>
                        <td class="actions">
                            <button class="btn-secondary edytuj-btn" data-id="${klasa.id}">Edytuj</button>
                            <button class="${deleteBtnClass} usun-btn" 
                                    data-id="${klasa.id}" ${disabledAttr}>
                                Usuń
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
    
                dodajEventListenery();
            })
            .catch(error => console.error('Błąd pobierania klas pokoi:', error));
    }
    

    // (2) Otwieranie modala dodawania
    openAddModalBtn.addEventListener('click', () => {
        // Najpierw wyczyść poprzednie dane
        addRoomClassForm.reset();
        document.getElementById('add-equipment-list').innerHTML = '';
        document.getElementById('add-lozka-select').innerHTML = '';
    
        // 1. Pobierz listę wyposażenia i typów łóżek równocześnie
        Promise.all([
            fetch(`${baseUrl}/pages/klasy_pokoi/pobierz_wyposazenie.php`).then(r => r.json()),
            pobierzTypyLozek()
        ])
        .then(([wyposazenia, listaLozek]) => {
            const equipmentContainer = document.getElementById('add-equipment-list');
            const lozkaSelect = document.getElementById('add-lozka-select');
    
            // 2. Wygeneruj checkboxy dla wyposażenia
            wyposazenia.forEach(item => {
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = item.id;
                checkbox.id = `add_eq_${item.id}`;
    
                const label = document.createElement('label');
                label.htmlFor = `add_eq_${item.id}`;
                label.textContent = item.nazwa_wyposażenia;
    
                const div = document.createElement('div');
                // Możesz dodać klasę 'equipment-item' dla spójności
                div.classList.add('equipment-item');
                div.appendChild(checkbox);
                div.appendChild(label);
    
                equipmentContainer.appendChild(div);
            });
    
            // 3. Wypełnij <select multiple> typami łóżek
            listaLozek.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nazwa_typu;
                lozkaSelect.appendChild(option);
            });
    
            // 4. Pokaż modal
            addModal.style.display = 'block';
        })
        .catch(err => {
            console.error('Błąd pobierania wyposażenia lub typów łóżek:', err);
            // Ewentualnie i tak pokaż modal, ale bez checkboxów lub typów łóżek
            addModal.style.display = 'block';
        });
    });    

    // (3) Zamknij modal dodawania
    closeAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'none';
        addRoomClassForm.reset();
    });

    // (4) Zamknij modal edycji
    closeEditModalBtn.addEventListener('click', () => {
        editModal.style.display = 'none';
        editRoomClassForm.reset();
    });

    // (5) Dodawanie nowej klasy
    addRoomClassBtn.addEventListener('click', () => {
        const nazwaKlasy = document.getElementById('nazwaKlasy').value.trim();
        const cenaPodstawowa = document.getElementById('cenaPodstawowa').value.trim();
    
        // Pobierz zaznaczone wyposażenie
        const checkedEquipment = [];
        document.querySelectorAll('#add-equipment-list input[type="checkbox"]:checked')
            .forEach(checkbox => {
                checkedEquipment.push(parseInt(checkbox.value));
            });
    
        // Pobierz zaznaczone typy łóżek
        const lozkaSelect = document.getElementById('add-lozka-select');
        const selectedBeds = [];
        for (let option of lozkaSelect.options) {
            if (option.selected) {
                selectedBeds.push(parseInt(option.value));
            }
        }
    
        const payload = {
            nazwaKlasy: nazwaKlasy,
            cenaPodstawowa: cenaPodstawowa,
            wyposazenie: checkedEquipment,
            lozka: selectedBeds  // Nowy klucz dla typów łóżek
        };
    
        fetch(`${baseUrl}/pages/klasy_pokoi/dodaj_klase_pokoju.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Klasa pokoju została dodana.');
                addModal.style.display = 'none';
                addRoomClassForm.reset();
                odswiezTabeleKlas();
            } else {
                alert(data.error || 'Wystąpił błąd podczas dodawania klasy pokoju.');
            }
        })
        .catch(error => {
            console.error('Błąd dodawania klasy pokoju:', error);
            alert('Wystąpił błąd podczas dodawania klasy pokoju.');
        });
    });
    

    // (6) Funkcja otwierająca modal edycji (pobieramy dane z tabeli / ewentualnie z backendu)
    function otworzEdytujModal(idKlasy) {
        // 1. Pobierz dane z wiersza (nazwa_klasy, cena_podstawowa)
        const row = document.querySelector(`.edytuj-btn[data-id='${idKlasy}']`).closest('tr');
        const cells = row.querySelectorAll('td');
        
        const nazwaKlasy = cells[1].textContent.trim();
        const cenaPodstawowa = cells[2].textContent.trim();
        
        document.getElementById('edit-id').textContent = idKlasy;
        document.getElementById('edit-nazwa-klasy').value = nazwaKlasy;
        document.getElementById('edit-cena-podstawowa').value = cenaPodstawowa;
    
        // 2. Pobierz listę wyposażenia i typów łóżek równocześnie
        Promise.all([
            pobierzTypyLozek(),
            pobierzLozkaKlasy(idKlasy),
            // Możesz dodać inne funkcje pobierania, jeśli są potrzebne
        ])
        .then(([listaLozek, lozkaKlasy]) => {
            // 3. Wypełnij <select multiple> typami łóżek
            const lozkaSelect = document.getElementById('edit-lozka-select');
            lozkaSelect.innerHTML = ''; // wyczyść
    
            listaLozek.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nazwa_typu;
    
                // Zaznacz, jeśli typ łóżka jest przypisany do klasy
                if (lozkaKlasy.includes(item.id.toString()) || lozkaKlasy.includes(item.id)) {
                    option.selected = true;
                }
    
                lozkaSelect.appendChild(option);
            });
    
            // 4. Pobierz i wypełnij wyposażenie (dotychczasowa funkcjonalność)
            return fetch(`${baseUrl}/pages/klasy_pokoi/pobierz_wyposazenie_klasy.php?id=${idKlasy}`)
                .then(r2 => r2.json())
                .then(wyposazenieKlasy => {
                    const container = document.getElementById('edit-equipment-list');
                    container.innerHTML = ''; // wyczyść
    
                    // Pobierz wszystkie wyposażenia
                    return fetch(`${baseUrl}/pages/klasy_pokoi/pobierz_wyposazenie.php`)
                        .then(r => r.json())
                        .then(wyposazenia => {
                            wyposazenia.forEach(item => {
                                const checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.value = item.id;
                                checkbox.id = `eq_${item.id}`;
    
                                // Zaznacz, jeśli wyposażenie jest przypisane do klasy
                                if (wyposazenieKlasy.includes(item.id.toString()) || wyposazenieKlasy.includes(item.id)) {
                                    checkbox.checked = true;
                                }
    
                                const label = document.createElement('label');
                                label.htmlFor = `eq_${item.id}`;
                                label.textContent = item.nazwa_wyposażenia;
    
                                const div = document.createElement('div');
                                div.appendChild(checkbox);
                                div.appendChild(label);
    
                                container.appendChild(div);
                            });
    
                            // Otwórz modal po zakończeniu
                            editModal.style.display = 'block';
                        });
                });
        })
        .catch(err => console.error('Błąd pobierania wyposażenia lub łóżek:', err));
    }    

    // (7) Zapisywanie zmian w edycji
    saveChangesBtn.addEventListener('click', () => {
        const idKlasy = document.getElementById('edit-id').textContent.trim();
        const nazwaKlasy = document.getElementById('edit-nazwa-klasy').value.trim();
        const cenaPodstawowa = document.getElementById('edit-cena-podstawowa').value.trim();
    
        // Pobierz zaznaczone wyposażenie
        const checkedEquipment = [];
        document.querySelectorAll('#edit-equipment-list input[type="checkbox"]:checked')
            .forEach(checkbox => {
                checkedEquipment.push(parseInt(checkbox.value));
            });
    
        // Pobierz zaznaczone typy łóżek
        const lozkaSelect = document.getElementById('edit-lozka-select');
        const selectedBeds = [];
        for (let option of lozkaSelect.options) {
            if (option.selected) {
                selectedBeds.push(parseInt(option.value));
            }
        }
    
        const payload = {
            id: idKlasy,
            nazwaKlasy: nazwaKlasy,
            cenaPodstawowa: cenaPodstawowa,
            wyposazenie: checkedEquipment,
            lozka: selectedBeds  // Nowy klucz dla typów łóżek
        };
    
        fetch(`${baseUrl}/pages/klasy_pokoi/edytuj_klase_pokoju.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Dane klasy pokoju, jej wyposażenie oraz typy łóżek zostały zaktualizowane.');
                editModal.style.display = 'none';
                editRoomClassForm.reset();
                odswiezTabeleKlas();  // Reload tabeli
            } else {
                alert(`Błąd aktualizacji: ${data.error}`);
            }
        })
        .catch(error => console.error('Błąd aktualizacji klasy pokoju:', error));
    });
    
    

    // (8) Usuwanie klasy pokoju
    function usunKlasePokoju(idKlasy) {
        if (confirm('Czy na pewno chcesz usunąć tę klasę pokoju?')) {
            fetch(`${baseUrl}/pages/klasy_pokoi/usun_klase_pokoju.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: idKlasy })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Klasa pokoju została usunięta.');
                    odswiezTabeleKlas();
                } else {
                    alert(`Błąd usuwania: ${data.error}`);
                }
            })
            .catch(error => console.error('Błąd usuwania klasy pokoju:', error));
        }
    }

    // (9) Dodawanie event listenerów do przycisków w tabeli
    function dodajEventListenery() {
        document.querySelectorAll('.edytuj-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                otworzEdytujModal(id);
            });
        });

        document.querySelectorAll('.usun-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                usunKlasePokoju(id);
            });
        });
    }

    //(10)
    function pobierzTypyLozek() {
        return fetch(`${baseUrl}/pages/klasy_pokoi/pobierz_typy_lozek.php`)
            .then(response => response.json())
            .catch(error => {
                console.error('Błąd pobierania typów łóżek:', error);
                return [];
            });
    }

    //(11)
    function pobierzLozkaKlasy(idKlasy) {
        return fetch(`${baseUrl}/pages/klasy_pokoi/pobierz_lozka_klasy.php?id=${idKlasy}`)
            .then(response => response.json())
            .catch(error => {
                console.error('Błąd pobierania łóżek klasy:', error);
                return [];
            });
    }
    
    

    // ================= URUCHOMIENIE =================
    odswiezTabeleKlas();

});
