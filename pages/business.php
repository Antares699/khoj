<?php
require_once '../includes/config.php';

// Include Leaflet for Maps
$leaflet_css = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>';
$leaflet_js = '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>';


$bizId = $_GET['id'] ?? null;
if (!$bizId) {
    header("Location: /pages/explore.php");
    exit;
}

// Fetch Business Details
$stmt = $conn->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->bind_param("i", $bizId);
$stmt->execute();
$bizResult = $stmt->get_result();

if ($bizResult->num_rows === 0) {
    header("Location: /pages/explore.php");
    exit;
}
$biz = $bizResult->fetch_assoc();

// Decode attributes
$attributes = [];
if (!empty($biz['attributes'])) {
    $attributes = json_decode($biz['attributes'], true);
}

// Fetch Reviews
$revStmt = $conn->prepare("
    SELECT r.*, u.first_name, u.last_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.business_id = ? 
    ORDER BY r.created_at DESC
");
$revStmt->bind_param("i", $bizId);
$revStmt->execute();
$reviews = $revStmt->get_result();

// Calculate Average Rating
$totalRating = 0;
$revCount = $reviews->num_rows;
$allReviews = [];

while ($r = $reviews->fetch_assoc()) {
    $allReviews[] = $r;
    $totalRating += $r['rating'];
}
$avgRating = $revCount > 0 ? round($totalRating / $revCount, 1) : 0;
$roundedRating = round($avgRating);

// Determine Cover Image
$imgUrl = '../assets/images/defaults/himalayan-kitchen.png';
$cat = strtolower($biz['category']);
if (strpos($cat, 'hotel') !== false)
    $imgUrl = '../assets/images/defaults/hotel-barahi.jpg';
if (strpos($cat, 'healthcare') !== false)
    $imgUrl = '../assets/images/defaults/nepal-mediciti.jpg';
if (strpos($cat, 'salon') !== false || strpos($cat, 'beauty') !== false)
    $imgUrl = '../assets/images/defaults/hair-studio.jpg';
if (strpos($cat, 'shopping') !== false)
    $imgUrl = '../assets/images/defaults/bhatbhateni.jpg';
if (strpos($cat, 'service') !== false)
    $imgUrl = '../assets/images/defaults/quick-fix.jpeg';

// Helper for tags
function renderTagHtml($attributes)
{
    $tagsHtml = '';

    // Check open status
    if (!empty($attributes['opening_hours'])) {
        $hoursStr = $attributes['opening_hours'];
        $isOpen = false;
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
                $isOpen = true; // Fallback
            }
        }

        if ($isOpen) {
            $tagsHtml .= '<span class="tag" style="background:#e8f5e9;color:#2e7d32;border-color:#c8e6c9;font-weight:600;">Open Now</span>';
        } else {
            $tagsHtml .= '<span class="tag" style="background:#ffebee;color:#c62828;border-color:#ffcdd2;font-weight:600;">Closed</span>';
        }
    }

    if (!empty($attributes['cuisine'])) {
        foreach ($attributes['cuisine'] as $c) {
            $tagsHtml .= '<span class="tag">' . htmlspecialchars($c) . '</span>';
        }
    }
    if (!empty($attributes['has_wifi']))
        $tagsHtml .= '<span class="tag">Free Wi-Fi</span>';
    if (!empty($attributes['outdoor_seating']))
        $tagsHtml .= '<span class="tag">Outdoor Seating</span>';
    if (!empty($attributes['wheelchair_accessible']))
        $tagsHtml .= '<span class="tag">Wheelchair Accessible</span>';
    if (!empty($attributes['delivery']))
        $tagsHtml .= '<span class="tag">Delivery</span>';
    if (!empty($attributes['accepts_credit_cards']))
        $tagsHtml .= '<span class="tag">Card Accepted</span>';
    if (!empty($attributes['diet_vegan']))
        $tagsHtml .= '<span class="tag" style="background:#e8f5e9;color:#2e7d32;">Vegan</span>';
    if (!empty($attributes['diet_vegetarian']))
        $tagsHtml .= '<span class="tag" style="background:#e8f5e9;color:#2e7d32;">Vegetarian</span>';
    if (!empty($attributes['diet_halal']))
        $tagsHtml .= '<span class="tag" style="background:#e8f5e9;color:#2e7d32;">Halal</span>';

    return $tagsHtml;
}

require_once '../includes/header.php';
?>

<div class="dot-grid"></div>

