<?php 
    require_once('../config/connect_db.php');

    $productArry = array();
    if(isset($_GET['id'])) {
        $id = $_GET['id'];

        if($product = mysqli_query($conn, "SELECT * FROM product WHERE id=$id")) {
            while ($row = $product->fetch_array(MYSQLI_ASSOC)) {
                $productArry[] = $row;
            }
            mysqli_close($conn);
            echo json_encode($productArry);
        }
    }
?>