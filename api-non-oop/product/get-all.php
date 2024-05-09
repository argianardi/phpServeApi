<?php
    require_once('../config/connect_db.php');
    $productArray = array();
    if($products = mysqli_query($conn, "SELECT * FROM product ORDER BY id ASC")) {
        while($row = $products->fetch_array(MYSQLI_ASSOC)) {
            $productArray[] = $row;
        }
        mysqli_close($conn);
        echo json_encode($productArray);
    }


?>