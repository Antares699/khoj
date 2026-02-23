<?php
// seed_osm.php
require 'includes/config.php';

echo "<h2>Khoj - OpenStreetMap Data Seeder</h2>";
echo "<p>Starting multi-pass seeding for Kathmandu district...</p>";

// 1. Create a "System Admin" Business Owner to own these seeded listings
$adminEmail = 'system@Khoj.local';
$stmt = $conn->prepare("SELECT id FROM business_owners WHERE email = ?");
$stmt->bind_param("s", $adminEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $ownerId = $result->fetch_assoc()['id'];
    echo "<p>Using existing System Owner (ID: $ownerId)</p>";
} else {
    $password = password_hash('SeedPassword123!', PASSWORD_BCRYPT);
    $insertOwner = $conn->prepare("INSERT INTO business_owners (first_name, last_name, email, password, job_title) VALUES ('System', 'Seeder', ?, ?, 'Data Bot')");
    $insertOwner->bind_param("ss", $adminEmail, $password);

    if ($insertOwner->execute()) {
        $ownerId = $conn->insert_id;
        echo "<p>Created new System Owner (ID: $ownerId)</p>";
    } else {
        die("Fatal Error: Could not create System Owner.");
    }
}

// 1B. Create a "Dummy User" to own the generated reviews
$dummyEmail = 'reviewer@Khoj.local';
$stmtUser = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmtUser->bind_param("s", $dummyEmail);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows > 0) {
    $dummyUserId = $resultUser->fetch_assoc()['id'];
} else {
    $password = password_hash('Pass123!', PASSWORD_BCRYPT);
    $insertUser = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name) VALUES ('LocalGuide', ?, ?, 'Local', 'Guide')");
    $insertUser->bind_param("ss", $dummyEmail, $password);

    if ($insertUser->execute()) {
        $dummyUserId = $conn->insert_id;
    } else {
        die("Fatal Error: Could not create Dummy User.");
    }
}

// 2. Define our target Quotas and Overpass Queries
// We are looking within the boundaries of Kathmandu district
// Bbox roughly: 27.65, 85.25 (SW) to 27.75, 85.38 (NE)
$bbox = "27.65,85.25,27.75,85.38";

// The Multi-Pass Strategy definitions:
$categories = [
    'Restaurants & Cafes' => [
        'limit' => 20,
        'query' => 'node["amenity"~"restaurant|cafe"]["phone"](' . $bbox . ');',
        'db_category' => 'Restaurants'
    ],
    'Healthcare' => [
        'limit' => 10,
        'query' => 'node["amenity"~"hospital|clinic|pharmacy|dentist"]["name"](' . $bbox . ');',
        'db_category' => 'Healthcare'
    ],
    'Hotels' => [
        'limit' => 10,
        'query' => 'node["tourism"~"hotel|hostel|guest_house"]["name"](' . $bbox . ');',
        'db_category' => 'Hotels'
    ],
    'Shopping' => [
        'limit' => 10,
        'query' => 'node["shop"~"supermarket|mall|clothes|department_store"]["name"](' . $bbox . ');',
        'db_category' => 'Shopping'
    ],
    'Salons' => [
        'limit' => 5,
        'query' => 'node["shop"~"beauty|hairdresser|massage"]["name"](' . $bbox . ');',
        'db_category' => 'Salons'
    ],
    'Services' => [
        'limit' => 5,
        'query' => 'node["craft"~"electrician|plumber|handyman"]["name"](' . $bbox . '); node["shop"~"car_repair|laundry"]["name"](' . $bbox . ');',
        'db_category' => 'Services'
    ]
];

$totalInserted = 0;

