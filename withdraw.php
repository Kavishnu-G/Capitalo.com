<?php
session_start();
include('db.php');  // Include database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user data
$sql = "SELECT total_investments FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$total_investments = $user['total_investments'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $withdraw_amount = $_POST['withdraw_amount'];
    $deduction = $withdraw_amount * 0.02; // Calculate 2% deduction
    $final_amount = $withdraw_amount - $deduction;

    if ($final_amount > 0 && $total_investments >= $withdraw_amount) {
        // Update total investments
        $new_total = $total_investments - $withdraw_amount;
        $update_sql = "UPDATE users SET total_investments = ? WHERE username = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ds", $new_total, $username);
        $update_stmt->execute();

        $success_message = "₹" . number_format($final_amount, 2) . " has been successfully withdrawn after a 2% deduction!";
    } else {
        $error_message = "Insufficient balance or invalid amount!";
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
    <title>Withdraw - GoldFin</title>
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
                <li><a href="withdraw.php" class="active"><i class="fas fa-money-check-alt"></i><span>Withdraw</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i><span>Settings</span></a></li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Withdraw</h1>
            <div class="profile-dropdown">
                <i class="fas fa-user-circle profile-icon"></i>
                <div class="dropdown-content">
                    <p><?php echo $_SESSION['username']; ?></p>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>

        <div class="container">
            <div class="withdraw-section">
                <h3>Withdraw Funds</h3>
                <p>Total Investments: ₹<?php echo number_format($total_investments, 2); ?></p>

                <?php if (isset($success_message)) { ?>
                    <p class="success"><?php echo $success_message; ?></p>
                <?php } ?>
                <?php if (isset($error_message)) { ?>
                    <p class="error"><?php echo $error_message; ?></p>
                <?php } ?>

                <form method="POST" action="">
                    <label for="withdraw_amount">Enter Amount to Withdraw:</label>
                    <input type="number" name="withdraw_amount" id="withdraw_amount" step="0.01" min="0" required>
                    <p>Note: A 2% deduction will apply to the withdrawal amount.</p>
                    <button type="submit">Confirm Withdraw</button>
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
