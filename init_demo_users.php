<?php
// Run this ONCE in browser after setting up database:
// http://localhost/trusted_notifications_app/init_demo_users.php
require 'config.php';

$adminEmail = 'admin@example.com';
$customer1Email = 'alice@example.com';
$customer2Email = 'bob@example.com';

$plainPassword = 'password';
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

function upsertUser($pdo, $name, $email, $phone, $role, $hash) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $id = $stmt->fetchColumn();

    if ($id) {
        $upd = $pdo->prepare("UPDATE users SET name = ?, phone = ?, role = ?, password = ? WHERE id = ?");
        $upd->execute([$name, $phone, $role, $hash, $id]);
    } else {
        $ins = $pdo->prepare("INSERT INTO users (name, email, phone, role, password) VALUES (?,?,?,?,?)");
        $ins->execute([$name, $email, $phone, $role, $hash]);
    }
}

upsertUser($pdo, 'Admin User', $adminEmail, '9999999999', 'admin', $hash);
upsertUser($pdo, 'Alice Customer', $customer1Email, '9000000001', 'customer', $hash);
upsertUser($pdo, 'Bob Customer', $customer2Email, '9000000002', 'customer', $hash);

echo "<h2>Demo users created/updated successfully.</h2>";
echo "<p>You can now login with:</p>";
echo "<pre>Admin: admin@example.com / password\nCustomer: alice@example.com / password</pre>";
echo '<p><a href=\"login.php\">Go to Login</a></p>';
?>
