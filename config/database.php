<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bookbytes;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$conn = mysqli_connect("localhost", "root", "", "bookbytes") or die("Connection failed: " . mysqli_connect_error());
mysqli_set_charset($conn, "utf8");
?>
