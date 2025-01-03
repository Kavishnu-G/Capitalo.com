<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['amount']) && isset($_POST['maturity_months'])) {
    $username = $_SESSION['username'];
    $amount = $_POST['amount'];
    $maturity_months = $_POST['maturity_months'];

    // Insert Ad details into user table
    $query = "UPDATE users SET ads_posted = ads_posted + 1, ad_status = 'active', ad_details = CONCAT(IFNULL(ad_details, ''), 'Ad Amount: ₹', ?, ', Maturity Months: ', ?, ' ', NOW(), '\\n') WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dis", $amount, $maturity_months, $username);
    $stmt->execute();
    $stmt->close();

    header("Location: ads.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Capitalo_Ads</title>
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
            <h1>Manage Ads</h1>
            <div class="profile-dropdown">
                <i class="fas fa-user-circle profile-icon"></i>
                <div class="dropdown-content">
                    <p><?php echo $_SESSION['username']; ?></p>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>
        <div class="container">
            <div class="post-ad-section">
                <form method="POST">
                    <div class="form-group">
                        <label for="amount">Ad Amount in INR:</label>
                        <input type="number" id="amount" name="amount" placeholder="Enter amount" required>
                    </div>
                    <div class="form-group">
                        <label for="maturity_months">Maturity Months:</label>
                        <input type="number" id="maturity_months" name="maturity_months" placeholder="Enter months" required>
                    </div>
                    <button type="submit" class="confirm-btn"><i class="fas fa-check-circle"></i> Post Ad</button>
                </form>
            </div>
            <div class="ads-list-section">
                <h2>Posted Ads</h2>
                <table class="ads-table">
                    <thead>
                        <tr>
                            <th>Ad Details</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT ad_details, ad_status FROM users WHERE username = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $_SESSION['username']);
                        $stmt->execute();
                        $stmt->bind_result($ad_details, $ad_status);
                        $stmt->fetch();
                        $stmt->close();

                        if (!empty($ad_details)) {
                            $ads = explode("\n", trim($ad_details));
                            foreach ($ads as $ad) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($ad) . "</td>
                                    <td>" . htmlspecialchars($ad_status) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>No ads posted yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <footer>
            <p>&copy; 2024 Gold Loan Investment App. All rights reserved.</p>
        </footer>
    </div>

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
