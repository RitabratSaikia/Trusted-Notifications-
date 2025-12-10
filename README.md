Trusted Notifications Web Application
==========================================

Stack: PHP, MySQL, HTML, CSS

1. Import database
------------------
- Create a MySQL database named `trusted_notifications`.
- Open phpMyAdmin or MySQL CLI and run the SQL script:

    database.sql

This will:
- create tables (users, notifications, delivery_logs)

It will NOT insert demo users. That is done by init_demo_users.php.

2. Configure database connection
--------------------------------
Edit `config.php` and update:

    $host, $dbname, $username, $password

to match your local MySQL settings (for XAMPP usually username=root, password="").

3. Create demo users (IMPORTANT STEP)
------------------------------------
- In your browser, after importing the DB, open:

    http://localhost/trusted_notifications_app/init_demo_users.php

- This script will create or update:
    admin@example.com / password  (role: admin)
    alice@example.com / password  (customer)
    bob@example.com   / password  (customer)

It uses PHP's password_hash() directly on your server, so login will always work.

4. Run the project
------------------
- Copy the whole folder `trusted_notifications_app` into your web root:
    For XAMPP: C:\xampp\htdocs\trusted_notifications_app
- Start Apache and MySQL.
- In your browser open:
    http://localhost/trusted_notifications_app/

5. How the solution matches the problem statement
-------------------------------------------------
- Reliable delivery:
    * Admin chooses the primary channel (SMS / Email / In-app).
    * Every attempt is logged in `delivery_logs` with attempt number and channel.
    * Admin can click "Retry via other channel" to simulate automatic fallback logic.

- Safe & anti-spoof:
    * Each notification gets a random anti_spoof_token plus optional 6-digit OTP.
    * Customer portal shows all alerts issued in their name.
    * Customer can enter any OTP/token they receive and check if it exists in the system.
      If not found, the app warns that it is suspicious.
    * Customer can mark an alert as "Looks like phishing", which changes status to
      `phishing_reported`.

- Timely & clear:
    * Admin dashboard shows recent notifications and their status.
    * Customer dashboard shows their recent alerts, channels, and statuses.

This is a basic end-to-end prototype that you can extend with real SMS/Email APIs,
more granular event-to-channel rules, and analytics dashboards.
