# Kiwi Kloset – Costume Rental Management System
A web application built using PHP, MySQL, HTML, CSS, and JavaScript to help Kiwi Kloset staff manage costume rentals, branches, and bookings.

## Features
- View and search all costumes
- Record rentals and returns
- Add new costumes with validation and security
- View rental statistics (top costumes, revenue, etc.)

## File structure should look like this:
   kiwi-kloset/
   - index.php
   - rentals.php
   - add.php
   - stats.php
   - db.php
     
   - assets folder
      └── styles.css
   - README.txt

## Setup Instructions
1. Import `kiwi_kloset.sql` into your MySQL database using phpMyAdmin.
2. Place this folder in your Apache server directory (`/var/www/html/kiwi-kloset`).
3. Update `db.php` with your local credentials.
4. Run the app at `http://localhost/kiwi-kloset`.

## Technologies Used
PHP • MySQL • HTML • CSS • JavaScript • Apache • Ubuntu LAMP Stack

## Security
- SQL Injection prevention with prepared statements
- XSS prevention using `htmlspecialchars()`

TROUBLESHOOTING
--------------------
| Problem | Fix |
|----------|-----|
| 500 Error | Check PHP syntax or missing semicolon. |
| Database connection error | Verify db.php credentials. |
| “Table doesn’t exist” | Re-import kiwi_kloset.sql. |
| Blank page | Enable error display: sudo nano /etc/php/*/apache2/php.ini → set display_errors = On, restart Apache. |


## Developer
**Taj Dhillon**
