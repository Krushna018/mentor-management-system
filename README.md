<div align="center">

# Mentor Management System

**A focused dashboard for organizing mentors, departments, capacity, and profile photos.**

[![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Frontend](https://img.shields.io/badge/Frontend-Vanilla%20JS-F7DF1E?logo=javascript&logoColor=111111)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License](https://img.shields.io/badge/Use-Personal%20%26%20Educational-2E7D32)](#license)

</div>

## Overview

Mentor Management System is a lightweight CRUD application built with HTML, CSS, JavaScript, PHP, and MySQL. It provides a simple workflow for maintaining mentor records and reviewing department capacity from a single browser-based dashboard.

> Built for quick local deployment with XAMPP and a straightforward MySQL data model.

<div align="center">

[Overview](#overview) | [Highlights](#highlights) | [Quick Start](#quick-start) | [API](#api-reference)

</div>

## Highlights

| Manage | Organize | Maintain |
| --- | --- | --- |
| Add, edit, and delete mentors | Filter records by department | Upload JPG and PNG photos |
| Store employee IDs and capacity | View mentors in a responsive table | Remove replaced profile photos |

## Stack

`HTML5` `CSS3` `Vanilla JavaScript` `PHP / MySQLi` `MySQL` `Apache / XAMPP`

## How It Works

| 01 | 02 | 03 | 04 |
| --- | --- | --- | --- |
| **Add** | **Review** | **Update** | **Manage** |
| Create a mentor record with a photo | Filter mentors by department | Edit details or replace a photo | Delete records and old photos |

## Quick Start

### 1. Requirements

- [XAMPP](https://www.apachefriends.org/) with Apache, PHP, and MySQL
- A modern web browser

> **Local setup:** This project expects Apache to serve the repository and MySQL to provide the `mentor_management` database.

### 2. Clone the project

Run these commands from the XAMPP web root:

```powershell
cd C:\xampp\htdocs
git clone https://github.com/Krushna018/mentor-management-system.git
```

### 3. Start the services

Open the XAMPP Control Panel and start **Apache** and **MySQL**.

### 4. Create the database

Run the following SQL in phpMyAdmin or the MySQL console:

```sql
CREATE DATABASE mentor_management;
USE mentor_management;

CREATE TABLE mentors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    employee_id VARCHAR(100) NOT NULL,
    department VARCHAR(150) NOT NULL,
    max_mentees INT UNSIGNED NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5. Configure and open

The default local database settings are already configured in [`css/php/db.php`](css/php/db.php):

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "mentor_management";
```

Open the dashboard at [http://localhost/mentor-management-system/](http://localhost/mentor-management-system/).

## Project Layout

```text
mentor-management-system/
|-- index.html             # Main application page
|-- css/
|   |-- style.css          # Application styles
|   `-- php/               # PHP API and database connection
|-- js/
|   `-- app.js             # Form handling and dashboard logic
`-- uploads/               # Mentor profile photos
```

## API Reference

| Endpoint | Method | Description |
| --- | --- | --- |
| `css/php/get_mentors.php` | `GET` | Return all mentors |
| `css/php/add_mentor.php` | `POST` | Create a mentor and upload a photo |
| `css/php/update_mentor.php` | `POST` | Update mentor details and an optional photo |
| `css/php/delete_mentor.php` | `POST` | Delete a mentor and their photo |

## Important Notes

- Keep the `uploads/` directory writable by Apache.
- Only JPG and PNG profile photos are accepted.
- Update the database credentials before deploying outside a local XAMPP environment.
- Add authentication and authorization before using the application in production.

> **Production reminder:** The included configuration is intended for local development. Protect the API endpoints and move database credentials into secure environment configuration before deployment.

## License

This project is available for personal and educational use.
