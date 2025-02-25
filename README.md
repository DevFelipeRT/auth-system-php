# User Authentication System in PHP

This repository hosts a **user authentication and registration system** implemented in **PHP**. The project incorporates enhanced security measures and uses **PDO (PHP Data Objects)** for secure communication with the database, helping to prevent SQL injection and other common vulnerabilities.

## Features

- **User Registration**: Secure registration with hashed passwords.
- **User Login**: Credential validation and session management.
- **Password Security**: Utilizes modern hashing algorithms to protect passwords.
- **PDO Database Connection**: Ensures safe and efficient interaction with the database.
- **Session Management**: Manages user sessions securely after login.

## Installation

### Prerequisites

- PHP 7.4 or higher.
- A web server (e.g., Apache or Nginx).
- A MySQL or MariaDB database.

### Setup Steps

1.  **Clone the Repository:**
    - git clone https://github.com/your-username/php-user-auth.git
2. **Database Setup:**
    - Create a new database.
    - Import the provided SQL schema to create the necessary tables.
    - Update the database connection settings in the `config.php` file.
3. **Web Server Configuration:**
    - Point your web server (Apache, Nginx, etc.) to the project directory.
4. **Access the Application:**
    - Open your browser and navigate to the project URL to register and log in.

## Usage

Once installed, users can:

- **Register** with a username and password.
- **Log in** using their registered credentials to access secured areas.

All sensitive information is securely handled, following best practices for password storage and session management.

## License

This project is licensed under the **MIT License**. See the [LICENSE](https://chatgpt.com/c/LICENSE) file for details.

## Disclaimer

** Please be advised that this project is intended solely for educational purposes and is currently under development. As such, some features may be incomplete. It is not recommended for use in production environments until further enhancements are implemented.
