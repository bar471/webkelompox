<?php
$host = "localhost";  // Ganti dengan host database Anda
$username = "root";   // Ganti dengan username database Anda
$password = "";       // Ganti dengan password database Anda
$database = "coffe"; // Ganti dengan nama database Anda

$kon = mysqli_connect($host, $username, $password, $database);

if (mysqli_connect_errno()) {
    echo "Koneksi gagal: " . mysqli_connect_error();
    exit();
}
?>
