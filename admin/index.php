<?php
require_once 'auth_check.php'; // Ensures admin is logged in

// session_start() is already called in auth_check.php if not already started
// For now, let's assume an admin session variable must be set.
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     // Redirect to a login page or show an error
//     // header('Location: login.php'); // Example
//     // exit;
//     echo "<p style='color: red; text-align: center;'><strong>Security Warning:</strong> Admin area is not secured. Implement proper authentication.</p>";
// }

// Include configuration or database connection if needed for admin tasks
// require_once '../config.php'; // Adjust path as necessary
// require_once '../src/db.php'; // Adjust path as necessary

$pageTitle = "Admin Panel - Fitness Tracker";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    </header>

    <nav>
        <ul>
            <li><a href="../index.php" target="_blank">View Main Site</a></li>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="activities.php">Manage Activities</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a></li>
            <!-- Add more admin navigation links here -->
            <!-- <li><a href="users.php">Manage Users</a></li> -->
            <!-- <li><a href="activities.php">Manage Activities</a></li> -->
            <!-- <li><a href="settings.php">Settings</a></li> -->
        </ul>
    </nav>

    <div class="container">
        

        <div class="content">
            <h2>Welcome to the Admin Dashboard</h2>
            <p>This is the central hub for managing your Fitness Tracker application. From here, you can oversee various aspects of the site.</p>

            <div class="feature-box">
                <h3>User Management (Placeholder)</h3>
                <p>Functionality to view, edit, and manage user accounts will be available here.</p>
                <!-- <p><a href="users.php">Go to User Management</a></p> -->
            </div>

            <div class="feature-box">
                <h3>Activity Data Overview (Placeholder)</h3>
                <p>Insights and management tools for user-submitted fitness activities.</p>
                <!-- <p><a href="activities.php">Go to Activity Management</a></p> -->
            </div>

            <div class="feature-box">
                <h3>Application Settings (Placeholder)</h3>
                <p>Configure site-wide settings and parameters.</p>
                <!-- <p><a href="settings.php">Go to Settings</a></p> -->
            </div>

            <p>More features will be added soon. Please check back for updates.</p>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Fitness Tracker Admin Panel</p>
    </footer>
</body>
</html>
