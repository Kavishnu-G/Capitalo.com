<?php
session_start();
include('db.php');  // Include database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user data
$sql = "SELECT username, password, email, phone, address FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle case when user data is not found
if (!$user) {
    die("User data not found!");
}

// Default values for form inputs
$password = $user['password'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';
$address = $user['address'] ?? '';

// Update user data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_address = $_POST['address'];
    $new_password = !empty($_POST['password']) ? $_POST['password'] : $password;

    $update_sql = "UPDATE users SET email = ?, phone = ?, address = ?, password = ? WHERE username = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssss", $new_email, $new_phone, $new_address, $new_password, $username);
    $update_stmt->execute();

    $success_message = "Profile updated successfully!";
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
    <title>Profile - GoldFin</title>
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
                <li><a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li><a href="add_fund.php"><i class="fas fa-wallet"></i><span>Add Funds</span></a></li>
                <li><a href="ads.php"><i class="fas fa-bullhorn"></i><span>Ads</span></a></li>
                <li><a href="withdraw.php"><i class="fas fa-money-check-alt"></i><span>Withdraw</span></a></li>
                <li><a href="profile.php" class="active"><i class="fas fa-user"></i><span>Profile</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i><span>Settings</span></a></li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Profile</h1>
            <div class="profile-dropdown">
                <i class="fas fa-user-circle profile-icon"></i>
                <div class="dropdown-content">
                    <p><?php echo $_SESSION['username']; ?></p>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>

        <div class="container">
            <div class="profile-section">
                <h3>Profile</h3>
                <div class="profile-icon">
                    <img src="assets/default-profile.png" alt="Profile Picture">
                    <i class="fas fa-pencil-alt edit-icon"></i>
                </div>

                <?php if (isset($success_message)) { ?>
                    <p class="success"><?php echo $success_message; ?></p>
                <?php } ?>
                <?php if (isset($error_message)) { ?>
                    <p class="error"><?php echo $error_message; ?></p>
                <?php } ?>

                <form method="POST" action="">
                    <label for="username">Username:</label>
                    <div class="editable-field">
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled >
                        <i class="fas fa-pencil-alt edit-btn"></i>
                    </div>

                    <label for="password">Password:</label>
                    <div class="editable-field">
                        <input type="password" name="password" id="password" value="<?php echo htmlspecialchars($user['password']); ?>" disabled >
                        <i class="fas fa-pencil-alt edit-btn"></i>
                    </div>

                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>">

                    <label for="phone">Phone Number:</label>
                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" >

                    <label for="address">Address:</label>
                    <textarea name="address" id="address" rows="3" ><?php echo htmlspecialchars($user['address']); ?></textarea>

                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 CAPITALO.COM. All rights reserved.</p>
        </footer>
    </div>

    <script>
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const inputField = btn.previousElementSibling;
                inputField.disabled = !inputField.disabled;
                if (!inputField.disabled) inputField.focus();
            });
        });

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
