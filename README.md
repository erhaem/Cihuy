# Cihuy

Cihuy is a simple URL shortener app built with PHP vanilla and MySQL. It allows you manage your own shortened links, and track the number of clicks each link receives.

## Features

- **User Authentication**: Secure registration and login system for users.
- **URL Shortening**: Create custom, easy-to-remember short links (slugs).
- **Dashboard**: A central place to view, edit, and delete your shortened URLs.
- **Click Tracking**: Monitors and displays the number of "hits" for each short URL.
- **Data Export**: Export your URL list to Excel or PDF directly from the dashboard.
- **Responsive UI**: Built with the Bulma CSS framework for a clean experience on any device.
- **Session Management**: Automatically logs out users after one hour of inactivity.

## Setup and Installation

Follow these steps to get the app running on your local server.

### Prerequisites

- A web server environment like XAMPP, WAMP, or MAMP or whatever.
- PHP
- MySQL or MariaDB

### 1. Clone the Repository

Clone this repository to your web server's public directory (e.g., `htdocs` in XAMPP).

```bash
git clone https://github.com/erhaem/Cihuy.git
```

### 2. Database Setup

1.  Create a new database in your database management tool (like phpMyAdmin). You can name it `dbcihuy`.
2.  Import the `dbcihuy.sql` file into the newly created database. This will set up the necessary `users` and `urls` tables.

### 3. Configure Database Connection

Open the `pdo.php` file and update the database credentials to match your local environment.

```php
// pdo.php

function getPDO() {
	$username = "your db username"; // e.g., "root"
	$password = "your db password"; // e.g., ""
	$dbname = "dbcihuy";

	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	];

	$pdo = new PDO("mysql:host=localhost;dbname={$dbname};charset=utf8mb4", $username, $password, $options);
	return $pdo;
}
```

### 4. Web Server Configuration

`.htaccess` file is included to handle the redirection from the short URL to the destination.

The rewrite rule is simple:

```apache
RewriteEngine On
RewriteRule ^([a-zA-Z0-9]+)/?/?$ 	redirect.php?url=$1
```

This rule captures an alphanumeric slug from the URL and passes it as a parameter to `redirect.php`.

### 5. Run the app

Navigate to the project directory in your web browser (e.g., `http://localhost/Cihuy/`). You will be redirected to the login page. You can create a new account to start using the aappp.

## File Structure

- `register.php`: Handles new user registration.
- `login.php`: Handles user authentication.
- `logout.php`: Destroys the user session and logs them out.
- `dashboard.php`: The main user dashboard, listing all created URLs.
- `add_new_url.php`: Form and logic for creating a new short URL.
- `edit_url.php`: Form and logic for updating an existing URL's destination or slug.
- `delete_url.php`: Logic for deleting a short URL.
- `redirect.php`: Looks up the slug in the database, increments the hit counter, and redirects the user to the destination URL.
- `.htaccess`: Apache configuration for clean URL rewriting.
- `dbcihuy.sql`: The database schema and table structure.
- `pdo.php`: Handles the PDO database connection.
- `helper.php`: Contains the `showInfo()` function for JavaScript alerts.
- `auto_logout.php`: Script to check for session expiration.
