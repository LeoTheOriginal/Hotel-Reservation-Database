document.addEventListener('DOMContentLoaded', function () {
    const dodajModal = document.getElementById('modal');
    const edytujModal = document.getElementById('editModal');
    const otworzDodajModalBtn = document.getElementById('openModal');
    const zamknijDodajModalBtn = document.getElementById('closeModal');
    const zamknijEdytujModalBtn = document.getElementById('closeEditModal');
    const dodajPracownikBtn = document.getElementById('addEmployeeBtn');
    const zapiszZmianyBtn = document.getElementById('saveChangesBtn');
    const dodajPracownikForm = document.getElementById('addEmployeeForm');
    const edytujPracownikForm = document.getElementById('editEmployeeForm');
    const tabelaBody = document.querySelector('.employees-table tbody');

    const changePasswordModal = document.getElementById('changePasswordModal');
    const closeChangePasswordModalBtn = document.getElementById('closeChangePasswordModal');
    const savePasswordBtn = document.getElementById('savePasswordBtn');
    const changePasswordForm = document.getElementById('changePasswordForm');

    if (userRole !== 'Administrator') {
        changePasswordBtn.classList.add('disabled');
        changePasswordBtn.setAttribute('disabled', true);
    }
    

    if (userRole === 'Administrator') {
        // Administrator -> usuwamy klasę 'disabled' i atrybut disabled
        changePasswordBtn.classList.remove('disabled');
        changePasswordBtn.removeAttribute('disabled');
    }

    // Funkcja do odświeżania tabeli pracowników
    function odswiezTabelePracownikow() {
        fetch(`${baseUrl}/pages/pracownicy/pobierz_pracownikow.php`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data)) {
                    console.error('Nieprawidłowy format danych:', data);
                    alert(data.error || 'Wystąpił nieznany błąd.');
                    return;
                }

                tabelaBody.innerHTML = ''; // Czyszczenie tabeli

                data.forEach((pracownik, index) => {
                    // Domyślnie przyciski są aktywne
                    let disabledClass = "";
                    let disabledAttr = "";

                    // Jeśli zalogowany użytkownik to Manager, a wiersz dotyczy Administratora -> zablokuj
                    if (userRole === "Manager" && pracownik.rola === "Administrator") {
                        disabledClass = "disabled";  // klasa CSS
                        disabledAttr = "disabled";   // atrybut HTML
                    }


                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${pracownik.imie}</td>
                        <td>${pracownik.nazwisko}</td>
                        <td>${pracownik.login}</td>
                        <td>${pracownik.rola}</td>
                        <td class="actions">
                            <button 
                                class="btn-secondary edytuj-btn ${disabledClass}" 
                                data-id="${pracownik.id}" 
                                ${disabledAttr}>
                                Edytuj
                            </button>
                            <button 
                                class="btn-danger usun-btn ${disabledClass}" 
                                data-id="${pracownik.id}"
                                ${disabledAttr}>
                                Usuń
                            </button>
                        </td>
                    `;
                    tabelaBody.appendChild(row);
                });

                dodajEventListenery();
            })
            .catch(error => console.error('Błąd odświeżania tabeli pracowników:', error));
    }

    // Funkcja otwierająca modal dodawania pracownika
    otworzDodajModalBtn.addEventListener('click', () => {
        dodajModal.style.display = 'block';
        zaladujRole(document.getElementById('role'));
    });

    zamknijDodajModalBtn.addEventListener('click', () => {
        dodajModal.style.display = 'none';
        dodajPracownikForm.reset();
    });

    zamknijEdytujModalBtn.addEventListener('click', () => {
        edytujModal.style.display = 'none';
        edytujPracownikForm.reset();
    });

    // Funkcja wypełniająca select rolami
    function zaladujRole(selectElement, domyslnaWartosc = null) {
        fetch(`${baseUrl}/pages/pracownicy/pobierz_role.php`)
            .then(response => response.json())
            .then(data => {
                selectElement.innerHTML = '<option value="">Wybierz rolę</option>';
                data.forEach(rola => {
                    const option = document.createElement('option');
                    option.value = rola.id;
                    option.textContent = rola.nazwa_roli;
                    if (domyslnaWartosc && rola.id === domyslnaWartosc) {
                        option.selected = true;
                    }
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Błąd ładowania ról:', error));
    }    

    // Funkcja dodawania pracownika
    dodajPracownikBtn.addEventListener('click', () => {
        const formData = new FormData(dodajPracownikForm);

        fetch(`${baseUrl}/pages/pracownicy/dodaj_pracownika.php`, {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Pracownik został dodany pomyślnie.');
                    dodajModal.style.display = 'none';
                    dodajPracownikForm.reset();
                    odswiezTabelePracownikow(); // Odśwież tabelę
                } else {
                    alert(data.error || 'Wystąpił błąd podczas dodawania pracownika.');
                }
            })
            .catch(error => {
                console.error('Błąd podczas dodawania pracownika:', error);
                alert('Wystąpił błąd podczas dodawania pracownika.');
            });
    });


    // Funkcja otwierająca modal edycji pracownika
    function otworzEdytujModal(idPracownika) {
        fetch(`${baseUrl}/pages/pracownicy/pobierz_pracownika.php?id=${idPracownika}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const pracownik = data.pracownik;

                    // Wypełnij formularz edycji danymi pracownika
                    document.getElementById('edit-id').textContent = pracownik.id;
                    document.getElementById('edit-first-name').value = pracownik.imie;
                    document.getElementById('edit-last-name').value = pracownik.nazwisko;
                    document.getElementById('edit-login').value = pracownik.login;

                    zaladujRole(document.getElementById('edit-role'), pracownik.rola_id);

                    edytujModal.style.display = 'block';
                } else {
                    alert(`Błąd pobierania danych pracownika: ${data.error}`);
                }
            })
            .catch(error => console.error('Błąd pobierania danych pracownika:', error));
    }


    // Funkcja zapisywania zmian w edytowanym pracowniku
    zapiszZmianyBtn.addEventListener('click', () => {
        const idPracownika = document.getElementById('edit-id').textContent;
        const formData = {
            id: idPracownika,
            imie: document.getElementById('edit-first-name').value,
            nazwisko: document.getElementById('edit-last-name').value,
            login: document.getElementById('edit-login').value,
            rola_id: document.getElementById('edit-role').value
        };

        fetch(`${baseUrl}/pages/pracownicy/edytuj_pracownika.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Dane pracownika zostały zaktualizowane.');
                    edytujModal.style.display = 'none';
                    odswiezTabelePracownikow();
                } else {
                    alert(`Błąd aktualizacji: ${data.error}`);
                }
            })
            .catch(error => console.error('Błąd aktualizacji pracownika:', error));
    });

    // Funkcja otwierająca modal zmiany hasła
    changePasswordBtn.addEventListener('click', () => {
        changePasswordModal.style.display = 'block';
    });

    // Funkcja zamykająca modal zmiany hasła
    closeChangePasswordModalBtn.addEventListener('click', () => {
        changePasswordModal.style.display = 'none';
        changePasswordForm.reset();
    });

    // Funkcja zapisywania nowego hasła
    savePasswordBtn.addEventListener('click', () => {
        const currentPassword = document.getElementById('current-password').value;
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const idPracownika = document.getElementById('edit-id').textContent;

        if (newPassword !== confirmPassword) {
            alert('Nowe hasła nie są zgodne!');
            return;
        }

        fetch(`${baseUrl}/pages/pracownicy/zmien_haslo.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: idPracownika,
                current_password: currentPassword,
                new_password: newPassword
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Hasło zostało zmienione.');
                    changePasswordModal.style.display = 'none';
                    changePasswordForm.reset();
                } else {
                    alert(`Błąd zmiany hasła: ${data.error}`);
                }
            })
            .catch(error => console.error('Błąd zmiany hasła:', error));
    });


    // Funkcja usuwania pracownika
    function usunPracownika(idPracownika) {
        if (confirm('Czy na pewno chcesz usunąć tego pracownika?')) {
            fetch(`${baseUrl}/pages/pracownicy/usun_pracownika.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: idPracownika }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pracownik został usunięty.');
                        odswiezTabelePracownikow();
                    } else {
                        alert(`Błąd usuwania: ${data.error}`);
                    }
                })
                .catch(error => console.error('Błąd usuwania pracownika:', error));
        }
    }

    // Funkcja dodaje event listenery do przycisków akcji
    function dodajEventListenery() {
        document.querySelectorAll('.edytuj-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                otworzEdytujModal(id);
            });
        });

        document.querySelectorAll('.usun-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                usunPracownika(id);
            });
        });
    }

    // Odśwież tabelę na starcie
    odswiezTabelePracownikow();
});
