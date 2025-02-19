<?php
session_start(); // Inicjalizacja sesji
include_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="<?php echo htmlspecialchars($base_url); ?>/assets/images/logo.png">
    <script>
        const baseUrl = "<?php echo htmlspecialchars($base_url); ?>";
        const userRole = "<?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?>"; 
    </script>
</head>
<body>
    <header class="stardust-header">
        <div class="header-content">
            <nav class="navigation">
                <ul class="menu">
                    <!-- Zawsze widoczne -->
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/index.php">Strona główna</a></li>
                    
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        <!-- Widoczne dla zalogowanych użytkowników -->

                        <!-- Goście -->
                        <li class="dropdown">
                            <a href="<?php echo htmlspecialchars($base_url); ?>/pages/goscie/goscie.php">Goście</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/goscie/goscie.php">Dodaj gościa</a></li>
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/goscie/goscie.php">Edytuj gościa</a></li>
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/goscie/goscie.php">Usuń gościa</a></li>
                            </ul>
                        </li>
                        
                        <!-- Klasy pokoi -->
                        <li class="dropdown">
                            <a href="<?php echo htmlspecialchars($base_url); ?>/pages/klasy_pokoi/klasy_pokoi.php">Klasy pokoi</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/klasy_pokoi/klasy_pokoi.php">Lista klas</a></li>
                            </ul>
                        </li>
                        
                        <!-- Typy łóżek -->
                        <li class="dropdown">
                            <a href="<?php echo htmlspecialchars($base_url); ?>/pages/typy_lozek/typy_lozek.php">Typy łóżek</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/typy_lozek/typy_lozek.php">Lista typów</a></li>
                            </ul>
                        </li> 
                        
                        <!-- Pokoje -->
                        <li class="dropdown">
                            <a href="<?php echo htmlspecialchars($base_url); ?>/pages/pokoje/pokoje.php">Pokoje</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pokoje/pokoje.php">Dodaj pokój</a></li>
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pokoje/pokoje.php">Edytuj pokój</a></li>
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pokoje/pokoje.php">Usuń pokój</a></li>
                            </ul>
                        </li>
                        
                        <!-- Raporty (Administrator i Manager) -->
                        <?php if (in_array($_SESSION['role'], ['Administrator', 'Manager'])): ?>
                            <li class="dropdown">
                                <a href="<?php echo htmlspecialchars($base_url); ?>/pages/raporty/raporty.php">Raporty</a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/raporty/raporty.php">Zobacz raporty</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Rezerwacje -->
                        <?php if (in_array($_SESSION['role'], ['Administrator', 'Manager', 'Recepcjonista'])): ?>
                            <li class="dropdown">
                                <a href="<?php echo htmlspecialchars($base_url); ?>/pages/rezerwacje/rezerwacje.php">Rezerwacje</a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/rezerwacje/rezerwacje.php">Dodaj rezerwację</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="dropdown">
                                <a href="<?php echo htmlspecialchars($base_url); ?>/pages/rezerwacje/rezerwacje_public.php">Rezerwacje</a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/rezerwacje/rezerwacje_public.php">Dodaj rezerwację</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
    
                        <!-- Pracownicy (Tylko Administrator i Manager) -->
                        <?php if (in_array($_SESSION['role'], ['Administrator', 'Manager'])): ?>
                            <li class="dropdown">
                                <a href="<?php echo htmlspecialchars($base_url); ?>/pages/pracownicy/pracownicy.php">Pracownicy</a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pracownicy/pracownicy.php">Dodaj pracownika</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pracownicy/pracownicy.php">Edytuj pracownika</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pracownicy/pracownicy.php">Usuń pracownika</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Wyposażenie -->
                        <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/wyposażenie/wyposażenie.php">Wyposażenie</a></li>
                        
                        <!-- Dodatki -->
                        <li class="dropdown">
                            <a href="<?php echo htmlspecialchars($base_url); ?>/pages/dodatki/dodatki.php">Dodatki</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/dodatki/dodatki.php">Lista dodatków</a></li>
                            </ul>
                        </li>
                        
                        <!-- Logi systemowe (Tylko Administrator) -->
                        <?php if ($_SESSION['role'] === 'Administrator'): ?>
                            <li class="dropdown">
                                <a href="<?php echo htmlspecialchars($base_url); ?>/pages/logi_systemowe/logi_systemowe.php">Logi systemowe</a>
                            </li>
                        <?php endif; ?>

                        <!-- Funkcjonalności (Tylko Administrator) -->
                        <?php if ($_SESSION['role'] === ROLE_ADMINISTRATOR): ?>
                            <li class="dropdown">
                                <a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci.php">Funkcjonalności</a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci.php">Opis Projektu</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_goscie.php">Goście</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_klasy_pokoi.php">Klasy pokoi</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_typy_lozek.php">Typy łóżek</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_pokoje.php">Pokoje</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_raporty.php">Raporty</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_rezerwacje.php">Rezerwacje</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_pracownicy.php">Pracownicy</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_wyposazenie.php">Wyposażenie</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_dodatki.php">Dodatki</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_logi_systemowe.php">Logi systemowe</a></li>
                                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/funkcjonalnosci/funkcjonalnosci_dashboard.php">Dashboard</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Kontakt -->
                        <li><a href="<?php echo htmlspecialchars($base_url); ?>/kontakt.php">Kontakt</a></li>
                        
                        <!-- Profil i Wylogowanie -->
                        <li class="dropdown">
                            <a href="<?php echo htmlspecialchars($base_url); ?>/pages/pracownicy/dashboard.php" class="dropdown-toggle">Witaj, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Użytkowniku'); ?> <span>&#9660;</span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pracownicy/dashboard.php">Dashboard</a></li>
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pracownicy/profil.php">Profil</a></li>
                                <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pracownicy/logout.php">Wyloguj się</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Menu dla niezalogowanych użytkowników -->
                        <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/rezerwacje/rezerwacje_public.php">Rezerwacje</a></li>
                        <li><a href="<?php echo htmlspecialchars($base_url); ?>/kontakt.php">Kontakt</a></li>
                        <li><a href="<?php echo htmlspecialchars($base_url); ?>/pages/pracownicy/login.php">Zaloguj się</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>
