<?php
include "db_connect.php"; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and escape form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match.";
        exit;
    }

    // Hash the password for security
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Directly construct the SQL query
    $sql = "INSERT INTO users (username, email, password_hash, created_at) VALUES ('$username', '$email', '$passwordHash', NOW())";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        // If there's an error, output it
        echo "Something went wrong. Error: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    echo " ";
}
?>
