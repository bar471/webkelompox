<?php
include 'koneksi.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection parameters
$host = 'localhost'; 
$user = 'root';      
$password = '';      
$dbname = 'coffe'; 

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Determine HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id_pembelian'])) {
            $id_pembelian = $_GET['id_pembelian'];
            $sql = "SELECT * FROM pembelian WHERE id_pembelian = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id_pembelian);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($data = $result->fetch_assoc()) {
                echo json_encode($data);
            } else {
                echo json_encode(['error' => 'Data not found']);
            }
        } else {
            $sql = "SELECT * FROM pembelian";
            $result = $conn->query($sql);
            $pembelian = [];
            while ($row = $result->fetch_assoc()) {
                $pembelian[] = $row;
            }
            echo json_encode($pembelian);
        }
        break;

        case 'POST':
            // Mendapatkan data JSON dari body request
            $data = json_decode(file_get_contents("php://input"), true);
        
            // Memastikan bahwa data yang dibutuhkan ada
            if (isset($data['username'], $data['password'], $data['email'])) {
                $username = $data['username'];
                $password = $data['password'];  // Tidak ada hashing, password langsung digunakan
                $email = $data['email'];
        
                // Validasi email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['error' => 'Invalid email format']);
                    break;
                }
        
                // Validasi password (misalnya: minimal 8 karakter)
                if (strlen($password) < 8) {
                    echo json_encode(['error' => 'Password must be at least 8 characters long']);
                    break;
                }
        
                // Menetapkan waktu pembuatan akun saat ini
                $created_at = date('Y-m-d H:i:s');
        
                // Query untuk memasukkan data pengguna baru
                $sql = "INSERT INTO users (username, password, email, created_at) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
        
                if ($stmt === false) {
                    // Jika terjadi error saat mempersiapkan query
                    echo json_encode(['error' => 'Failed to prepare SQL statement']);
                    break;
                }
        
                // Binding parameter
                $stmt->bind_param('ssss', $username, $password, $email, $created_at);
        
                // Mengeksekusi query
                if ($stmt->execute()) {
                    echo json_encode(['success' => 'User successfully added']);
                } else {
                    // Jika terjadi error saat mengeksekusi query
                    echo json_encode(['error' => 'Failed to add user: ' . $stmt->error]);
                }
        
                // Menutup statement setelah selesai
                $stmt->close();
            } else {
                // Mengirimkan respons jika data tidak lengkap
                echo json_encode(['error' => 'Incomplete data']);
            }
            break;
        
        default:
            // Menangani request selain POST
            echo json_encode(['error' => 'Invalid request method']);
            break;
        

    

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id_pembelian'], $data['user_id'], $data['amount'])) {
            $id_pembelian = $data['id_pembelian'];
            $user_id = $data['user_id'];
            $amount = $data['amount'];
            $pembelian_date = date('Y-m-d H:i:s'); // Set current date and time

            $sql = "UPDATE pembelian SET user_id = ?, pembelian_date = ?, amount = ? WHERE id_pembelian = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('issi', $user_id, $pembelian_date, $amount, $id_pembelian);
            if ($stmt->execute()) {
                echo json_encode(['success' => 'Data successfully updated']);
            } else {
                echo json_encode(['error' => 'Failed to update data']);
            }
        } else {
            echo json_encode(['error' => 'Incomplete data']);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id_pembelian'])) {
            $id_pembelian = $_GET['id_pembelian'];
            $sql = "DELETE FROM pembelian WHERE id_pembelian = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id_pembelian);
            if ($stmt->execute()) {
                echo json_encode(['success' => 'Data successfully deleted']);
            } else {
                echo json_encode(['error' => 'Failed to delete data']);
            }
        } else {
            echo json_encode(['error' => 'ID not provided']);
        }
        break;
}

// Close connection
$conn->close();
?>