// Prepare the insert statement
$insertBiz = $conn->prepare("INSERT INTO businesses (owner_id, name, slug, category, description, location, lat, lon, phone, website, attributes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
// ssss sss s s -> i s s s s s s s s (1 int, 8 strings = 9 fields)

// Helper to create a slug
function createSlug($string)
{
    $string = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $string . '-' . rand(1000, 9999); // Ensure uniqueness
}

// 3. Execute the queries
foreach ($categories as $groupName => $config) {
    echo "<h3>Fetching $groupName (Target: {$config['limit']})</h3>";

    // Build the Overpass Query (JSON format)
    $overpassQuery = "[out:json][timeout:25];(" . $config['query'] . ");out body " . $config['limit'] . ";>;out skel qt;";

    // API Call
    $url = "http://overpass-api.de/api/interpreter?data=" . urlencode($overpassQuery);

    // Use file_get_contents with a timeout context
    $context = stream_context_create(array(
        'http' => array('timeout' => 30)
    ));

    $response = @file_get_contents($url, false, $context);

    if ($response === FALSE) {
        echo "<p style='color:red;'>Failed to connect to Overpass API for $groupName.</p>";
        continue;
    }

    $data = json_decode($response, true);

    if (!isset($data['elements'])) {
        echo "<p>No results found for $groupName.</p>";
        continue;
    }

    $count = 0;
    foreach ($data['elements'] as $element) {
        if (!isset($element['tags']) || !isset($element['tags']['name'])) {
            continue; // Must have a name
        }

        $tags = $element['tags'];

        $name = $tags['name'] ?? 'Unknown Business';
        // Prefer English name if it exists and the local name looks messy
        if (isset($tags['name:en']) && preg_match('/[A-Za-z]/', $tags['name:en'])) {
            $name = $tags['name:en'];
        }

        $slug = createSlug($name);
        $category = $config['db_category'];

        // Extract coordinates from OSM element
        $lat = $element['lat'] ?? null;
        $lon = $element['lon'] ?? null;

        // Build Location String
        $locationParts = [];
        if (isset($tags['addr:street']))
            $locationParts[] = $tags['addr:street'];
        if (isset($tags['addr:city']))
            $locationParts[] = $tags['addr:city'];
        $location = empty($locationParts) ? "Kathmandu" : implode(', ', $locationParts);

        // Contact Info
        $phone = $tags['phone'] ?? ($tags['contact:phone'] ?? null);
        $website = $tags['website'] ?? ($tags['contact:website'] ?? null);

        // Default Description
        $description = "A highly-rated $category located in $location.";

        // --- BUILD THE JSON ATTRIBUTES PAYLOAD ---
        $attributes = [];

        if (isset($tags['cuisine'])) {
            $attributes['cuisine'] = explode(';', $tags['cuisine']);
        }
        if (isset($tags['opening_hours'])) {
            $attributes['opening_hours'] = $tags['opening_hours'];
        }
        if (isset($tags['internet_access']) && ($tags['internet_access'] == 'wlan' || $tags['internet_access'] == 'yes')) {
            $attributes['has_wifi'] = true;
        }
        if (isset($tags['outdoor_seating']) && $tags['outdoor_seating'] == 'yes') {
            $attributes['outdoor_seating'] = true;
        }
        if (isset($tags['wheelchair']) && $tags['wheelchair'] !== 'no') {
            $attributes['wheelchair_accessible'] = true;
        }
        if (isset($tags['delivery']) && $tags['delivery'] !== 'no') {
            $attributes['delivery'] = true;
        }

        // Dietary restrictions (mostly restaurants)
        if (isset($tags['diet:vegan']) && $tags['diet:vegan'] === 'yes') {
            $attributes['diet_vegan'] = true;
        }
        if (isset($tags['diet:vegetarian']) && $tags['diet:vegetarian'] === 'yes') {
            $attributes['diet_vegetarian'] = true;
        }
        if (isset($tags['diet:halal']) && $tags['diet:halal'] === 'yes') {
            $attributes['diet_halal'] = true;
        }

        // Payment attributes
        if (isset($tags['payment:credit_cards']) && $tags['payment:credit_cards'] === 'yes') {
            $attributes['accepts_credit_cards'] = true;
        }
        if (isset($tags['amenity']) && $tags['amenity'] == 'hospital' && isset($tags['healthcare:speciality'])) {
            $attributes['specialties'] = explode(';', $tags['healthcare:speciality']);
        }

        $jsonAttributes = json_encode($attributes);

        // Execute Insert (lat/lon can be null)
        $insertBiz->bind_param("isssssddsss", $ownerId, $name, $slug, $category, $description, $location, $lat, $lon, $phone, $website, $jsonAttributes);

        if ($insertBiz->execute()) {
            $count++;
            $totalInserted++;
            $bizId = $conn->insert_id;

            // Generate 1-5 Random Reviews for this business
            $numReviews = rand(1, 4);
            $comments = ["Great place!", "Really enjoyed my visit here.", "Will definitely come back.", "Highly recommended.", "Good service but a bit crowded.", "Perfect location.", "Amazing experience!"];

            for ($i = 0; $i < $numReviews; $i++) {
                $rating = rand(3, 5); // Bias towards positive for demo
                $comment = $comments[array_rand($comments)];

                $insertReview = $conn->prepare("INSERT INTO reviews (user_id, business_id, rating, comment) VALUES (?, ?, ?, ?)");
                $insertReview->bind_param("iiis", $dummyUserId, $bizId, $rating, $comment);
                $insertReview->execute();
                $insertReview->close();
            }
        }
    }

    echo "<p style='color:green;'>Inserted $count $groupName.</p>";

    // Polite delay for OSM servers
    sleep(1);
}

echo "<h2>Done! Successfully seeded $totalInserted high-quality businesses across Kathmandu.</h2>";

$conn->close();
?>
