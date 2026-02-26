<?php
// business-signup.php
require_once '../includes/config.php';
$pagePrefix = '../';

$error = '';
$success = '';

// If user is already logged in, redirect to add-business (if owner) or home
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'business_owner') {
        header("Location: /pages/explore.php");
    } else {
        header("Location: /index.php");
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // User Info
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $job_title = trim($_POST['job_title']);

    // Business Info
    $biz_name = trim($_POST['biz_name']);
    $biz_city = trim($_POST['biz_city']);
    $biz_state = trim($_POST['biz_state']);
    // $biz_zip = trim($_POST['biz_zip']); // Removed
    $biz_category = $_POST['biz_category'];

    // Construct username skipped - business owners identify by email

    if (empty($email) || empty($password) || empty($biz_name)) {
        $error = "Please fill in all required fields.";
    } else {
        // Start Transaction
        $conn->begin_transaction();

        try {
            // 1. Create Business Owner
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO business_owners (first_name, last_name, email, password, phone, job_title) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $hashed, $phone, $job_title);
            $stmt->execute();
            $owner_id = $conn->insert_id;
            $stmt->close();

            // 2. Create Business
            // Combine location fields
            $location = "$biz_city, $biz_state";
            // Create slug
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $biz_name)));

            $stmt = $conn->prepare("INSERT INTO businesses (owner_id, name, slug, category, location, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $owner_id, $biz_name, $slug, $biz_category, $location, $phone);
            $stmt->execute();
            $stmt->close();

            // Commit
            $conn->commit();

            // Auto Login
            session_start();
            $_SESSION['user_id'] = $owner_id; // Using owner_id as user_id for session consistency 
            $_SESSION['user_type'] = 'business_owner'; // Distinguisher
            $_SESSION['username'] = $first_name; // Display name

            header("Location: /index.php");
            exit;

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            if (strpos($exception->getMessage(), 'Duplicate entry') !== false) {
                $error = "Email or Business Name already exists.";
            } else {
                $error = "Error: " . $exception->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner with Khoj</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>

<body class="biz-signup-body">

    <div class="nav">
        <a href="<?php echo $pagePrefix; ?>index.php" class="logo">
            <span>Khoj <span style="color: var(--blue);">Partner</span></span>
        </a>
        <div class="nav-right">
            <a href="<?php echo $pagePrefix; ?>login.php" class="link-login">Log in</a>
        </div>
    </div>

    <div class="biz-container">
        <div class="biz-header">
            <h1>Tell us about your business</h1>
            <p>Fill out the form below and start managing your presence on Khoj.</p>
        </div>

        <?php if ($error): ?>
            <div class="auth-error biz-error" style="margin-bottom: 30px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="business-signup.php" method="POST">
            <div class="biz-form-grid">

                <div class="biz-group">
                    <label class="biz-label">First Name <span class="required">*</span></label>
                    <input type="text" name="first_name" class="biz-input" required>
                </div>

                <div class="biz-group">
                    <label class="biz-label">Last Name <span class="required">*</span></label>
                    <input type="text" name="last_name" class="biz-input" required>
                </div>

                <div class="biz-group full-width">
                    <label class="biz-label">Email Address <span class="required">*</span></label>
                    <input type="email" name="email" class="biz-input" required>
                </div>

                <div class="biz-group full-width">
                    <label class="biz-label">Create Password <span class="required">*</span></label>
                    <input type="password" name="password" class="biz-input" required placeholder="Min. 6 characters">
                </div>

                <div class="biz-group">
                    <label class="biz-label">Your Phone Number <span class="required">*</span></label>
                    <input type="tel" name="phone" class="biz-input" required>
                </div>

                <div class="biz-group">
                    <label class="biz-label">Job Title</label>
                    <select name="job_title" class="biz-select">
                        <option>Owner</option>
                        <option>Finance/Accounting</option>
                        <option>Sales/Marketing</option>
                        <option>Other: Manager</option>
                        <option>Other: Non-Manager</option>
                    </select>
                </div>

                <div class="biz-group full-width">
                    <label class="biz-label">Business Name <span class="required">*</span></label>
                    <input type="text" name="biz_name" class="biz-input" required
                        value="<?php echo isset($_GET['biz_name']) ? htmlspecialchars($_GET['biz_name']) : ''; ?>">
                </div>

                <div class="biz-group full-width">
                    <label class="biz-label">Category <span class="required">*</span></label>
                    <select name="biz_category" class="biz-select" required>
                        <option value="Restaurant">Restaurant</option>
                        <option value="Hotel">Hotel</option>
                        <option value="Shopping">Shopping</option>
                        <option value="Services">Services</option>
                        <option value="Automotive">Automotive</option>
                        <option value="Beauty">Beauty & Spa</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Education">Education</option>
                        <option value="Finance">Finance</option>
                        <option value="Travel">Travel</option>
                        <option value="Food">Food</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="biz-group full-width">
                    <label class="biz-label">Business City <span class="required">*</span></label>
                    <input type="text" name="biz_city" class="biz-input" required>
                </div>

                <div class="biz-group">
                    <label class="biz-label">State / Province</label>
                    <input type="text" name="biz_state" class="biz-input">
                </div>



            </div>

            <button type="submit" class="biz-submit">Submit</button>

        </form>
    </div>

</body>

</html>
