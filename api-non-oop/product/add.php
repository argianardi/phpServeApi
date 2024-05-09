<?php
    require_once('../config/connect_db.php');

    // Mengambil data dari permintaan POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Jika body menggunakan raw JSON
        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
        } 
        // Jika body menggunakan x-www-form-urlencoded atau form-data
        else {
            $data = $_POST;
        }

        // Memeriksa apakah data yang diperlukan tersedia
        if(isset($data['productName']) && isset($data['price']) && isset($data['productType']) && isset($data['stock'])) {
            $productName = $data['productName'];
            $price = $data['price'];
            $productType = $data['productType'];
            $stock = $data['stock'];
            
            // Menyiapkan dan menjalankan query SQL
            $sql = $conn -> prepare("INSERT INTO product (productName, price, productType, stock) VALUES (?, ?, ?, ?)");

            // Pada bind parameter ssddd itu artinya tipe data yang dikirimkan s = string, d = double, i = integer
            $sql->bind_param('sdsd', $productName, $price, $productType,  $stock);
            $sql->execute();
            
            // Memeriksa apakah query berhasil dijalankan
            if ($sql) {
                // Mengembalikan respons JSON jika berhasil
                echo json_encode(array('RESPONSE' => 'SUCCESS'));
            } else {
                // Mengembalikan pesan "Gagal" jika terjadi kesalahan
                echo "Gagal";
            }
        } else {
            // Mengembalikan pesan "Gagal" jika data yang diperlukan tidak tersedia
            echo "Gagal";
        }
    } else {
        // Mengembalikan pesan "Gagal" jika metode request bukan POST
        echo "Gagal";
    }
?>
