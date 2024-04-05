<?php

class Auth {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        $dsn = "mysql:host=$host;dbname=$dbname";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function register($username, $password) {
        // Check if username already exists
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            return false; // Username already exists
        }

        // Insert new user into database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);
        return true;
    }

    public function login($username, $password) {
        // Retrieve user data from database
        $stmt = $this->pdo->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Set cookie or session
            $_SESSION['user_id'] = $user['id'];
            return true; // Login successful
        }

        return false; // Login failed
    }

    public function logout() {
        // Unset session variables
        session_unset();

        // Destroy the session
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}
?>
