<?php
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kontakt - Stardust Hotel</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_contact.css">
</head>
<body>
    <header class="stardust-header">
        
    </header>

    <div class="sections-container">
        <div class="contact-section">
            <div class="container">
                <h2 class="with-background">Nasze dane kontaktowe</h2>
                <p><strong>Adres:</strong> Ul. Gwiezdna 45, 31-424 Kraków, Zielonki</p>
                <p><strong>Telefon:</strong> +48 123 456 789</p>
                <p><strong>Email:</strong> kontakt@stardusthotel.pl</p>
            </div>
        </div>

        <div class="map-section">
            <div class="container">
                <h2 class="with-background">Znajdź nas</h2>
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2457.473641398895!2d19.89775941581609!3d50.08052087941156!2m3!1f0!2f0!3f0!3m2!
                    1i1024!2i768!4f13.1!3m3!1m2!1s0x47165b1a2b8e1e47%3A0x8b8a5e1f4f8c5b0b!2sUl.%20Gwiezdna%2045%2C%2031-424%20Krak%C3%B3w!5e0!3m2!1spl!2spl!4v1616161616161!5m2!1spl!2spl"
                    width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0">
                </iframe>
            </div>
        </div>
    </div>


    <footer class="stardust-footer">
        
    </footer>

    <?php include_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
