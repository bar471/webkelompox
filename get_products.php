<?php
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "coffe"; 

header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json');


// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mengambil data dari tabel products
$sql = "SELECT product_id, product_name, category_id, price, stock FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Membuat array untuk menyimpan hasil data
    $products = [];
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    // Mengubah data menjadi format JSON
    echo json_encode($products);
} else {
    // Jika tidak ada data ditemukan
    echo json_encode(['error' => 'No data found']);
}

$conn->close();
?>
