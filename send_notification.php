<?php
require 'config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = intval($_POST['customer_id'] ?? 0);
    $event_type = trim($_POST['event_type'] ?? '');
    $channel = $_POST['channel'] ?? 'SMS';
    $message = trim($_POST['message'] ?? '');
    $include_otp = isset($_POST['include_otp']);

    if (!$customer_id || !$event_type || !$channel || !$message) {
        $_SESSION['flash'] = "Please fill all required fields.";
        header("Location: dashboard_admin.php");
        exit();
    }

    $otp = null;
    if ($include_otp) {
        $otp = str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
        $message = str_replace('{{OTP}}', $otp, $message);
    }

    $token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("INSERT INTO notifications (event_type, customer_id, channel, message, otp_code, anti_spoof_token, status)
                           VALUES (?,?,?,?,?,?, 'sent')");
    $stmt->execute([$event_type, $customer_id, $channel, $message, $otp, $token]);
    $notification_id = $pdo->lastInsertId();

    $logStmt = $pdo->prepare("INSERT INTO delivery_logs (notification_id, attempt_no, channel, status, detail)
                              VALUES (?,?,?,?,?)");
    $logStmt->execute([$notification_id, 1, $channel, 'sent', 'Initial send from admin dashboard']);

    $_SESSION['flash'] = "Notification sent. Demo OTP: $otp | Token: $token";
    header("Location: dashboard_admin.php");
    exit();
}
header("Location: dashboard_admin.php");
