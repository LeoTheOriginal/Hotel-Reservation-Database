document.addEventListener('DOMContentLoaded', function () {
    const addModal = document.getElementById('modal');
    const editModal = document.getElementById('editModal');
    const openAddModalBtn = document.getElementById('openModal');
    const closeAddModalBtn = document.getElementById('closeModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const addGuestBtn = document.getElementById('addGuestBtn');
    const saveChangesBtn = document.getElementById('save-changes');
    const cancelChangesBtn = document.getElementById('cancel-changes');
    const phoneErrorEl = document.getElementById('phoneError');
    const emailErrorEl = document.getElementById('emailError');
    const addGuestForm = document.getElementById('addGuestForm');
    let currentRow = null; // Wskaźnik na aktualnie edytowany wiersz

    function resetFormErrors() {
        phoneErrorEl.textContent = '';
        emailErrorEl.textContent = '';
    }

    function validatePhoneNumber(phoneNumber) {
        const phoneRegex = /^[0-9]{9}$/;
        return phoneRegex.test(phoneNumber);
    }

    function refreshGuestTable() {
        fetch(`${baseUrl}/pages/goscie/odswiez_tabele_gosci.php`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Błąd podczas pobierania danych gości.');
                }
                return response.json();
            })
            .then(data => {
                const tbody = document.querySelector('.guests-table tbody');
                tbody.innerHTML = '';
                if (Array.isArray(data)) {
                    data.forEach((guest, index) => {
                        const tr = document.createElement('tr');
    
                        const nrTd = document.createElement('td');
                        nrTd.textContent = index + 1;
                        tr.appendChild(nrTd);
    
                        const imieTd = document.createElement('td');
                        imieTd.textContent = guest['imię'];
                        tr.appendChild(imieTd);
    
                        const nazwiskoTd = document.createElement('td');
                        nazwiskoTd.textContent = guest['nazwisko'];
                        tr.appendChild(nazwiskoTd);
    
                        const telTd = document.createElement('td');
                        telTd.textContent = guest['numer_telefonu'];
                        tr.appendChild(telTd);
    
                        const emailTd = document.createElement('td');
                        emailTd.textContent = guest['adres_email'];
                        tr.appendChild(emailTd);
    
                        const actionsTd = document.createElement('td');
                        actionsTd.classList.add('actions');
    
                        const editButton = document.createElement('a');
                        editButton.textContent = 'Edytuj';
                        editButton.classList.add('btn-secondary');
                        editButton.addEventListener('click', function () {
                            openEditModal(tr, guest);
                        });
    
                        const deleteButton = document.createElement('a');
                        deleteButton.textContent = 'Usuń';
                        deleteButton.classList.add('btn-danger');

                        // Sprawdzenie roli – jeśli to nie Administrator ani Manager,
                        // dezaktywuj przycisk
                        if (userRole !== 'Administrator' && userRole !== 'Manager') {
                            deleteButton.classList.add('btn-disabled');
                        } else {
                            // Dodaj event listener TYLKO, jeżeli to Administrator/Manager
                            deleteButton.addEventListener('click', function () {
                                const email = tr.children[4].textContent;
                                if (confirm(`Czy na pewno chcesz usunąć gościa z adresem e-mail: ${email}?`)) {
                                    deleteGuest(email);
                                }
                            });
                        }
    
                        actionsTd.appendChild(editButton);
                        actionsTd.appendChild(deleteButton);
                        tr.appendChild(actionsTd);
    
                        tbody.appendChild(tr);
                    });
                }
            })
            .catch(error => console.error('Błąd odświeżania tabeli gości:', error));
    }
    

    function openEditModal(row, guest) {
        // Wypełnienie pól w modalnym oknie edycji
        document.getElementById('edit-id').textContent = guest.id;
        document.getElementById('edit-name').value = guest['imię'];
        document.getElementById('edit-surname').value = guest['nazwisko'];
        document.getElementById('edit-phone').value = guest['numer_telefonu'];
        document.getElementById('edit-email').value = guest['adres_email'];

        currentRow = row; // Ustawienie aktualnie edytowanego wiersza
        editModal.style.display = 'block';
    }

    function deleteGuest(email) {
        fetch(`${baseUrl}/pages/goscie/usun_goscia.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Gość został pomyślnie usunięty.');
                refreshGuestTable();
            } else {
                alert(`Błąd: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Błąd usuwania gościa:', error);
            alert('Wystąpił błąd podczas usuwania gościa. Szczegóły w konsoli.');
        });
    }
    
    
    

    openAddModalBtn.addEventListener('click', function () {
        addModal.style.display = 'block';
    });

    closeAddModalBtn.addEventListener('click', function () {
        addModal.style.display = 'none';
        resetFormErrors();
        addGuestForm.reset();
    });

    closeEditModalBtn.addEventListener('click', function () {
        editModal.style.display = 'none';
    });

    cancelChangesBtn.addEventListener('click', function () {
        editModal.style.display = 'none';
    });

    addGuestBtn.addEventListener('click', function () {
        resetFormErrors();
        const prefix = document.getElementById('prefix').value;
        const phone = document.getElementById('numer_telefonu').value;
        const imie = document.getElementById('imię').value;
        const nazwisko = document.getElementById('nazwisko').value;
        const email = document.getElementById('adres_email').value;

        if (!validatePhoneNumber(phone)) {
            phoneErrorEl.textContent = 'Niepoprawny numer telefonu (wprowadź 9 cyfr).';
            return;
        }

        fetch(`${baseUrl}/pages/goscie/dodaj_goscia_action.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                numer_telefonu: `${prefix}${phone}`,
                imię: imie,
                nazwisko: nazwisko,
                adres_email: email
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Gość został pomyślnie dodany.');
                    addModal.style.display = 'none';
                    addGuestForm.reset();
                    resetFormErrors();
                    refreshGuestTable();
                } else {
                    if (data.error && data.error.includes('Email')) {
                        emailErrorEl.textContent = data.error;
                    } else {
                        alert(`Wystąpił błąd: ${data.error}`);
                    }
                }
            })
            .catch(error => console.error('Błąd dodawania gościa:', error));
    });

    saveChangesBtn.addEventListener('click', function () {
        const id = document.getElementById('edit-id').textContent;
        const name = document.getElementById('edit-name').value;
        const surname = document.getElementById('edit-surname').value;
        const phone = document.getElementById('edit-phone').value;
        const email = document.getElementById('edit-email').value;

        fetch(`${baseUrl}/pages/goscie/zaktualizuj_goscia.php`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id, name, surname, phone, email })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aktualizacja wiersza w tabeli
                    currentRow.children[1].textContent = name;
                    currentRow.children[2].textContent = surname;
                    currentRow.children[3].textContent = phone;
                    currentRow.children[4].textContent = email;
                    editModal.style.display = 'none';
                } else {
                    alert('Błąd podczas zapisywania zmian: ' + data.error);
                }
            })
            .catch(error => console.error('Błąd:', error));
    });

    refreshGuestTable();
});
