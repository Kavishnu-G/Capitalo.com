<?php
session_start();
include('db.php');  // Include database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user data
$sql = "SELECT total_investments, interest_earned, active_loans, recent_transactions, ad_details FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Set default values if no data is found (for initial setup)
$total_investments = $user['total_investments'] ?? 0;
$interest_earned = $user['interest_earned'] ?? 0;
$active_loans = $user['active_loans'] ?? 0;
$recent_transactions = $user['recent_transactions'] ?? '';
$ad_details = $user['ad_details'] ?? '';

// Merge `ad_details` into `recent_transactions`
$full_recent_transactions = $recent_transactions;
if (!empty($ad_details)) {
    $full_recent_transactions .= "\n" . $ad_details;
}

$credited_transactions = $recent_transactions; // Assuming these are credited details
$ads_transactions = $ad_details;

// Close the statement
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Capitalo_HOME</title>
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
            <h1>Dashboard</h1>
            <div class="profile-dropdown">
                <i class="fas fa-user-circle profile-icon"></i>
                <div class="dropdown-content">
                    <p><?php echo $_SESSION['username']; ?></p>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>

        <div class="container">
            <!-- Dashboard Summary -->
            <div class="dashboard-summary">
                <div class="stat">
                    <h3>Total Investments</h3>
                    <p>₹<?php echo number_format($total_investments); ?></p>
                    <small>+₹0.00 this month</small>
                </div>
                <div class="stat">
                    <h3>Interest Earned</h3>
                    <p>₹<?php echo number_format($interest_earned); ?></p>
                    <small>+₹0.00 this month</small>
                </div>
                <div class="stat">
                    <h3>Active Loans</h3>
                    <p><?php echo $active_loans; ?></p>
                    <small>+0 this month</small>
                </div>
            </div>

            <!-- Investment Activity -->
            <div class="activity-section">
                <div class="investment-activity">
                    <h3>Investment Activity</h3>
                    <canvas id="investmentChart"></canvas>
                </div>
                <div class="recent-activities">
    <div class="credited-menu">
        <h3>Credited Transactions</h3>
        <ul>
            <?php
            $credited_items = explode("\n", $credited_transactions);
            foreach ($credited_items as $item) {
                if (!empty(trim($item))) {
                    echo "<div class='slide_item'><span>$item</span></div>";
                }
            }
            ?>
        </ul>
    </div>

    <div class="ads-menu">
        <h3>Ads Information</h3>
        <div class="sliding-container">
            <?php
            $ads_items = explode("\n", $ads_transactions);
            foreach ($ads_items as $ad) {
                if (!empty(trim($ad))) {
                    echo "<div class='slide-item'><span>$ad</span></div>";
                }
            }
            ?>
        </div>
    </div>
</div>

            </div>
        </div>

        <footer>
            <p>&copy; 2024 CAPITALO.COM. All rights reserved.</p>
        </footer>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('investmentChart').getContext('2d');
        const investmentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Investments',
                        data: [0, 0, 0, <?php echo $total_investments; ?>],
                        borderColor: 'gold',
                        borderWidth: 3,
                        tension: 0.2
                    },
                    {
                        label: 'Interest',
                        data: [0, 0, 0, <?php echo $interest_earned; ?>],
                        borderColor: 'green',
                        borderWidth: 3,
                        tension: 0.2
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    </script>

    <!-- JavaScript for Sidebar Toggle -->
    <script>

        const sidebar = document.querySelector('.sidebar');
        const toggleArrow = document.querySelector('#toggle-arrow');
        const mainContent = document.querySelector('.main-content');

        toggleArrow.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            toggleArrow.classList.toggle('fa-arrow-right');
            toggleArrow.classList.toggle('fa-arrow-left');
        });

        
    </script>
</body>
</html>
