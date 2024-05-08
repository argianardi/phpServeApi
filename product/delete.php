<?php
    require_once('../config/connect_db.php');
    
    // Ambil id dari parameter URL
    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {


    if($id != null) {
        $sql = $conn->prepare("DELETE FROM product WHERE id=?");
        $sql->bind_param('i', $id);
        $sql->execute();

        if($sql) {
            echo json_encode(array('RESPONSE' => 'SUCCESS'));
        }else {
            echo json_encode(array('RESPONSE' => 'FAILED'));
        }
    } else {
        "GAGAL";
    }
} else {
    echo "Method yang digunakan salah!";
}

?>