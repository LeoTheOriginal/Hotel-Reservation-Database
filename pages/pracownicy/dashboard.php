<?php
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    header("Location: login.php");
    exit;
}

// Pobranie informacji o pracowniku z bazy danych
include_once __DIR__ . '/../../config.php';

try {
    $pdo = new PDO($dsn, $db_user, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Pobranie danych pracownika wraz z nazwą roli
    $stmt = $pdo->prepare("
        SELECT p.*, r.nazwa_roli 
        FROM rezerwacje_hotelowe.pracownik p
        JOIN rezerwacje_hotelowe.rola r ON p.rola_id = r.id
        WHERE p.id = :id
    ");
    $stmt->bindParam(':id', $_SESSION['pracownik_id'], PDO::PARAM_INT);
    $stmt->execute();
    $pracownik = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$pracownik){
        // Jeśli pracownik nie istnieje, wyloguj się
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch(PDOException $e){
    // Obsługa błędów bazy danych
    die("Błąd bazy danych: " . $e->getMessage());
}

include_once __DIR__ . '/../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Pracownik</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_dashboard.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_modal.css">
</head>
<body>
    <header class="stardust-header">
        
    </header>

    <div class="dashboard-container">
        <div class="profile-section">
            <div class="profile-picture">
                <?php if($pracownik['profile_picture']): ?>
                    <img src="<?php echo $base_url . '/assets/images/uploads/' . htmlspecialchars($pracownik['profile_picture']); ?>" alt="Zdjęcie Profilowe">
                <?php else: ?>
                    <img src="<?php echo $base_url; ?>/assets/images/default_profile.jpg" alt="Default Profile Picture">
                <?php endif; ?>
                <form id="uploadPictureForm" action="<?php echo $base_url; ?>/pages/pracownicy/upload_picture.php" method="POST" enctype="multipart/form-data">
                    <label for="profile_picture" class="upload-label">
                        Zmień zdjęcie
                    </label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                </form>
                <div id="imagePreview" class="image-preview"></div>
            </div>
        </div>

        <div class="info-section">
            <h2>Witaj, <?php echo htmlspecialchars($pracownik['login']); ?>!</h2>
            <?php
            if(isset($_SESSION['success'])){
                echo '<p class="success">'.htmlspecialchars($_SESSION['success']).'</p>';
                unset($_SESSION['success']);
            }
            if(isset($_SESSION['error'])){
                echo '<p class="error">'.htmlspecialchars($_SESSION['error']).'</p>';
                unset($_SESSION['error']);
            }
            ?>

            <div class="user-info">
                <p><strong>Imię:</strong> <?php echo htmlspecialchars($pracownik['imię']); ?></p>
                <p><strong>Nazwisko:</strong> <?php echo htmlspecialchars($pracownik['nazwisko']); ?></p>
                <p><strong>Login:</strong> <?php echo htmlspecialchars($pracownik['login']); ?> 
                    <button class="btn btn-secondary" id="changeLoginBtn">Zmień Login</button>
                </p>
                <p><strong>Rola:</strong> <?php echo htmlspecialchars($pracownik['nazwa_roli']); ?></p>
            </div>

            <div class="user-actions">
                <button class="btn btn-secondary" id="changePasswordBtn">Zmień Hasło</button>
            </div>
        </div>
    </div>

    <!-- Modal do zmiany loginu -->
    <div id="changeLoginModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeLoginModal">&times;</span>
            <h2>Zmiana Loginu</h2>
            <form id="changeLoginForm" action="<?php echo $base_url; ?>/pages/pracownicy/update_login.php" method="POST">
                <div class="form-group">
                    <label for="new_login">Nowy Login:</label>
                    <input type="text" id="new_login" name="new_login" required>
                </div>
                <div class="form-group">
                    <label for="current_password_login">Aktualne Hasło:</label>
                    <input type="password" id="current_password_login" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password_login">Potwierdź Hasło:</label>
                    <input type="password" id="confirm_password_login" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Zmień Login</button>
            </form>
        </div>
    </div>

    <!-- Modal do zmiany hasła -->
    <div id="changePasswordModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closePasswordModal">&times;</span>
            <h2>Zmiana Hasła</h2>
            <form id="changePasswordForm" action="<?php echo $base_url; ?>/pages/pracownicy/update_password.php" method="POST">
                <div class="form-group">
                    <label for="current_password_change">Aktualne Hasło:</label>
                    <input type="password" id="current_password_change" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Nowe Hasło:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password_change">Potwierdź Nowe Hasło:</label>
                    <input type="password" id="confirm_password_change" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Zmień Hasło</button>
            </form>
        </div>
    </div>

    <footer class="stardust-footer">
        
    </footer>

    <script src="<?php echo $base_url; ?>/assets/js/script_dashboard.js"></script>
    <?php include_once __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
