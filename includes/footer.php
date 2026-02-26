<footer class="foot">
    <div class="container">
        <div class="foot-grid">
            <div class="foot-brand">
                <div class="foot-logo-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24">
                        <rect x="2" y="2" width="20" height="20" rx="3" fill="none" stroke="currentColor"
                            stroke-width="2" />
                        <rect x="8" y="8" width="8" height="8" rx="1" fill="currentColor" />
                    </svg>
                </div>
                <div class="foot-brand-text">
                    <span class="logo-text">Khoj</span>
                    <p>Nepal's trusted business discovery platform.</p>
                </div>
            </div>
            <div class="foot-col">
                <h4>Discover</h4>
                <a href="<?php echo $pagePrefix; ?>explore.php?q=Restaurants">Restaurants</a>
                <a href="<?php echo $pagePrefix; ?>explore.php?q=Healthcare">Healthcare</a>
                <a href="<?php echo $pagePrefix; ?>explore.php?q=Salons">Salons</a>
                <a href="<?php echo $pagePrefix; ?>explore.php?q=Hotels">Hotels</a>
                <a href="<?php echo $pagePrefix; ?>explore.php?q=Services">Services</a>
                <a href="<?php echo $pagePrefix; ?>explore.php?q=Shopping">Shopping</a>
            </div>
            <div class="foot-col">
                <h4>Business</h4>
                <a href="<?php echo $pagePrefix; ?>business-signup.php">Add a Business</a>
                <a href="<?php echo $pagePrefix; ?>claim.php">Claim your business</a>

            </div>
            <div class="foot-col">
                <h4>Khoj</h4>
                <a href="<?php echo $pagePrefix; ?>about.php">About</a>
                <a href="<?php echo $pagePrefix; ?>contact.php">Contact</a>

            </div>
        </div>
        <div class="foot-bottom">&copy; 2026 Khoj</div>
    </div>
</footer>

<script src="<?php echo $pagePrefix; ?>js/script.js?v=<?php echo time(); ?>"></script>
</body>

</html>