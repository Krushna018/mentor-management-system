# Mentor Management System

A lightweight mentor management application built with HTML, CSS, JavaScript, PHP, and MySQL. It allows administrators to add, view, filter, edit, and delete mentor records, including profile photos.

## Features

- Add mentors with name, employee ID, department, maximum mentee capacity, and profile photo
- View all mentors in a responsive table
- Filter mentors by department
- Edit mentor details and replace profile photos
- Delete mentors and their associated profile photos
- JPG and PNG image validation
- JSON-based PHP endpoints for frontend requests

## Technology Stack

- HTML5
- CSS3
- Vanilla JavaScript
- PHP with MySQLi
- MySQL
- Apache through XAMPP

## Requirements

- [XAMPP](https://www.apachefriends.org/) with Apache, PHP, and MySQL
- A modern web browser

## Installation

1. Clone this repository into the XAMPP web root:

	```powershell
	cd C:\xampp\htdocs
	git clone https://github.com/Krushna018/mentor-management-system.git
	```

2. Start **Apache** and **MySQL** from the XAMPP Control Panel.

3. Create the database and table in phpMyAdmin or the MySQL console:

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

4. Confirm the database settings in [`css/php/db.php`](css/php/db.php). The default local XAMPP configuration is:

	```php
	$host = "localhost";
	$username = "root";
	$password = "";
	$database = "mentor_management";
	```

5. Open the application in your browser:

	[http://localhost/mentor-management-system/](http://localhost/mentor-management-system/)

## Project Structure

```text
mentor-management-system/
|-- index.html             # Main application page
|-- css/
|   |-- style.css          # Application styles
|   `-- php/               # PHP API endpoints and database connection
|-- js/
|   `-- app.js             # Form handling and mentor management logic
`-- uploads/               # Stored mentor profile photos
```

## API Endpoints

| Endpoint | Method | Purpose |
| --- | --- | --- |
| `css/php/get_mentors.php` | GET | Fetch all mentors |
| `css/php/add_mentor.php` | POST | Create a mentor and upload a profile photo |
| `css/php/update_mentor.php` | POST | Update mentor details and optionally replace the photo |
| `css/php/delete_mentor.php` | POST | Delete a mentor and their profile photo |

## Notes

- The `uploads/` directory must be writable by the web server.
- The application accepts JPG and PNG profile photos.
- For production use, update the database credentials and add authentication before deployment.

## License

This project is available for personal and educational use.
