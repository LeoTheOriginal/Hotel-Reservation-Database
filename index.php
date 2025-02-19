<?php
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/includes/header.php';
?>

<div class="hero-section">
    <div class="hero-content with-background">
        <h1>Witamy w Stardust Hotel</h1>
        <p>Doświadcz magii luksusu w naszym hotelu. Zarezerwuj swój pokój już teraz!</p>
        <a href="<?php echo $base_url; ?>/pages/rezerwacje/rezerwacje_public.php" class="btn-primary">Zarezerwuj teraz</a>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <h2 class="with-background">Nasze wyjątkowe usługi</h2>
        <div class="features">
            <div class="feature">
                <img src="<?php echo $base_url; ?>/assets/images/icon-room.png" alt="Pokoje">
                <h3>Komfortowe pokoje</h3>
                <p>Oferujemy szeroki wybór pokoi dopasowanych do Twoich potrzeb, od standardowych po luksusowe apartamenty.</p>
            </div>
            <div class="feature">
                <img src="<?php echo $base_url; ?>/assets/images/icon-dining.png" alt="Restauracja">
                <h3>Wykwintna kuchnia</h3>
                <p>Nasza restauracja serwuje dania inspirowane kuchniami z całego świata, przygotowywane z najwyższej jakości składników.</p>
            </div>
            <div class="feature">
                <img src="<?php echo $base_url; ?>/assets/images/icon-spa.png" alt="Spa">
                <h3>Relaks i SPA</h3>
                <p>Zrelaksuj się w naszym nowoczesnym SPA i odnowie biologicznej. Oferujemy szeroki wybór zabiegów.</p>
            </div>
        </div>
    </div>
</div>

<div class="gallery-section">
    <div class="container">
        <h2 class="with-background">Galeria</h2>
        <div class="gallery">
            <img src="<?php echo $base_url; ?>/assets/images/gallery1.jpg" alt="Pokój">
            <img src="<?php echo $base_url; ?>/assets/images/gallery2.jpg" alt="Restauracja">
            <img src="<?php echo $base_url; ?>/assets/images/gallery3.jpg" alt="SPA">
        </div>
    </div>
</div>

<div class="cta-section">
    <div class="container">
        <h2 class="with-background">Masz pytania? Skontaktuj się z nami!</h2>
        <p>Nasz zespół jest dostępny 24/7, aby pomóc Ci w każdej sprawie.</p>
        <a href="<?php echo $base_url; ?>/kontakt.php" class="btn-secondary">Skontaktuj się z nami</a>
    </div>
</div>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
