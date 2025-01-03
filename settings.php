<?php
session_start();
include('db.php'); // Include database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user data
$sql = "SELECT email, notifications_enabled FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$email = $user['email'] ?? '';
$notifications_enabled = $user['notifications_enabled'] ?? 0;

// Handle updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_settings'])) {
        $new_email = $_POST['email'];
        $notifications = isset($_POST['notifications_enabled']) ? 1 : 0;

        $update_sql = "UPDATE users SET email = ?, notifications_enabled = ? WHERE username = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sis", $new_email, $notifications, $username);
        $update_stmt->execute();

        $success_message = "Settings updated successfully!";
    } elseif (isset($_POST['delete_account'])) {
        $delete_sql = "DELETE FROM users WHERE username = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("s", $username);
        $delete_stmt->execute();

        session_destroy();
        header("Location: goodbye.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Settings - GoldFin</title>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="toggle-btn">
            <i class="fas fa-arrow-left" id="toggle-arrow"></i>
        </div>
        <div class="logo-section">
            <h2>GoldFin</h2>
        </div>
        <nav>
            <ul>
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li><a href="add_fund.php"><i class="fas fa-wallet"></i><span>Add Funds</span></a></li>
                <li><a href="ads.php"><i class="fas fa-bullhorn"></i><span>Ads</span></a></li>
                <li><a href="withdraw.php"><i class="fas fa-money-check-alt"></i><span>Withdraw</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i><span>Settings</span></a></li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Settings</h1>
            <div class="profile-dropdown">
                <i class="fas fa-user-circle profile-icon"></i>
                <div class="dropdown-content">
                    <p><?php echo htmlspecialchars($username); ?></p>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>

        <div class="container">
            <div class="settings-section">
                <h3>Account Settings</h3>

                <?php if (isset($success_message)) { ?>
                    <p class="success"><?php echo $success_message; ?></p>
                <?php } ?>

                <form method="POST" action="">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>

                    <label for="notifications_enabled">
                        <input type="checkbox" name="notifications_enabled" id="notifications_enabled" <?php if ($notifications_enabled) echo 'checked'; ?>>
                        Enable Notifications
                    </label>

                    <button type="submit" name="update_settings">Save Changes</button>
                </form>

                <h3>Danger Zone</h3>
                <form method="POST" action="">
                    <button type="submit" name="delete_account" class="danger">Delete Account</button>
                </form>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 CAPITALO.COM. All rights reserved.</p>
        </footer>
    </div>

    <script>
        const toggleArrow = document.getElementById('toggle-arrow');
        const sidebar = document.querySelector('.sidebar');

        toggleArrow.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            toggleArrow.classList.toggle('fa-arrow-left');
            toggleArrow.classList.toggle('fa-arrow-right');
        });
    </script>
</body>
</html>
