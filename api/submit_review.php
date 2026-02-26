<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to submit a review.");
    }

    $userId = $_SESSION['user_id'];
    $businessId = isset($_POST['business_id']) ? intval($_POST['business_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($businessId > 0 && $rating >= 1 && $rating <= 5 && !empty($comment)) {
        $checkStmt = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND business_id = ?");
        $checkStmt->bind_param("ii", $userId, $businessId);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            echo "<script>alert('You have already reviewed this business.'); window.location.href='../business.php?id=$businessId';</script>";
            exit;
        }

        $imagePath = null;
        if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fileType = $_FILES['review_image']['type'];
            $fileSize = $_FILES['review_image']['size'];

            if (in_array($fileType, $allowed) && $fileSize <= 5 * 1024 * 1024) { // Max 5MB
                $ext = pathinfo($_FILES['review_image']['name'], PATHINFO_EXTENSION);
                $newName = 'review_' . $userId . '_' . $businessId . '_' . time() . '.' . $ext;
                $uploadDir = '../uploads/reviews/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $destination = $uploadDir . $newName;
                if (move_uploaded_file($_FILES['review_image']['tmp_name'], $destination)) {
                    $imagePath = 'uploads/reviews/' . $newName;
                }
            }
        }

        $stmt = $conn->prepare("INSERT INTO reviews (user_id, business_id, rating, comment, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $userId, $businessId, $rating, $comment, $imagePath);

        if ($stmt->execute()) {
            header("Location: ../business.php?id=" . $businessId);
            exit;
        } else {
            die("Error submitting review: " . $conn->error);
        }
    } else {
        die("Invalid form data. Please ensure the rating and comment are correctly filled.");
    }
} else {
    die("Invalid request method.");
}
?>
