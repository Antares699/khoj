<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<body>

    <div class="dot-grid"></div>

    <header class="hero">
        <div class="hero-inner">
            <div class="hero-left">
                <p class="hero-label">Business Directory</p>
                <h1>Discover.<br>Compare.<br><span>Decide.</span></h1>
                <p class="hero-desc">Nepal's most precise business search.<br>Curated businesses. Honest reviews.</p>

                <div class="search-row">
                    <div class="search-box autocomplete-wrapper">
                        <input type="text" placeholder="Search businesses" id="searchInput" autocomplete="off">
                        <div id="home-autocomplete-results" class="autocomplete-results" style="display: none;"></div>
                    </div>
                    <div class="search-box">
                        <input type="text" placeholder="Location" id="locationInput">
                    </div>
                    <button class="search-go" id="searchBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </button>
                </div>

                <div class="cat-row">
                    <a href="explore.php?q=Restaurants" class="cat-link">Restaurants</a>
                    <a href="explore.php?q=Healthcare" class="cat-link">Healthcare</a>
                    <a href="explore.php?q=Salons" class="cat-link">Salons</a>
                    <a href="explore.php?q=Hotels" class="cat-link">Hotels</a>
                    <a href="explore.php?q=Services" class="cat-link">Services</a>
                    <a href="explore.php?q=Shopping" class="cat-link">Shopping</a>
                </div>
            </div>
            <div class="hero-right">
                <div class="geo-shape geo-1">
                    <svg viewBox="0 0 100 100">
                        <rect x="10" y="10" width="80" height="80" rx="4" fill="none" stroke="#3366FF" stroke-width="1"
                            opacity="0.3" />
                    </svg>
                </div>
                <div class="geo-shape geo-2">
                    <svg viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#1A1A2E" stroke-width="1" opacity="0.2" />
                    </svg>
                </div>
                <div class="geo-shape geo-3">
                    <svg viewBox="0 0 100 100">
                        <polygon points="50,10 90,90 10,90" fill="none" stroke="#3366FF" stroke-width="1"
                            opacity="0.2" />
                    </svg>
                </div>
                <div class="stat-block sb-1">
                    <strong>2,400+</strong>
                    <span>businesses listed</span>
                </div>
                <div class="stat-block sb-2">
                    <strong>18K+</strong>
                    <span>verified reviews</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Filters Area Removed (Advanced filtering now happens on explore.php) -->

    <section class="listings">
        <div class="container">
            <div class="listings-top">
                <h2>Featured</h2>
            </div>
            <div class="card-grid">
                <?php
                $topBizQuery = "
                    SELECT b.*, 
                    IFNULL(AVG(r.rating), 0) as avg_rating, 
                    COUNT(r.id) as review_count
                    FROM businesses b
                    LEFT JOIN reviews r ON b.id = r.business_id
                    GROUP BY b.id
                    ORDER BY review_count DESC, avg_rating DESC
                    LIMIT 30
                ";
                $topBizResult = $conn->query($topBizQuery);

                if ($topBizResult && $topBizResult->num_rows > 0) {
                    $seenCategories = [];
                    $featuredCount = 0;

                    while ($biz = $topBizResult->fetch_assoc()) {
                        $catLower = strtolower(trim($biz['category']));

                        if (in_array($catLower, $seenCategories)) {
                            continue;
                        }

                        $seenCategories[] = $catLower;
                        $featuredCount++;
                        $cat = strtolower($biz['category']);
                        $imgUrl = './Resources/Himalayan Kitchen.png';
                        if (strpos($cat, 'hotel') !== false)
                            $imgUrl = './Resources/Hotel Barahi.jpg';
                        if (strpos($cat, 'healthcare') !== false)
                            $imgUrl = './Resources/Nepal Mediciti.JPG';
                        if (strpos($cat, 'salon') !== false || strpos($cat, 'beauty') !== false)
                            $imgUrl = './Resources/Hair Studio.jpg';
                        if (strpos($cat, 'shopping') !== false)
                            $imgUrl = './Resources/Bhatbhateni.jpg';
                        if (strpos($cat, 'service') !== false)
                            $imgUrl = './Resources/Quick Fix.jpeg';

                        $starsHtml = '';
                        for ($i = 0; $i < 5; $i++) {
                            $starsHtml .= $i < $rating ? '&#9733;' : '&#9734;';
                        }

                        $isOpen = false;
                        $hasHours = false;
                        $attributes = !empty($biz['attributes']) ? json_decode($biz['attributes'], true) : [];

                        if (!empty($attributes['opening_hours'])) {
                            $hasHours = true;
                            $hoursStr = $attributes['opening_hours'];
                            if (strtolower($hoursStr) === '24/7') {
                                $isOpen = true;
                            } else {
                                $parts = explode('-', $hoursStr);
                                if (count($parts) == 2) {
                                    date_default_timezone_set('Asia/Kathmandu');
                                    $nowStr = date('H:i');
                                    $start = trim($parts[0]);
                                    $end = trim($parts[1]);

                                    $startMins = (int) substr($start, 0, 2) * 60 + (int) substr($start, 3, 2);
                                    $endMins = (int) substr($end, 0, 2) * 60 + (int) substr($end, 3, 2);
                                    $nowMins = (int) substr($nowStr, 0, 2) * 60 + (int) substr($nowStr, 3, 2);

                                    if ($endMins < $startMins)
                                        $endMins += 24 * 60;
                                    $chkMins = $nowMins;
                                    if ($chkMins < $startMins && $endMins > 24 * 60)
                                        $chkMins += 24 * 60;

                                    if ($chkMins >= $startMins && $chkMins <= $endMins) {
                                        $isOpen = true;
                                    }
                                } else {
                                    $isOpen = true; // Fallback for complex OSM strings
                                }
                            }
                        }

                        $isDiet = (!empty(json_decode($biz['attributes'], true)['diet_vegan'])) ? 'Vegan Friendly' : '';
                        ?>
                        <article class="card" onclick="window.location.href='business.php?id=<?php echo $biz['id']; ?>'"
                            style="cursor:pointer;">
                            <div class="card-image" style="background-image: url('<?php echo $imgUrl; ?>');">
                                <?php if ($isDiet): ?>
                                    <span class="card-overlay-badge"><?php echo $isDiet; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="card-head">
                                    <span class="card-cat"><?php echo htmlspecialchars($biz['category']); ?></span>
                                </div>
                                <h3><a href="business.php?id=<?php echo $biz['id']; ?>"
                                        style="color:inherit; text-decoration:none;"><?php echo htmlspecialchars($biz['name']); ?></a>
                                </h3>
                                <p class="card-loc"><?php echo htmlspecialchars($biz['location']); ?></p>
                                <div class="card-rating">
                                    <span class="stars" style="color:#FFD700;"><?php echo $starsHtml; ?></span>
                                    <span class="count"><?php echo $biz['review_count']; ?> reviews</span>
                                </div>
                                <div class="card-foot" style="justify-content: flex-end;">
                                    <?php if ($isOpen): ?>
                                        <span class="open" style="color:#2e7d32; font-weight:700;">Open</span>
                                    <?php else: ?>
                                        <span class="closed" style="color:#c62828; font-weight:700;">Closed</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                        <?php
                        if ($featuredCount >= 6) {
                            break;
                        }
                    }
                } else {
                    echo "<p style='grid-column: 1 / -1; text-align:center; color: var(--gray-500);'>No featured businesses found.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <section class="metrics">
        <div class="container">
            <div class="metrics-grid">
                <div class="metric"><strong>2,400+</strong><span>Businesses</span></div>
                <div class="metric"><strong>18,000+</strong><span>Reviews</span></div>
                <div class="metric"><strong>50,000+</strong><span>Users</span></div>
                <div class="metric"><strong>75</strong><span>Districts</span></div>
            </div>
        </div>
    </section>



    <?php require_once 'includes/footer.php'; ?>
