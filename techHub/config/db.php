<?php
// --- Database Configuration ---
$dbHost = 'localhost';   // Your database host (e.g., 'localhost' or IP address)
$dbUser = 'root';        // Your database username
$dbPass = '';            // Your database password
$dbName = 'tech_hub';    // Your database name

// --- Create Connection ---
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// --- Check Connection ---
if ($conn->connect_error) {
    // If connection fails, stop the script and display an error.
    die("Connection failed: " . $conn->connect_error);
}
