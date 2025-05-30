<?php
require_once 'auth_check.php'; // Ensures admin is logged in
// require_once '../config.php'; // May be needed later for saving settings

$pageTitle = "Application Settings - Admin Panel";

// Handle form submission for settings if any (to be implemented)
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    // Process and save settings
//    $_SESSION['settings_success'] = 'Settings updated successfully!';
//    header('Location: settings.php');
//    exit;
// }

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
        </ul>
    </nav>

    <div class="container">
        <div class="content">
            <h2>Application Settings</h2>
            
            <?php if (isset($_SESSION['settings_success'])): ?>
                <p class="success-message"><?php echo htmlspecialchars($_SESSION['settings_success']); unset($_SESSION['settings_success']); ?></p>
            <?php endif; ?>

            <p>This section will allow administrators to configure application-wide settings.</p>
            <p><em>(Settings form and functionality to be implemented here.)</em></p>
            
            <!-- Example of a settings form (to be developed) -->
            <!--
            <form action="settings.php" method="post" class="settings-form">
                <div class="form-group">
                    <label for="site_name">Site Name:</label>
                    <input type="text" id="site_name" name="site_name" value="<?php // echo htmlspecialchars(getCurrentSetting('site_name', 'Fitness Tracker')); ?>">
                </div>
                <div class="form-group">
                    <label for="maintenance_mode">Maintenance Mode:</label>
                    <select id="maintenance_mode" name="maintenance_mode">
                        <option value="0" <?php // echo (getCurrentSetting('maintenance_mode', '0') == '0') ? 'selected' : ''; ?>>Off</option>
                        <option value="1" <?php // echo (getCurrentSetting('maintenance_mode', '0') == '1') ? 'selected' : ''; ?>>On</option>
                    </select>
                </div>
                <button type="submit">Save Settings</button>
            </form>
            -->
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Fitness Tracker Admin Panel</p>
    </footer>
</body>
</html>
