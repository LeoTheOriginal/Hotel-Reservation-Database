document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('.equipment-table tbody');
    
    // Referencje do modalów, przycisków
    const addModal = document.getElementById('modal');
    const openAddModalBtn = document.getElementById('openModal');
    const closeAddModalBtn = document.getElementById('closeModal');
    const addEquipmentForm = document.getElementById('addEquipmentForm');
    const addEquipmentBtn = document.getElementById('addEquipmentBtn');

    const editModal = document.getElementById('editModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const editEquipmentForm = document.getElementById('editEquipmentForm');
    const saveChangesBtn = document.getElementById('saveChangesBtn');

    // =============== Funkcja odświeżania tabeli ===============
    function odswiezTabeleWyposazenia() {
        fetch(`${baseUrl}/pages/wyposażenie/pobierz_wyposazenie.php`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data)) {
                    console.error('Nieprawidłowy format danych:', data);
                    alert(data.error || 'Wystąpił nieznany błąd.');
                    return;
                }
    
                tableBody.innerHTML = '';
    
                data.forEach((item, index) => {
                    // Odczyt is_deletable
                    // Uwaga: w PostgreSQL bywa, że "true"/"false" przychodzą jako string 't'/'f' 
                    // albo literal boole. Poniższy kod ujednolica interpretację:
                    const isDeletable = (
                        item.is_deletable === true 
                        || item.is_deletable === 'true' 
                        || item.is_deletable === 't'
                    );
    
                    // Jeśli nie jest usuwalne -> disabled
                    const disabledAttr = isDeletable ? '' : 'disabled';
                    
                    // Możemy nadać inne style:
                    const deleteBtnClass = isDeletable ? 'btn-danger' : 'btn-disabled';
    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${item.nazwa_wyposażenia}</td>
                        <td class="actions">
                            <button class="btn-secondary edytuj-btn" data-id="${item.id}">Edytuj</button>
                            <button class="${deleteBtnClass} usun-btn"
                                    data-id="${item.id}"
                                    ${disabledAttr}
                                    title="${isDeletable ? '' : 'Nie można usunąć – wyposażenie jest w użyciu.'}">
                                Usuń
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
    
                dodajEventListenery();
            })
            .catch(error => console.error('Błąd pobierania wyposażenia:', error));
    }    

    // =============== Modal dodawania nowego wyposażenia ===============
    openAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'block';
    });

    closeAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'none';
        addEquipmentForm.reset();
    });

    // =============== Dodawanie wyposażenia ===============
    addEquipmentBtn.addEventListener('click', () => {
        const formData = new FormData(addEquipmentForm);

        fetch(`${baseUrl}/pages/wyposażenie/dodaj_wyposazenie.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Wyposażenie dodane pomyślnie.');
                addModal.style.display = 'none';
                addEquipmentForm.reset();
                odswiezTabeleWyposazenia();
            } else {
                alert(data.error || 'Wystąpił błąd podczas dodawania wyposażenia.');
            }
        })
        .catch(error => {
            console.error('Błąd podczas dodawania wyposażenia:', error);
            alert('Wystąpił błąd podczas dodawania wyposażenia.');
        });
    });

    // =============== Edycja wyposażenia ===============
    function otworzEdytujModal(idWyposazenia) {
        // Możemy pobrać dane z aktualnego wiersza w tabeli, by wstawić do formularza edycji
        const row = document.querySelector(`.edytuj-btn[data-id='${idWyposazenia}']`).closest('tr');
        const cells = row.querySelectorAll('td');
        const nazwa = cells[1].textContent;

        document.getElementById('edit-id').textContent = idWyposazenia;
        document.getElementById('edit-nazwa-wyposazenia').value = nazwa;

        editModal.style.display = 'block';
    }

    closeEditModalBtn.addEventListener('click', () => {
        editModal.style.display = 'none';
        editEquipmentForm.reset();
    });

    // Zapisanie zmian (edycja wyposażenia)
    saveChangesBtn.addEventListener('click', () => {
        const idWyposazenia = document.getElementById('edit-id').textContent;
        const nowaNazwa = document.getElementById('edit-nazwa-wyposazenia').value;

        const payload = {
            id: idWyposazenia,
            nazwa: nowaNazwa
        };

        fetch(`${baseUrl}/pages/wyposażenie/edytuj_wyposazenie.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Wyposażenie zostało zaktualizowane.');
                editModal.style.display = 'none';
                odswiezTabeleWyposazenia();
            } else {
                alert(`Błąd aktualizacji: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Błąd aktualizacji wyposażenia:', error);
            alert('Wystąpił nieznany błąd podczas edycji wyposażenia.');
        });
    });

    // =============== Usuwanie wyposażenia ===============
    function usunWyposazenie(idWyposazenia) {
        if (!confirm('Czy na pewno chcesz usunąć to wyposażenie?')) {
            return;
        }

        fetch(`${baseUrl}/pages/wyposażenie/usun_wyposazenie.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: idWyposazenia })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Wyposażenie zostało usunięte.');
                odswiezTabeleWyposazenia();
            } else {
                alert(`Błąd usuwania: ${data.error}`);
            }
        })
        .catch(error => console.error('Błąd usuwania wyposażenia:', error));
    }

    // =============== Podpinanie eventów do przycisków w tabeli ===============
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
                usunWyposazenie(id);
            });
        });
    }

    // =============== Inicjalizacja ===============
    odswiezTabeleWyposazenia();
});
