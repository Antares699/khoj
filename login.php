<?php
// login.php
require_once 'includes/config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashed_password);
            $stmt->fetch();
            $user_type = 'user';
        } else {
            $stmt->close();
            $stmt = $conn->prepare("SELECT id, first_name, password FROM business_owners WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $username, $hashed_password);
                $stmt->fetch();
                $user_type = 'business_owner';
            } else {
                $error = "No account found with that email.";
            }
        }
        $stmt->close();

        if (empty($error) && isset($hashed_password)) {
            if (password_verify($password, $hashed_password)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['user_type'] = $user_type;

                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Welcome Back</h2>
        <p class="auth-desc">Log in to manage your account and reviews.</p>

        <?php if ($error): ?>
            <div class="auth-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <button type="submit" class="auth-btn">Log In</button>
        </form>

        <div class="auth-footer">
            <?php if (isset($_GET['role']) && $_GET['role'] === 'business'): ?>
                Don't have an account? <a href="claim.php">Claim your business</a> or <a href="business-signup.php">Create
                    business account</a>.
            <?php else: ?>
                <p class="auth-footer">
                    Don't have an account? <a href="register.php" class="auth-link">Sign up</a><br><br>
                    Update your credentials? <a href="change-password.php" class="auth-link">Change Password</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
