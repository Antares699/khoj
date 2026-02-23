<?php require_once 'includes/header.php'; ?>

<?php
// Fetch distinct categories from the database for the sidebar
require_once 'includes/config.php';
$catResult = $conn->query("SELECT DISTINCT category FROM businesses ORDER BY category ASC");
$categories = [];
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row['category'];
}
?>

<main class="explore-container">
    <!-- Sidebar Filters -->
    <aside class="explore-sidebar">
        <div class="filter-group">
            <h4>Minimum Rating</h4>
            <div class="star-rating-filter" id="ratingFilter">
                <span class="star-pick" data-rating="1">☆</span>
                <span class="star-pick" data-rating="2">☆</span>
                <span class="star-pick" data-rating="3">☆</span>
                <span class="star-pick" data-rating="4">☆</span>
                <span class="star-pick" data-rating="5">☆</span>
                <span class="rating-label" id="ratingLabel"></span>
            </div>
        </div>

        <div class="filter-group">
            <h4>Features</h4>
            <div class="checkbox-list" id="featureFilter">
                <label class="checkbox-item"><input type="checkbox" value="has_wifi"> Free Wi-Fi</label>
                <label class="checkbox-item"><input type="checkbox" value="open_now"> Open Now</label>
            </div>
        </div>

        <div class="filter-group">
            <h4>Category</h4>
            <div class="foot-col" style="display:flex; flex-direction:column; gap:12px;" id="categoryFilterLinks">
                <?php $currentQ = $_GET['q'] ?? ''; ?>
                <a href="?q=Restaurants&loc=<?php echo urlencode($_GET['loc'] ?? 'Kathmandu'); ?>"
                    class="cat-sidebar-link <?php echo $currentQ === 'Restaurants' ? 'active' : ''; ?>">Restaurants</a>
                <a href="?q=Healthcare&loc=<?php echo urlencode($_GET['loc'] ?? 'Kathmandu'); ?>"
                    class="cat-sidebar-link <?php echo $currentQ === 'Healthcare' ? 'active' : ''; ?>">Healthcare</a>
                <a href="?q=Salons&loc=<?php echo urlencode($_GET['loc'] ?? 'Kathmandu'); ?>"
                    class="cat-sidebar-link <?php echo $currentQ === 'Salons' ? 'active' : ''; ?>">Salons</a>
                <a href="?q=Hotels&loc=<?php echo urlencode($_GET['loc'] ?? 'Kathmandu'); ?>"
                    class="cat-sidebar-link <?php echo $currentQ === 'Hotels' ? 'active' : ''; ?>">Hotels</a>
                <a href="?q=Services&loc=<?php echo urlencode($_GET['loc'] ?? 'Kathmandu'); ?>"
                    class="cat-sidebar-link <?php echo $currentQ === 'Services' ? 'active' : ''; ?>">Services</a>
                <a href="?q=Shopping&loc=<?php echo urlencode($_GET['loc'] ?? 'Kathmandu'); ?>"
                    class="cat-sidebar-link <?php echo $currentQ === 'Shopping' ? 'active' : ''; ?>">Shopping</a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <section class="explore-main">
        <div class="explore-results-meta">
            <div>
                <h1>All Businesses</h1>
                <p class="results-count" id="resultsCount"
                    style="font-size:13px; color:var(--gray-500); margin-top:4px;"></p>
            </div>
            <div class="sort-dropdown">
                <label style="font-size: 14px; font-weight: 600; margin-right: 8px;">Sort:</label>
                <select id="sortSelect">
                    <option value="recommended">Recommended</option>
                    <option value="highest">Highest Rated</option>
                    <option value="most_reviewed">Most Reviewed</option>
                </select>
            </div>
        </div>

        <div class="res-list" id="resultsContainer">
            <!-- Dynamic results will be loaded here via JS -->
            <div style="padding: 40px; text-align: center; color: var(--gray-500);">Loading results...</div>
        </div>
    </section>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const q = urlParams.get('q') || '';
        const loc = urlParams.get('loc') || '';

        // Update the search bar UI to reflect what was searched
        const searchInputs = document.querySelectorAll(".h-search-box input");
        if (searchInputs.length >= 2) {
            searchInputs[0].value = q;
            searchInputs[1].value = loc;
        }

        // --- STATE ---
        let allResults = []; // full API response, filtered client-side
        let activeMinRating = 0;

        const resultsContainer = document.getElementById("resultsContainer");
        const resultsCount = document.getElementById("resultsCount");
        const h1 = document.querySelector(".explore-results-meta h1");
        const sortSelect = document.getElementById("sortSelect");

        if (q || loc) {
            h1.textContent = `Results for "${q}" in ${loc || 'Kathmandu'}`;
        }

        // --- INTERACTIVE STAR RATING ---
        const stars = document.querySelectorAll(".star-pick");
        const ratingLabel = document.getElementById("ratingLabel");

        function updateStarDisplay(rating) {
            stars.forEach(s => {
                const val = parseInt(s.dataset.rating);
                if (val <= rating) {
                    s.textContent = '\u2605';
                    s.classList.add("active");
                } else {
                    s.textContent = '\u2606';
                    s.classList.remove("active");
                }
            });
        }

        stars.forEach(star => {
            star.addEventListener("mouseenter", function () {
                updateStarDisplay(parseInt(this.dataset.rating));
            });

            star.addEventListener("click", function () {
                const clicked = parseInt(this.dataset.rating);
                if (activeMinRating === clicked) {
                    activeMinRating = 0;
                    updateStarDisplay(0);
                    ratingLabel.textContent = "";
                } else {
                    activeMinRating = clicked;
                    updateStarDisplay(clicked);
                    ratingLabel.textContent = clicked + "+ stars";
                }
                renderResults();
            });
        });

        document.getElementById("ratingFilter").addEventListener("mouseleave", function () {
            updateStarDisplay(activeMinRating);
        });

        // --- CHECKBOX FILTERS (Features) ---
        document.querySelectorAll("#featureFilter input").forEach(cb => {
            cb.addEventListener("change", renderResults);
        });

        // --- SORT DROPDOWN ---
        sortSelect.addEventListener("change", renderResults);

        // --- FETCH DATA ---
        fetch(`api/search_businesses.php?q=${encodeURIComponent(q)}&loc=${encodeURIComponent(loc)}`)
            .then(response => response.json())
            .then(data => {
                allResults = data;
                renderResults();
            })
            .catch(err => {
                resultsContainer.innerHTML = `<div style="padding: 40px; text-align: center; color: red;">Failed to load search results. Please try again.</div>`;
                console.error('Search API error:', err);
            });

        // --- RENDER FUNCTION ---
        function renderResults() {
            let filtered = [...allResults];

            // 1. Rating filter
            if (activeMinRating > 0) {
                filtered = filtered.filter(biz => parseFloat(biz.avg_rating) >= activeMinRating);
            }

            // Helper to check if currently open based on hours string (e.g. '10:00-22:00')
            function isBusinessOpen(hoursStr) {
                if (!hoursStr) return false;
                if (hoursStr.toLowerCase() === '24/7') return true;

                // Extremely simple parsing for MVP (assumes "HH:MM-HH:MM" format)
                try {
                    const parts = hoursStr.split('-');
                    if (parts.length !== 2) return true; // fallback if format is weird

                    const now = new Date();
                    const currentMinutes = now.getHours() * 60 + now.getMinutes();

                    const startParts = parts[0].trim().split(':');
                    const startMinutes = parseInt(startParts[0]) * 60 + parseInt(startParts[1] || 0);

                    const endParts = parts[1].trim().split(':');
                    let endMinutes = parseInt(endParts[0]) * 60 + parseInt(endParts[1] || 0);

                    if (endMinutes < startMinutes) endMinutes += 24 * 60; // wraps past midnight
                    let checkMinutes = currentMinutes;
                    if (currentMinutes < startMinutes && endMinutes > 24 * 60) checkMinutes += 24 * 60;

                    return checkMinutes >= startMinutes && checkMinutes <= endMinutes;
                } catch (e) {
                    return true; // fallback
                }
            }

            // 2. Feature filters
            const checkedFeatures = [];
            document.querySelectorAll("#featureFilter input:checked").forEach(cb => {
                checkedFeatures.push(cb.value);
            });
            if (checkedFeatures.length > 0) {
                filtered = filtered.filter(biz => {
                    if (!biz.attributes) return false;
                    return checkedFeatures.every(feat => {
                        if (feat === 'open_now') {
                            return isBusinessOpen(biz.attributes['opening_hours']);
                        }
                        return biz.attributes[feat] === true;
                    });
                });
            }

            // 3. Category filters (Now handled via server/URL params directly)
            // No client-side category filtering needed anymore since clicking
            // a category link reloads the page with ?q=Category

            // 4. Sorting
            const sortVal = sortSelect.value;
            if (sortVal === "highest") {
                filtered.sort((a, b) => parseFloat(b.avg_rating) - parseFloat(a.avg_rating));
            } else if (sortVal === "most_reviewed") {
                filtered.sort((a, b) => parseInt(b.review_count) - parseInt(a.review_count));
            }
            // 'recommended' = default API order (already sorted by rating desc)

            // Update count
            resultsCount.textContent = `${filtered.length} result${filtered.length !== 1 ? 's' : ''} found`;

            // Clear and render
            resultsContainer.innerHTML = '';

            if (filtered.length === 0) {
                resultsContainer.innerHTML = `<div style="padding: 40px; text-align: center; width: 100%; color: var(--gray-500);">No businesses found matching your filters.</div>`;
                return;
            }

            filtered.forEach((biz, index) => {
                // Determine cover image
                let imgUrl = 'Resources/Himalayan Kitchen.png';
                const cat = (biz.category || '').toLowerCase();
                if (cat.includes('hotel')) imgUrl = 'Resources/Hotel Barahi.jpg';
                if (cat.includes('healthcare')) imgUrl = 'Resources/Nepal Mediciti.JPG';
                if (cat.includes('salon') || cat.includes('beauty')) imgUrl = 'Resources/Hair Studio.jpg';
                if (cat.includes('shopping')) imgUrl = 'Resources/Bhatbhateni.jpg';
                if (cat.includes('service')) imgUrl = 'Resources/Quick Fix.jpeg';

                // Parse rating
                const rating = parseFloat(biz.avg_rating) || 0;
                const roundedRating = Math.round(rating);
                let starsHTML = '';
                for (let i = 0; i < 5; i++) {
                    starsHTML += i < roundedRating ? '★' : '☆';
                }

                // Parse attributes for tags
                let tagsHTML = `<span class="tag">${biz.category}</span>`;
                if (biz.attributes) {
                    if (biz.attributes.cuisine) {
                        biz.attributes.cuisine.forEach(c => {
                            tagsHTML += `<span class="tag">${c}</span>`;
                        });
                    }
                    if (biz.attributes.specialties) {
                        biz.attributes.specialties.forEach(s => {
                            tagsHTML += `<span class="tag">${s}</span>`;
                        });
                    }

                    // New Rich Features
                    if (biz.attributes.has_wifi) tagsHTML += `<span class="tag">Free Wi-Fi</span>`;
                    if (biz.attributes.outdoor_seating) tagsHTML += `<span class="tag">Outdoor Seating</span>`;
                    if (biz.attributes.wheelchair_accessible) tagsHTML += `<span class="tag">Wheelchair Accessible</span>`;
                    if (biz.attributes.delivery) tagsHTML += `<span class="tag">Delivery</span>`;
                    if (biz.attributes.accepts_credit_cards) tagsHTML += `<span class="tag">Card Accepted</span>`;

                    // Dietary Tags
                    if (biz.attributes.diet_vegan) tagsHTML += `<span class="tag" style="background:#e8f5e9;color:#2e7d32;">Vegan</span>`;
                    if (biz.attributes.diet_vegetarian) tagsHTML += `<span class="tag" style="background:#e8f5e9;color:#2e7d32;">Vegetarian</span>`;
                    if (biz.attributes.diet_halal) tagsHTML += `<span class="tag" style="background:#e8f5e9;color:#2e7d32;">Halal</span>`;
                }
                // Helper: Format OSM Hours to AM/PM string
                function formatHours(raw) {
                    if (!raw) return '';
                    if (raw.toLowerCase() === '24/7') return 'Mon - Sun, Open 24 Hours';

                    const daysMap = {
                        'Mo': 'Mon', 'Tu': 'Tue', 'We': 'Wed',
                        'Th': 'Thu', 'Fr': 'Fri', 'Sa': 'Sat', 'Su': 'Sun'
                    };

                    const match = raw.trim().match(/^([A-Za-z]{2}(?:-[A-Za-z]{2})?)?\s*(\d{2}:\d{2})-(\d{2}:\d{2})$/);
                    if (match) {
                        const daysPart = match[1] || '';
                        let prettyDays = "Mon - Sun";
                        if (daysPart) {
                            if (daysPart.includes('-')) {
                                const dp = daysPart.split('-');
                                prettyDays = (daysMap[dp[0]] || dp[0]) + ' - ' + (daysMap[dp[1]] || dp[1]);
                            } else {
                                prettyDays = daysMap[daysPart] || daysPart;
                            }
                        }

                        const formatTime = (timeStr) => {
                            let [h, m] = timeStr.split(':');
                            h = parseInt(h);
                            const ampm = h >= 12 ? 'PM' : 'AM';
                            h = h % 12;
                            h = h ? h : 12; // 0 -> 12
                            return `${h}:${m} ${ampm}`;
                        };

                        return `${prettyDays}, ${formatTime(match[2])} - ${formatTime(match[3])}`;
                    }
                    return raw; // Fallback
                }

                // Append parsed hours tag if available
                if (biz.attributes && biz.attributes.opening_hours) {
                    tagsHTML += `<span class="tag">Hours: ${formatHours(biz.attributes.opening_hours)}</span>`;
                }

                // Add Open status tag automatically if Open
                if (biz.attributes && isBusinessOpen(biz.attributes.opening_hours)) {
                    tagsHTML = `<span class="tag" style="background:#e8f5e9;color:#2e7d32;border-color:#c8e6c9;font-weight:600;">Open</span>` + tagsHTML;
                } else if (biz.attributes && biz.attributes.opening_hours) {
                    tagsHTML = `<span class="tag" style="background:#ffebee;color:#c62828;border-color:#ffcdd2;font-weight:600;">Closed</span>` + tagsHTML;
                }

                const cardHTML = `
            <article class="res-card">
                <div class="res-img" style="background-image: url('${imgUrl}');"></div>
                <div class="res-info">
                    <h3><a href="business.php?id=${biz.id}" style="color:inherit;text-decoration:none;">${index + 1}. ${biz.name}</a></h3>
                    <div class="res-rating">
                        <span class="stars-row">${starsHTML}</span>
                        <span class="res-meta">${biz.avg_rating} (${biz.review_count} reviews)</span>
                    </div>
                    <div class="res-meta">
                        ${biz.location}
                    </div>
                    <p class="res-review">"${biz.description}"</p>
                    <div class="res-tags">
                        ${tagsHTML}
                    </div>
                </div>
            </article>`;

                resultsContainer.insertAdjacentHTML('beforeend', cardHTML);
            });
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
