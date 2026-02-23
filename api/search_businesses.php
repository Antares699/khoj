<?php
// api/search_businesses.php
require_once '../includes/config.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$loc = isset($_GET['loc']) ? trim($_GET['loc']) : '';

// Base query joining businesses and reviews
$sql = "SELECT b.*, 
        IFNULL(AVG(r.rating), 0) as avg_rating, 
        COUNT(r.id) as review_count
        FROM businesses b
        LEFT JOIN reviews r ON b.id = r.business_id
        WHERE 1=1 ";

$params = [];
$types = "";

if ($query !== '') {
    $sql .= " AND (b.name LIKE ? OR b.category LIKE ? OR JSON_EXTRACT(b.attributes, '$.cuisine') LIKE ? OR JSON_EXTRACT(b.attributes, '$.specialties') LIKE ?) ";
    $likeQuery = "%" . $query . "%";
    $params[] = $likeQuery;
    $params[] = $likeQuery;
    $params[] = $likeQuery;
    $params[] = $likeQuery;
    $types .= "ssss";
}

if ($loc !== '') {
    $sql .= " AND b.location LIKE ? ";
    $likeLoc = "%" . $loc . "%";
    $params[] = $likeLoc;
    $types .= "s";
}

$sql .= " GROUP BY b.id ORDER BY avg_rating DESC, review_count DESC LIMIT 50";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$businesses = [];
while ($row = $result->fetch_assoc()) {
    // Decode JSON attributes back to array for the frontend
    if (!empty($row['attributes'])) {
        $row['attributes'] = json_decode($row['attributes'], true);
    }
    // Format rating to 1 decimal place
    $row['avg_rating'] = number_format((float) $row['avg_rating'], 1, '.', '');
    $businesses[] = $row;
}

echo json_encode($businesses);
$stmt->close();
$conn->close();
?>
