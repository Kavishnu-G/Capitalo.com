<?php
// Start the session if not already started
session_start();

// If the session is active, destroy it
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goodbye</title>
    <script>
        // Alert a goodbye message
        alert("Your account has been deleted. You will be redirected to the login page.");

        // Redirect to login.php after 2 seconds
        setTimeout(function() {
            window.location.href = "login.php";
        }, 2000);
    </script>
</head>
<body>
    <h1>Goodbye</h1>
    <p>Your account has been successfully deleted. Redirecting to the login page...</p>
</body>
</html>
