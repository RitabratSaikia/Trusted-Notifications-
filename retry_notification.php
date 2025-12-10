<?php
require 'config.php';
require_admin();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header("Location: dashboard_admin.php");
    exit();
}

// Fetch notification
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE id = ?");
$stmt->execute([$id]);
$notif = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$notif) {
    header("Location: dashboard_admin.php");
    exit();
}

// Simple retry logic: flip channel SMS<->Email, Email<->App, App<->SMS
$oldChannel = $notif['channel'];
$newChannel = $oldChannel === 'SMS' ? 'Email' : ($oldChannel === 'Email' ? 'App' : 'SMS');

// Count attempts
$cntStmt = $pdo->prepare("SELECT COUNT(*) FROM delivery_logs WHERE notification_id = ?");
$cntStmt->execute([$id]);
$attempt_no = intval($cntStmt->fetchColumn()) + 1;

// Insert log
$logStmt = $pdo->prepare("INSERT INTO delivery_logs (notification_id, attempt_no, channel, status, detail)
                          VALUES (?,?,?,?,?)");
$logStmt->execute([$id, $attempt_no, $newChannel, 'sent', 'Retry send via ' . $newChannel]);

// Update main notification channel and status
$upd = $pdo->prepare("UPDATE notifications SET channel = ?, status = 'sent' WHERE id = ?");
$upd->execute([$newChannel, $id]);

$_SESSION['flash'] = "Retry attempt #$attempt_no sent via $newChannel (simulated).";
header("Location: dashboard_admin.php");
exit();
