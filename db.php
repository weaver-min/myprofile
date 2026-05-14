<?php
$host = "localhost";
$dbname = "mail_system";
$username = "root";
$password = "0123";
//use pdo function for modern style and less code....
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => false,  // use real prepared statements
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

