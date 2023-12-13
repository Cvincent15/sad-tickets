<?php
session_start();
include 'database_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit();
}

// Validate and sanitize the login form data
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = trim($_POST['password']);

if (empty($username) || empty($password)) {
    // Handle empty input data
    echo "Invalid input data.";
    header('Refresh: 1; URL= ../index.php');
    exit();
}

// Prepare the query using placeholders for username
$stmt = $conn->prepare("SELECT user_ctmeu_id, username, password, first_name, last_name, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

// Get the result
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // Verify the hashed password
    if (password_verify($password, $user['password'])) {
        // Password is correct, set the session variable for username
        $_SESSION['username'] = $user['username'];

        // Now, you can use the username to fetch the user's primary key (user_ctmeu_id)
        $user_ctmeu_id = $user['user_ctmeu_id'];
        // ... you can use $user_ctmeu_id as needed

        // Set other session variables
        $_SESSION['user_ctmeu_id'] = $user_ctmeu_id;
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['role'] = $user['role'];

        if ($_SESSION['role'] == 'Enforcer') {
            header('Location: ../ctmeuusers.php');
        } else {
            header('Location: ../ctmeupage.php');
        }
        exit(); // Always exit after a header redirect
    } else {
        // Password is incorrect, display an error message
        echo "Invalid username or password";
        header('Refresh: 1; URL= ../index.php');
        exit();
    }
} else {
    // User not found, display an error message
    echo "User not found.";
    header('Refresh: 1; URL= ../index.php');
    exit();
}
?>