<div class="biz-profile-hero" style="background-image: url('<?php echo $imgUrl; ?>');">
    <div class="container">
        <span class="biz-profile-cat">
            <?php echo htmlspecialchars($biz['category']); ?>
        </span>
        <h1 class="biz-profile-title">
            <?php echo htmlspecialchars($biz['name']); ?>
        </h1>

        <div class="biz-profile-meta">
            <div class="biz-profile-rating">
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <?php echo $i < $roundedRating ? '★' : '☆'; ?>
                <?php endfor; ?>
                <span>
                    <?php echo $avgRating; ?> (<?php echo $revCount; ?> reviews)
                </span>
            </div>
            <span style="opacity: 0.8;">&bull;</span>
            <span class="biz-profile-loc">
                <?php echo htmlspecialchars($biz['location']); ?>
            </span>
        </div>

        <div class="biz-profile-tags">
            <?php echo renderTagHtml($attributes); ?>
        </div>
    </div>
</div>

<div class="biz-content-wrap">

    <!-- Main Info & Reviews -->
    <div class="biz-main-content">
        <section class="biz-section">
            <h2 class="biz-section-title">About this <?php echo htmlspecialchars($biz['category']); ?></h2>
            <p class="biz-section-desc">
                <?php echo nl2br(htmlspecialchars($biz['description'])); ?>
            </p>
            <?php if (!empty($biz['website'])): ?>
                <?php
                $websiteUrl = $biz['website'];
                if (!preg_match('/^https?:\/\//i', $websiteUrl)) {
                    $websiteUrl = 'https://' . $websiteUrl;
                }
                ?>
                <div style="margin-top: 20px;">
                    <a href="<?php echo htmlspecialchars($websiteUrl); ?>" target="_blank" rel="noopener noreferrer"
                        class="filter-btn" style="text-decoration: none; display: inline-block;">Visit Website</a>
                </div>
            <?php endif; ?>
        </section>

        <hr class="biz-divider">

        <section>
            <div class="biz-reviews-header">
                <h2 class="biz-section-title" style="margin:0;">Reviews</h2>
                <a href="#write-review" class="biz-reviews-link">Write a Review</a>
            </div>

            <div class="biz-reviews-list">
                <?php if (empty($allReviews)): ?>
                    <p class="biz-section-desc">No reviews yet. Be the first to review!</p>
                <?php else: ?>
                    <?php foreach ($allReviews as $r): ?>
                        <div class="biz-review-card">
                            <div class="biz-review-head">
                                <div>
                                    <strong class="biz-reviewer-name">
                                        <?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?>
                                    </strong>
                                    <span class="biz-review-date">
                                        <?php echo date('M d, Y', strtotime($r['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="biz-review-stars">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <?php echo $i < $r['rating'] ? '★' : '☆'; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="biz-review-comment">
                                <?php echo nl2br(htmlspecialchars($r['comment'])); ?>
                            </p>
                            <?php if (!empty($r['image_path'])): ?>
                                <div style="margin-top: 12px;">
                                    <img src="<?php echo htmlspecialchars($r['image_path']); ?>" alt="Review photo"
                                        style="max-width: 100%; max-height: 300px; border-radius: 8px; object-fit: cover; cursor: pointer;"
                                        onclick="window.open(this.src, '_blank')">
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div id="write-review" class="biz-review-form-box">
                <h3>Write a Review</h3>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="../api/submit_review.php" method="POST" enctype="multipart/form-data"
                        class="biz-review-form">
                        <input type="hidden" name="business_id" value="<?php echo $bizId; ?>">
                        <div class="biz-form-group">
                            <label>Rating</label>
                            <select name="rating" required>
                                <option value="5">★★★★★ - Excellent</option>
                                <option value="4">★★★★☆ - Very Good</option>
                                <option value="3">★★★☆☆ - Average</option>
                                <option value="2">★★☆☆☆ - Poor</option>
                                <option value="1">★☆☆☆☆ - Terrible</option>
                            </select>
                        </div>
                        <div class="biz-form-group">
                            <label>Comment</label>
                            <textarea name="comment" required rows="4" placeholder="Share your experience..."></textarea>
                        </div>
                        <div class="biz-form-group">
                            <label>Add a Photo (optional)</label>
                            <div class="biz-file-upload" id="uploadZone">
                                <input type="file" name="review_image" accept="image/*" id="reviewImageInput">
                                <div class="biz-file-upload-icon">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                                <p class="biz-file-upload-text"><span>Click to upload</span> or drag and drop</p>
                                <p class="biz-file-upload-hint">JPG, PNG, WebP or GIF (max 5MB)</p>
                            </div>
                            <img id="reviewImagePreview"
                                style="display:none; margin-top: 12px; max-width: 200px; max-height: 150px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border);">
                        </div>
                        <button type="submit" class="filter-btn" style="align-self: flex-start;">Post Review</button>
                    </form>
                    <script>
                        document.getElementById('reviewImageInput').addEventListener('change', function (e) {
                            const preview = document.getElementById('reviewImagePreview');
                            const file = e.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function (ev) {
                                    preview.src = ev.target.result;
                                    preview.style.display = 'block';
                                };
                                reader.readAsDataURL(file);
                            } else {
                                preview.style.display = 'none';
                            }
                        });
                    </script>
                <?php else: ?>
                    <p class="biz-section-desc">
                        <a href="<?php echo $pagePrefix; ?>login.php" class="biz-reviews-link">Log in</a> to write a review.
                    </p>
                <?php endif; ?>
            </div>

        </section>
    </div>

    <!-- Sidebar Info -->
    <div class="biz-sidebar">
        <div class="biz-sidebar-card">
            <h3>Business Info</h3>

            <?php if (!empty($biz['phone'])): ?>
                <div class="biz-sidebar-row">
                    <strong style="color:var(--charcoal);">Phone:</strong>
                    <span>
                        <?php echo htmlspecialchars($biz['phone']); ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="biz-sidebar-row">
                <strong style="color:var(--charcoal);">Address:</strong>
                <span>
                    <?php echo htmlspecialchars($biz['location']); ?>
                </span>
            </div>

            <?php if (!empty($attributes['opening_hours'])): ?>
                <div class="biz-sidebar-row">
                    <strong style="color:var(--charcoal);">Hours:</strong>
                    <div style="display:flex; flex-direction:column; gap:4px;">
                        <?php
                        $raw_hours = $attributes['opening_hours'];
                        if (strtolower($raw_hours) === '24/7') {
                            echo '<span>Monday - Sunday</span><span style="display:block; font-weight:600; color:var(--charcoal);">Open 24 Hours</span>';
                        } else {
                            // Basic OSM Parsing: "Su-Fr 09:00-22:00" -> "Sunday - Friday" / "9:00 AM - 10:00 PM"
                            $days_map = [
                                'Mo' => 'Monday',
                                'Tu' => 'Tuesday',
                                'We' => 'Wednesday',
                                'Th' => 'Thursday',
                                'Fr' => 'Friday',
                                'Sa' => 'Saturday',
                                'Su' => 'Sunday'
                            ];

                            $formatted_schedule = HTMLSpecialChars($raw_hours); // Default
                    
                            // Regex to catch: "Su-Fr 09:00-22:00" or just "09:00-22:00"
                            if (preg_match('/^([A-Za-z]{2}(?:-[A-Za-z]{2})?)?\s*(\d{2}:\d{2})-(\d{2}:\d{2})$/', trim($raw_hours), $matches)) {
                                $days_part = $matches[1];
                                $start_time = date('g:i A', strtotime($matches[2]));
                                $end_time = date('g:i A', strtotime($matches[3]));

                                // Parse Days
                                $pretty_days = "Mon - Sun";
                                if (!empty($days_part)) {
                                    if (strpos($days_part, '-') !== false) {
                                        $dp = explode('-', $days_part);
                                        $pretty_days = ($days_map[$dp[0]] ?? $dp[0]) . ' - ' . ($days_map[$dp[1]] ?? $dp[1]);
                                    } else {
                                        $pretty_days = $days_map[$days_part] ?? $days_part;
                                    }
                                }

                                echo '<span style="color:var(--gray-500);">' . $pretty_days . '</span>';
                                echo '<span style="display:block; font-weight:600; color:var(--charcoal); margin-top:2px; white-space:nowrap;">' . $start_time . ' - ' . $end_time . '</span>';
                            } else {
                                // If format is too complex/weird, just echo the raw string safely
                                echo '<span>' . $formatted_schedule . '</span>';
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($biz['lat']) && !empty($biz['lon'])): ?>
                <div style="margin-top: 24px; border-top: 1px solid var(--border); padding-top: 24px;">
                    <strong
                        style="display: block; color:var(--charcoal); font-size: 16px; margin-bottom: 12px;">Location</strong>
                    <div id="biz-map"
                        style="width: 100%; height: 250px; border-radius: 8px; border: 1px solid var(--border); z-index: 1;">
                    </div>
                </div>
            <?php endif; ?>

        </div> <!-- end .biz-sidebar-card -->
    </div> <!-- end .biz-sidebar -->

</div> <!-- end .biz-content-wrap -->

<?php if (!empty($biz['lat']) && !empty($biz['lon'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var lat = <?php echo floatval($biz['lat']); ?>;
            var lon = <?php echo floatval($biz['lon']); ?>;
            var bizName = "<?php echo addslashes($biz['name']); ?>";

            // Initialize the map
            var map = L.map('biz-map').setView([lat, lon], 14);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Add a marker with the business name
            L.marker([lat, lon]).addTo(map)
                .bindPopup("<b>" + bizName + "</b>")
                .openPopup();

            // Force Leaflet to recalculate the map size after the container is fully rendered
            setTimeout(function () {
                map.invalidateSize();
            }, 100);
        });
    </script>
<?php endif; ?>
<?php require_once '../includes/footer.php'; ?>
