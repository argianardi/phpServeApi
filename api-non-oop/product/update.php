<?php 
    require_once('../config/connect_db.php');

    // Mengambil data dari permintaan PUT
    if($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // Parse data yang dikirim melalui PUT
        parse_str(file_get_contents("php://input"), $_PUT);
        
        // Ambil id dari parameter URL
        $id = $_GET['id'];

        // Memeriksa apakah data yang diperlukan tersedia
        if(isset($id) && isset($_PUT['productName']) && isset($_PUT['price']) && isset($_PUT['productType']) && isset($_PUT['stock'])) {
            // $id = $_PUT['id'];
            $productName = $_PUT['productName'];
            $price = $_PUT['price'];
            $productType = $_PUT['productType'];
            $stock = $_PUT['stock'];

            // Menyiapkan dan menjalankan query SQL
            $sql = $conn->prepare("UPDATE product SET productName=?, price=?, productType=?, stock=? WHERE id=?");

            // Pada bind parameter ssddd itu artinya tipe data yang dikirimkan s = string, d = double, i = integer
            $sql->bind_param('sdsdd', $productName, $price, $productType, $stock, $id);
            $sql->execute();

            if ($sql) {
                echo json_encode(array('RESPONSE' => 'SUCCESS'));
            } else {
                echo json_encode(array('RESPONSE' => 'FAILED'));
            }
        } else {
            echo json_encode(array('RESPONSE' => 'MISSING_DATA'));
        }
    } else {
        echo 'GAGAL';
    }
?>
