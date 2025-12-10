<?php
require 'config.php';
require_login();

$id = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id || !in_array($action, ['delivered','phishing_reported'])) {
    header("Location: dashboard_customer.php");
    exit();
}

// Ensure this notification belongs to current user (or admin override)
if (!is_admin()) {
    $check = $pdo->prepare("SELECT * FROM notifications WHERE id = ? AND customer_id = ?");
    $check->execute([$id, $_SESSION['user_id']]);
    if (!$check->fetch()) {
        header("Location: dashboard_customer.php");
        exit();
    }
}

$stmt = $pdo->prepare("UPDATE notifications SET status = ? WHERE id = ?");
$stmt->execute([$action, $id]);

if ($action === 'delivered') {
    $_SESSION['flash'] = "Thank you, we have marked this alert as delivered.";
} else {
    $_SESSION['flash'] = "We have flagged this alert as suspected phishing. Our team will investigate.";
}

if (is_admin()) {
    header("Location: dashboard_admin.php");
} else {
    header("Location: dashboard_customer.php");
}
exit();
