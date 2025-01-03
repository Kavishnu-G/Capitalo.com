<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php'; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['amount']) && isset($_POST['terms'])) {
    $amount = $_POST['amount'];
    $username = $_SESSION['username'];

    // Check for a valid amount
    if ($amount <= 0) {
        echo "<script>alert('Please enter a valid amount.'); window.location.href='add_fund.php';</script>";
        exit();
    }

    // Step 1: Retrieve the current balance from the database
    $query = "SELECT total_investments, recent_transactions FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_balance = $row['total_investments'];
        $recent_transactions = $row['recent_transactions'];

        // Step 2: Add the funds to the current balance
        $new_balance = $current_balance + $amount;

        // Step 3: Update the balance and recent transactions in the database
        $new_transaction = "Amount Credited: ₹" . $amount . " on " . date("Y-m-d H:i:s") . "\n";
        $updated_transactions = $recent_transactions . $new_transaction;

        $update_query = "UPDATE users SET total_investments = ?, recent_transactions = ? WHERE username = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("dss", $new_balance, $updated_transactions, $username);

        if ($update_stmt->execute()) {
            echo "<script>alert('Funds added successfully. New Balance: ₹$new_balance'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error updating funds.'); window.location.href='add_fund.php';</script>";
        }

        $update_stmt->close();
    } else {
        echo "<script>alert('User not found.'); window.location.href='add_fund.php';</script>";
    }

    $stmt->close();
    $conn->close();
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
    <title>Capitalo_FUNDS</title>
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
        <li><a href="#"><i class="fas fa-cog"></i><span>Settings</span></a></li>
    </ul>
</nav>

    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Add Funds</h1>
           
            <div class="profile-dropdown">
                <i class="fas fa-user-circle profile-icon"></i>
                <div class="dropdown-content">
                    <p><?php echo $_SESSION['username']; ?></p>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
      
        </header>
        <div class="container">
            <div class="add-fund-section">
                <form method="POST">
                    <div class="form-group">
                        <label for="amount">Amount in INR:</label>
                        <input type="number" id="amount" name="amount" placeholder="Enter amount" required>
                    </div>
                    <div class="predefined-amounts">
                        <button type="button" class="amount-btn" onclick="setAmount(10000)">₹10,000</button>
                        <button type="button" class="amount-btn" onclick="setAmount(20000)">₹20,000</button>
                        <button type="button" class="amount-btn" onclick="setAmount(50000)">₹50,000</button>
                        <button type="button" class="amount-btn" onclick="setAmount(100000)">₹1,00,000</button>
                    </div>
                    <div class="form-group terms">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="#">Terms and Conditions</a></label>
                    </div>
                    <button type="submit" class="confirm-btn"><i class="fas fa-check-circle"></i> Confirm</button>
                </form>
            </div>
        </div>
        <footer>
            <p>&copy; 2024 Gold Loan Investment App. All rights reserved.</p>
        </footer>
    </div>

    <script>
        function setAmount(value) {
            document.getElementById('amount').value = value;
        }
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
