<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $pageTitle = $pageTitle ?? "Khoj – Discover Nepal's Best";
    $pageDesc = $pageDesc ?? "Khoj is the modern, clean platform to discover the best restaurants, healthcare, hotels, and more in Nepal.";
    $pageImage = $pageImage ?? "http://localhost/Khoj/Resources/default-og.png"; // Replace with real domain later
    $pageUrl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    ?>
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDesc); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($pageUrl); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDesc); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($pageImage); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo htmlspecialchars($pageUrl); ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($pageDesc); ?>">
    <meta property="twitter:image" content="<?php echo htmlspecialchars($pageImage); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="./style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233366FF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z'%3E%3C/path%3E%3Cpolyline points='3.27 6.96 12 12.01 20.73 6.96'%3E%3C/polyline%3E%3Cline x1='12' y1='22.08' x2='12' y2='12'%3E%3C/line%3E%3C/svg%3E">
    <!-- Optional Map Libraries -->
    <?php if (isset($leaflet_css))
        echo $leaflet_css; ?>
    <?php if (isset($leaflet_js))
        echo $leaflet_js; ?>
</head>

<body>

    <div class="dot-grid"></div>

    <nav class="nav">
        <a href="./" class="logo">
            <svg width="28" height="28" viewBox="0 0 24 24">
                <rect x="2" y="2" width="20" height="20" rx="3" fill="none" stroke="currentColor" stroke-width="2" />
                <rect x="8" y="8" width="8" height="8" rx="1" fill="currentColor" />
            </svg>
            <span>Khoj</span>
        </a>
        <div class="nav-center">
            <?php if (basename($_SERVER['PHP_SELF']) == 'explore.php'): ?>
                <div class="header-search-row">
                    <div class="h-search-box autocomplete-wrapper">
                        <input type="text" placeholder="Search businesses"
                            value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" id="headerSearchInput"
                            autocomplete="off">
                        <div id="header-autocomplete-results" class="autocomplete-results"
                            style="display: none; top: 110%;"></div>
                    </div>
                    <div class="h-search-box">
                        <input type="text" placeholder="Location" value="Kathmandu">
                    </div>
                    <button class="h-search-go">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <div class="nav-right">
            <div class="nav-dropdown">
                <button class="nav-drop-btn">For Businesses
                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 1L5 5L9 1" />
                    </svg>
                </button>
                <div class="nav-drop-content">
                    <a href="business-signup.php">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        Add a Business
                    </a>
                    <a href="claim.php">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Claim your business
                    </a>
                    <a href="login.php?role=business">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Log in to Business Account
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'business_owner'): ?>
                    <a href="add-business.php" class="link-cta" style="background: var(--blue); margin-right: 10px;">+ Add
                        Business</a>
                <?php endif; ?>
                <span style="font-size: 13px; color: var(--gray-500); font-weight: 600;">Hi,
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="logout.php" class="link-login" style="color: #EF4444;">Log out</a>
            <?php else: ?>
                <a href="login.php" class="link-login">Log in</a>
                <a href="register.php" class="link-cta">Sign up</a>
            <?php endif; ?>
        </div>
        <button class="burger" id="burger">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="2" y1="5" x2="18" y2="5" />
                <line x1="2" y1="10" x2="18" y2="10" />
                <line x1="2" y1="15" x2="18" y2="15" />
            </svg>
        </button>
    </nav>

    <div class="mob-panel" id="mobPanel">
        <a href="./">Home</a>
        <a href="#">Explore</a>
        <a href="#">Categories</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">Log out</a>
        <?php else: ?>
            <a href="login.php">Log in</a>
            <a href="register.php">Sign up</a>
        <?php endif; ?>
    </div>

    <?php if (basename($_SERVER['PHP_SELF']) == 'explore.php'): ?>
        <!-- Autocomplete JS is now handled in script.js -->
    <?php endif; ?>

    <!-- Unified Site Javascript -->
    <script defer src="./script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
