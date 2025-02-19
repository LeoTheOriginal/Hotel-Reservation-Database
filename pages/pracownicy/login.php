<?php
session_start();

// Jeśli użytkownik jest już zalogowany, przekieruj go do dashboardu
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
    header("Location: dashboard.php");
    exit;
}

// Pobranie konfiguracji
include_once __DIR__ . '/../../includes/header.php'; 
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie - Dla Pracowników</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_login.css">
</head>
<body>
    <header class="stardust-header">
        
    </header>

    <div class="login-wrapper">
        <div class="login-container">
            <h2 class="section-title">Logowanie dla Pracowników</h2>
            <?php
            // Wyświetlanie komunikatów
            if(isset($_SESSION['error'])){
                echo '<p class="error">'.htmlspecialchars($_SESSION['error']).'</p>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
                echo '<p class="success">'.htmlspecialchars($_SESSION['success']).'</p>';
                unset($_SESSION['success']);
            }
            ?>
            <form action="<?php echo $base_url; ?>/pages/pracownicy/login_process.php" method="POST">
                <div class="form-group">
                    <label for="login">Login:</label>
                    <input type="text" id="login" name="login" required>
                </div>
                <div class="form-group">
                    <label for="password">Hasło:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Zaloguj się</button>
            </form>
        </div>
    </div>

    <footer class="stardust-footer">
        
    </footer>
</body>
</html>

    
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

