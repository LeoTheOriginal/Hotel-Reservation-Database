document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('.addons-table tbody');

    const addModal = document.getElementById('modal');
    const openAddModalBtn = document.getElementById('openModal');
    const closeAddModalBtn = document.getElementById('closeModal');
    const addAddonForm = document.getElementById('addAddonForm');
    const addAddonBtn = document.getElementById('addAddonBtn');

    const editModal = document.getElementById('editModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const editAddonForm = document.getElementById('editAddonForm');
    const saveChangesBtn = document.getElementById('saveChangesBtn');

    // ======================= Funkcja odświeżania =======================
    function odswiezTabeleDodatkow() {
        fetch(`${baseUrl}/pages/dodatki/pobierz_dodatki.php`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data)) {
                    console.error('Nieprawidłowy format danych:', data);
                    alert(data.error || 'Wystąpił nieznany błąd.');
                    return;
                }

                tableBody.innerHTML = '';

                data.forEach((dodatek, index) => {
                    // Odczyt is_deletable (może przyjść jako bool lub 't'/'f')
                    const isDeletable = (
                        dodatek.is_deletable === true ||
                        dodatek.is_deletable === 'true' ||
                        dodatek.is_deletable === 't'
                    );

                    // Jeśli nie jest usuwalny -> disabled + inna klasa CSS
                    const disabledAttr = isDeletable ? '' : 'disabled';
                    const deleteBtnClass = isDeletable ? 'btn-danger' : 'btn-disabled';
                    const tooltip = isDeletable ? '' : 'Nie można usunąć – dodatek jest w użyciu.';

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${dodatek.nazwa_dodatku}</td>
                        <td>${dodatek.cena}</td>
                        <td class="actions">
                            <button class="btn-secondary edytuj-btn" data-id="${dodatek.id}">Edytuj</button>
                            <button class="${deleteBtnClass} usun-btn"
                                    data-id="${dodatek.id}"
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
            .catch(error => console.error('Błąd pobierania dodatków:', error));
    }

    // ======================= Dodawanie dodatku =======================
    openAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'block';
    });

    closeAddModalBtn.addEventListener('click', () => {
        addModal.style.display = 'none';
        addAddonForm.reset();
    });

    addAddonBtn.addEventListener('click', () => {
        const formData = new FormData(addAddonForm);

        fetch(`${baseUrl}/pages/dodatki/dodaj_dodatek.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Dodatek został dodany pomyślnie.');
                addModal.style.display = 'none';
                addAddonForm.reset();
                odswiezTabeleDodatkow();
            } else {
                alert(data.error || 'Wystąpił błąd podczas dodawania dodatku.');
            }
        })
        .catch(error => {
            console.error('Błąd podczas dodawania dodatku:', error);
            alert('Wystąpił błąd podczas dodawania dodatku.');
        });
    });

    // ======================= Edycja dodatku =======================
    function otworzEdytujModal(idDodatku) {
        // Pobieramy z wiersza w tabeli
        const row = document.querySelector(`.edytuj-btn[data-id='${idDodatku}']`).closest('tr');
        const cells = row.querySelectorAll('td');
        const nazwa = cells[1].textContent;
        const cena = cells[2].textContent;

        document.getElementById('edit-id').textContent = idDodatku;
        document.getElementById('edit-nazwa-dodatku').value = nazwa;
        document.getElementById('edit-cena').value = cena;

        editModal.style.display = 'block';
    }

    closeEditModalBtn.addEventListener('click', () => {
        editModal.style.display = 'none';
        editAddonForm.reset();
    });

    saveChangesBtn.addEventListener('click', () => {
        const idDodatku = document.getElementById('edit-id').textContent;
        const nazwaDodatku = document.getElementById('edit-nazwa-dodatku').value;
        const cena = document.getElementById('edit-cena').value;

        const payload = {
            id: idDodatku,
            nazwa: nazwaDodatku,
            cena: cena
        };

        fetch(`${baseUrl}/pages/dodatki/edytuj_dodatek.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Dodatek został zaktualizowany.');
                editModal.style.display = 'none';
                odswiezTabeleDodatkow();
            } else {
                alert(`Błąd aktualizacji: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Błąd aktualizacji dodatku:', error);
            alert('Wystąpił błąd podczas edycji dodatku.');
        });
    });

    // ======================= Usuwanie dodatku =======================
    function usunDodatek(idDodatku) {
        if (!confirm('Czy na pewno chcesz usunąć ten dodatek?')) {
            return;
        }

        fetch(`${baseUrl}/pages/dodatki/usun_dodatek.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: idDodatku })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Dodatek został usunięty.');
                odswiezTabeleDodatkow();
            } else {
                alert(`Błąd usuwania: ${data.error}`);
            }
        })
        .catch(error => console.error('Błąd usuwania dodatku:', error));
    }

    function dodajEventListenery() {
        document.querySelectorAll('.edytuj-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                otworzEdytujModal(id);
            });
        });

        document.querySelectorAll('.usun-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                usunDodatek(id);
            });
        });
    }

    // ======================= Start =======================
    odswiezTabeleDodatkow();
});
