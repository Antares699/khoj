<?php
session_start();
require_once 'includes/db_connect.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($email) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif (strlen($newPassword) < 6) {
        $error = "New password must be at least 6 characters.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New passwords do not match.";
    } else {
        // Try users table first
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        $userId = null;
        $hashedPassword = null;
        $userType = null;

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $hashedPassword);
            $stmt->fetch();
            $userType = 'user';
        } else {
            // Try business_owners table
            $stmt->close();
            $stmt = $conn->prepare("SELECT id, password FROM business_owners WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($userId, $hashedPassword);
                $stmt->fetch();
                $userType = 'business_owner';
            }
        }
        $stmt->close();

        if ($userId && password_verify($currentPassword, $hashedPassword)) {
            $newHashed = password_hash($newPassword, PASSWORD_DEFAULT);

            if ($userType === 'business_owner') {
                $upd = $conn->prepare("UPDATE business_owners SET password = ? WHERE id = ?");
            } else {
                $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            }
            $upd->bind_param("si", $newHashed, $userId);

            if ($upd->execute()) {
                $success = "Password updated successfully! You can now log in.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $upd->close();
        } else {
            $error = "Incorrect email or current password.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Change Password</h2>
        <p class="auth-desc">Update your account password using your email and current password.</p>

        <?php if ($error): ?>
            <div class="auth-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px;">
                <?php echo htmlspecialchars($success); ?>
                <br><br>
                <a href="login.php" style="color: #166534; font-weight: bold; text-decoration: underline;">Go to Login</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-input" required placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-input" required placeholder="Enter current password">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-input" required placeholder="At least 6 characters" minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-input" required placeholder="Re-enter new password">
                </div>
                <button type="submit" class="auth-btn">Update Password</button>
            </form>
        <?php endif; ?>

        <p class="auth-footer">
            <a href="login.php" class="auth-link">Back to Login</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
