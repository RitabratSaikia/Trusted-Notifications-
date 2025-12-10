<?php
require 'config.php';
require_login();
if (is_admin()) {
    header("Location: dashboard_admin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE customer_id = ? ORDER BY created_at DESC LIMIT 20");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = $_SESSION['flash'] ?? "";
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Alerts - Trusted Notifications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
    <div class="top-bar">
        <div>
            <h1>My Trusted Alerts</h1>
            <p class="text-muted">See all alerts issued in your name and verify suspicious messages.</p>
        </div>
        <div>
            <span class="tag">Logged in as <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert success"><?php echo htmlspecialchars($flash); ?></div>
    <?php endif; ?>

    <div class="card" style="margin-bottom:20px;">
        <h2>Verify an OTP / Alert</h2>
        <form method="get" action="verify_alert.php">
            <div class="form-grid">
                <div>
                    <label>Enter OTP or token you received</label>
                    <input type="text" name="code" placeholder="6-digit OTP or token" required>
                </div>
            </div>
            <button type="submit">Check if it is trusted</button>
            <p class="text-muted">Use this when you get an SMS / email that looks suspicious.</p>
        </form>
    </div>

    <div class="card">
        <h2>Recent Notifications Sent To You</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Event</th>
                        <th>Channel</th>
                        <th>Message</th>
                        <th>OTP</th>
                        <th>Status</th>
                        <th>Mark</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$notifications): ?>
                    <tr><td colspan="7">No notifications yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($notifications as $n): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($n['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($n['event_type']); ?></td>
                            <td><?php echo htmlspecialchars($n['channel']); ?></td>
                            <td><?php echo htmlspecialchars($n['message']); ?></td>
                            <td><?php echo htmlspecialchars($n['otp_code']); ?></td>
                            <td>
                                <span class="status-pill status-<?php echo htmlspecialchars($n['status']); ?>">
                                    <?php echo htmlspecialchars($n['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a class="btn small" href="update_status.php?id=<?php echo $n['id']; ?>&action=delivered">Received</a>
                                <a class="btn small secondary" href="update_status.php?id=<?php echo $n['id']; ?>&action=phishing_reported">Looks like phishing</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
