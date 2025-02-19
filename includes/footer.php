<footer class="stardust-footer">
    <div class="footer-content">
        <!-- Sekcja o hotelu -->
        <div class="footer-about">
            <h3>O nas</h3>
            <p>Stardust Hotel to wyjątkowe miejsce oferujące komfortowy wypoczynek i niezapomniane doświadczenia w magicznej atmosferze.</p>
        </div>

        <!-- Szybkie linki -->
        <div class="footer-links">
            <h3>Szybkie linki</h3>
            <ul>
                <li><a href="<?php echo $base_url; ?>/index.php">Strona główna</a></li>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <li><a href="<?php echo $base_url; ?>/pages/rezerwacje/rezerwacje.php">Rezerwacje</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base_url; ?>/pages/rezerwacje/rezerwacje_public.php">Rezerwacje</a></li>
                <?php endif; ?>
                <li><a href="<?php echo $base_url; ?>/kontakt.php">Kontakt</a></li>
            </ul>
        </div>

        <!-- Kontakt -->
        <div class="footer-contact">
            <h3>Kontakt</h3>
            <p><strong>Adres:</strong> Ul. Gwiezdna 12, Kraków, Polska</p>
            <p><strong>Telefon:</strong> +48 123 456 789</p>
            <p><strong>Email:</strong> <a href="mailto:kontakt@stardusthotel.pl">kontakt@stardusthotel.pl</a></p>
        </div>

        <!-- Social media -->
        <div class="footer-social">
            <h3>Śledź nas</h3>
            <ul>
                <li><a href="#"><img src="<?php echo $base_url; ?>/assets/images/facebook-icon.png" alt="Facebook"> Facebook</a></li>
                <li><a href="#"><img src="<?php echo $base_url; ?>/assets/images/twitter-icon.png" alt="Twitter"> Twitter</a></li>
                <li><a href="#"><img src="<?php echo $base_url; ?>/assets/images/instagram-icon.png" alt="Instagram"> Instagram</a></li>
            </ul>
        </div>

    </div>

    <!-- Dolna sekcja -->
    <div class="footer-bottom">
        <p>© 2025 Stardust Hotel. Wszystkie prawa zastrzeżone.</p>
        <p>Projekt strony: <a href="#">Gwieździsta Noc</a></p>
    </div>

</footer>
</body>
</html>
