<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'campus_animal_adoption';

try {
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die('Database connection failed. Start MySQL in XAMPP and import database.sql using phpMyAdmin.');
}
?>
