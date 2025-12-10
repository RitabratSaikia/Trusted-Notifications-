<?php
require 'config.php';
require_admin();

// Fetch customers
$stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'customer' ORDER BY name");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch latest notifications
$notifStmt = $pdo->query("SELECT n.*, u.name AS customer_name FROM notifications n
    JOIN users u ON n.customer_id = u.id
    ORDER BY n.created_at DESC LIMIT 20");
$notifications = $notifStmt->fetchAll(PDO::FETCH_ASSOC);

$flash = $_SESSION['flash'] ?? "";
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Trusted Notifications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
    <div class="top-bar">
        <div>
            <h1>Admin Dashboard</h1>
            <p class="text-muted">Configure event → channel, send OTP/alerts & track delivery.</p>
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
        <h2>Send Trusted Notification</h2>
        <form method="post" action="send_notification.php">
            <div class="form-grid">
                <div>
                    <label>Customer</label>
                    <select name="customer_id" required>
                        <option value="">Select customer</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?php echo $c['id']; ?>">
                                <?php echo htmlspecialchars($c['name'] . " (" . $c['email'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Event type</label>
                    <input type="text" name="event_type" placeholder="Login OTP / Debit alert" required>
                </div>
                <div>
                    <label>Primary channel</label>
                    <select name="channel" required>
                        <option value="SMS">SMS</option>
                        <option value="Email">Email</option>
                        <option value="App">In-app</option>
                    </select>
                </div>
            </div>
            <label>Message</label>
            <textarea name="message" rows="3" placeholder="Your one time password is {{OTP}} for ..." required></textarea>

            <label>
                <input type="checkbox" name="include_otp" value="1" checked>
                Generate 6-digit OTP
            </label>

            <div style="margin-top:14px;">
                <button type="submit">Send Notification</button>
            </div>
            <p class="text-muted" style="margin-top:10px;">
                In the demo, actual SMS/Email is not sent. OTP and anti-spoof token are visible on-screen so you can test.
            </p>
        </form>
    </div>

    <div class="card">
        <h2>Recent Notifications</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Customer</th>
                        <th>Event</th>
                        <th>Channel</th>
                        <th>OTP</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$notifications): ?>
                    <tr><td colspan="7">No notifications yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($notifications as $n): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($n['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($n['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($n['event_type']); ?></td>
                            <td><?php echo htmlspecialchars($n['channel']); ?></td>
                            <td><?php echo htmlspecialchars($n['otp_code']); ?></td>
                            <td>
                                <span class="status-pill status-<?php echo htmlspecialchars($n['status']); ?>">
                                    <?php echo htmlspecialchars($n['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a class="btn small secondary" href="retry_notification.php?id=<?php echo $n['id']; ?>">Retry via other channel</a>
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
