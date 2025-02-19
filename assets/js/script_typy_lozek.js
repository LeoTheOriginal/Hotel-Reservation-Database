document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('.bed-types-table tbody');

    const addModal = document.getElementById('modal');
    const openAddModalBtn = document.getElementById('openModal');
    const closeAddModalBtn = document.getElementById('closeModal');
    const addBedTypeForm = document.getElementById('addBedTypeForm');
    const addBedTypeBtn = document.getElementById('addBedTypeBtn');

    const editModal = document.getElementById('editModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const editBedTypeForm = document.getElementById('editBedTypeForm');
    const saveChangesBtn = document.getElementById('saveChangesBtn');

    // ==================== Odświeżanie tabeli ====================
    function odswiezTabeleTypowLozek() {
        fetch(`${baseUrl}/pages/typy_lozek/pobierz_typy_lozek.php`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data)) {
                    console.error('Nieprawidłowy format danych:', data);
                    alert(data.error || 'Wystąpił nieznany błąd.');
                    return;
                }

                tableBody.innerHTML = '';

                data.forEach((typ, index) => {
                    // Odczyt is_deletable
                    const isDeletable = (
                        typ.is_deletable === true ||
                        typ.is_deletable === 'true' ||
                        typ.is_deletable === 't'
                    );

                    // Sprawdź, czy bieżący zalogowany user może usuwać (Admin/Manager)
                    const userHasDeletePerm = (userRole === 'Administrator' || userRole === 'Manager');
                    // Finalna flaga usuwalności
                    const finalDeletable = (isDeletable && userHasDeletePerm);

                    // Jeśli finalDeletable = false => klawisz usuń będzie wyszarzony
                    const disabledAttr = finalDeletable ? '' : 'disabled';
                    const deleteBtnClass = finalDeletable ? 'btn-danger' : 'btn-disabled';

                    // Tooltip, jeśli jest w bazie zablokowane:
                    // lub jeśli user nie ma uprawnień
                    let tooltip = '';
                    if (!isDeletable) {
                        tooltip = 'Nie można usunąć – typ łóżka jest w użyciu.';
                    } else if (!userHasDeletePerm) {
                        tooltip = 'Brak uprawnień do usuwania tego typu łóżka.';
                    }
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${typ.nazwa_typu}</td>
                        <td>${typ.liczba_osob}</td>
                        <td class="actions">
                            <button class="btn-secondary edytuj-btn" data-id="${typ.id}">Edytuj</button>
                            <button class="${deleteBtnClass} usun-btn"
                                    data-id="${typ.id}"
                                    ${disabledAttr}
                                    title="${tooltip}">
                                Usuń
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });

                dodajEventListenery();
            })
            .catch(error => console.error('Błąd pobierania typów łóżek:', error));
    }

    // ==================== Dodawanie nowego typu łóżka ====================
    openAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'block';
    });

    closeAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'none';
        addBedTypeForm.reset();
    });

    addBedTypeBtn.addEventListener('click', () => {
        const formData = new FormData(addBedTypeForm);
        // formData zawiera: nazwaTypu, liczbaOsob

        fetch(`${baseUrl}/pages/typy_lozek/dodaj_typ_lozka.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Typ łóżka został dodany pomyślnie.');
                addModal.style.display = 'none';
                addBedTypeForm.reset();
                odswiezTabeleTypowLozek();
            } else {
                alert(data.error || 'Wystąpił błąd podczas dodawania typu łóżka.');
            }
        })
        .catch(error => {
            console.error('Błąd podczas dodawania typu łóżka:', error);
            alert('Wystąpił błąd podczas dodawania typu łóżka.');
        });
    });

    // ==================== Edycja typu łóżka ====================
    function otworzEdytujModal(idTypu) {
        const row = document.querySelector(`.edytuj-btn[data-id='${idTypu}']`).closest('tr');
        const cells = row.querySelectorAll('td');
        const nazwa = cells[1].textContent;
        const liczba = cells[2].textContent;

        document.getElementById('edit-id').textContent = idTypu;
        document.getElementById('edit-nazwa-typu').value = nazwa;
        document.getElementById('edit-liczba-osob').value = liczba;

        editModal.style.display = 'block';
    }

    closeEditModalBtn.addEventListener('click', () => {
        editModal.style.display = 'none';
        editBedTypeForm.reset();
    });

    saveChangesBtn.addEventListener('click', () => {
        const idTypu = document.getElementById('edit-id').textContent;
        const nowaNazwa = document.getElementById('edit-nazwa-typu').value;
        const nowaLiczbaOsob = document.getElementById('edit-liczba-osob').value;

        const payload = {
            id: idTypu,
            nazwa: nowaNazwa,
            liczba_osob: nowaLiczbaOsob
        };

        fetch(`${baseUrl}/pages/typy_lozek/edytuj_typ_lozka.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Typ łóżka został zaktualizowany.');
                editModal.style.display = 'none';
                odswiezTabeleTypowLozek();
            } else {
                alert(`Błąd aktualizacji: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Błąd aktualizacji typu łóżka:', error);
            alert('Wystąpił błąd podczas edycji typu łóżka.');
        });
    });

    // ==================== Usuwanie typu łóżka ====================
    function usunTypLozka(idTypu) {
        if (!confirm('Czy na pewno chcesz usunąć ten typ łóżka?')) {
            return;
        }

        fetch(`${baseUrl}/pages/typy_lozek/usun_typ_lozka.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: idTypu })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Typ łóżka został usunięty.');
                odswiezTabeleTypowLozek();
            } else {
                alert(`Błąd usuwania: ${data.error}`);
            }
        })
        .catch(error => console.error('Błąd usuwania typu łóżka:', error));
    }

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
                usunTypLozka(id);
            });
        });
    }

    // ==================== Start ====================
    odswiezTabeleTypowLozek();
});
