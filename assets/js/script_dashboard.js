// script_dashboard.js

document.addEventListener('DOMContentLoaded', function () {
    // Modale
    const changeLoginModal = document.getElementById('changeLoginModal');
    const changePasswordModal = document.getElementById('changePasswordModal');

    // Przyciski otwierające modale
    const changeLoginBtn = document.getElementById('changeLoginBtn');
    const changePasswordBtn = document.getElementById('changePasswordBtn');

    // Przycisk zamykający modale
    const closeLoginModal = document.getElementById('closeLoginModal');
    const closePasswordModal = document.getElementById('closePasswordModal');

    // Funkcja otwierająca modal
    function openModal(modal) {
        modal.style.display = 'block';
    }

    // Funkcja zamykająca modal
    function closeModal(modal) {
        modal.style.display = 'none';
    }

    // Obsługa otwierania modali
    changeLoginBtn.addEventListener('click', () => openModal(changeLoginModal));
    changePasswordBtn.addEventListener('click', () => openModal(changePasswordModal));

    // Obsługa zamykania modali
    closeLoginModal.addEventListener('click', () => closeModal(changeLoginModal));
    closePasswordModal.addEventListener('click', () => closeModal(changePasswordModal));

    // Zamknięcie modali po kliknięciu poza nimi
    window.addEventListener('click', function(event) {
        if (event.target === changeLoginModal) {
            closeModal(changeLoginModal);
        }
        if (event.target === changePasswordModal) {
            closeModal(changePasswordModal);
        }
    });

    // Obsługa podglądu zdjęcia przed uploadem
    const profilePictureInput = document.getElementById('profile_picture');
    const imagePreview = document.getElementById('imagePreview');
    const uploadPictureForm = document.getElementById('uploadPictureForm');

    profilePictureInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Sprawdzenie czy plik jest obrazem
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" alt="Podgląd Zdjęcia" />`;
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.innerHTML = '';
        }
    });

    // Automatyczne przesłanie formularza po wybraniu pliku
    profilePictureInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            uploadPictureForm.submit();
        }
    });
});
