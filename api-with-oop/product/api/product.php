<?php
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods:POST,GET,PUT,DELETE");
    header("Access-Control-Max-Age:3600");
    header("Access-Control-Allow-Headers:Content-Type,Access-Control-Allow-Headers,Authorization,X-Requested-With");

    // Get database connection
    include_once '../config/Database.php';
    // Instantiate product model
    include_once '../model/Product.php';

    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Create product object
    $product = new Product($db);

    // Get Request Method
    $request = $_SERVER['REQUEST_METHOD'];

    // Check Request Method
    switch($request) {

        // GET Request
        case "GET":
        if(!isset($_GET['id'])) {
            $stmt = $product->read();
            $num = $stmt->rowCount();

            // Check if more than 0 record found
            if($num>0) {
                // Products array
                $productArr = array();
                $productArr["products"] = array();

                // Retrieve our table contents
                // fetch() is faster than fetchAll()
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // extract row
                    // this will make $row['name'] to 
                    // just $name only
                    extract($row);

                    $productItem = array(
                        "id" => $id,
                        "productName" => $productName,
                        "price" => $price,
                        "productType" => $productType,
                        "stock" => $stock
                    );
                    array_push($productArr["products"], $productItem);
                }

                // Set response code-200 OK
                http_response_code(200);
                echo json_encode($productArr);
            } else {
                // No products found 
                // Set response code-404 Not found
                http_response_code(404);

                // Tell the user no products found
                echo json_encode(array("message" => "No products found."));
            }
        } elseif ($_GET['id'] == NULL) {
            echo json_encode(array("message" => "No id found."));
        } else {
            // Set ID property of record to read
            $product->id = $_GET['id'];

            // Read the details of product to be edited
            $product->readOne();

            if($product->id != null) {
                // Create array
                $productItem = array(
                    "id" => $product->id,
                    "productName" => $product->productName,
                    "price" => $product->price,
                    "productType" => $product->productType,
                    "stock" => $product->stock
                );

                // Set response code-200 OK
                http_response_code(200);

                // Make it json format
                echo json_encode($productItem);
            }else {
                // Set response code-404 Not found
                http_response_code(404);

                // Tell the user product not found
                echo json_encode(array("message" => "Product not found.")); 
            }
        }
        break;

        // POST Request
        case 'POST':
         
            // Jika body menggunakan raw JSON
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                $json_data = file_get_contents('php://input');
                $data = json_decode($json_data, true);
            } 
            // Jika body menggunakan x-www-form-urlencoded atau form-data
            else {
                $data = $_POST;
            }
        
            // Memeriksa apakah data yang diperlukan tersedia dalam array $data
            if (isset($data['productName']) && isset($data['price']) && isset($data['productType']) && isset($data['stock'])) {
                // Menetapkan nilai-nilai produk dari data yang diterima
                $product->productName = $data['productName'];
                $product->price = $data['price'];
                $product->productType = $data['productType'];
                $product->stock = $data['stock'];
        
                // Membuat produk baru
                if ($product->createProduct()) {
                    // Set response code-201 Created
                    http_response_code(201);
                    echo json_encode(array("codeStatus" => "201", "message" => "Product created."));
                } else {
                    // Set response code-503 Service Unavailable
                    http_response_code(503);
                    echo json_encode(array("codeStatus" => "503", "message" => "Unable to create product."));
                }
            } else {
                // Set response code-400 Bad Request
                http_response_code(400);
                echo json_encode(array("codeStatus" => "400", "message" => "Incomplete data."));
            }
        
            break;

            // PUT Request
            case 'PUT':
            // Ambil nilai ID dari query string
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                $json_data = file_get_contents('php://input');
                $data = json_decode($json_data, true);
            } elseif ($_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded') {
            // Jika body menggunakan x-www-form-urlencoded
            parse_str(file_get_contents('php://input'), $data);
            }else {
                // Jika body menggunakan x-www-form-urlencoded atau form-data
                // Ambil nilai body request
                $body = file_get_contents("php://input");

                // Pisahkan bagian-bagian form-data berdasarkan delimiter
                $parts = explode("---------------------------", $body);

                // Inisialisasi array untuk menyimpan nilai
                $data = array();

                // Iterasi setiap bagian form-data
                foreach ($parts as $part) {
                    // Pilih hanya bagian yang berisi data
                    if (strpos($part, "Content-Disposition: form-data") !== false) {
                        // Pisahkan nama field dan nilainya
                        preg_match('/name="(.*?)"/', $part, $matches);
                        $fieldName = $matches[1];
                        preg_match('/\r\n\r\n(.*?)\r\n/', $part, $matches);
                        $fieldValue = $matches[1];

                        // Simpan ke dalam array
                        $data[$fieldName] = $fieldValue;
                    }
                }

                // Akses nilai-nilai tersebut
                $productName = isset($data['productName']) ? $data['productName'] : null;
                $price = isset($data['price']) ? $data['price'] : null;
                $productType = isset($data['productType']) ? $data['productType'] : null;
                $stock = isset($data['stock']) ? $data['stock'] : null;
            }

            if(empty($id)) {
                http_response_code(400); // Bad Request
                echo json_encode(array("message" => "Id cannot be empty."));    
            } else {
                // Assign data ke properti objek produk
                $product->id = $id;
                $product->productName = isset($data['productName']) ? $data['productName'] : null;
                $product->price = isset($data['price']) ? $data['price'] : null;
                $product->productType = isset($data['productType']) ? $data['productType'] : null;
                $product->stock = isset($data['stock']) ? $data['stock'] : null;

                // Periksa apakah semua data produk yang diperlukan telah disediakan
                if($product->productName !== null && $product->price !== null && $product->productType !== null && $product->stock !== null) {

                    // Periksa apakah produk dengan ID yang diberikan ada
                    if($product->updateProduct()) {
                        // Set response code-200 OK
                        http_response_code(200);

                        // Tell the user
                        echo json_encode(array("message" => "Product was updated."));
                    } else {
                        // Set response code-503 Service Unavailable
                        http_response_code(503);

                        $result = array(
                            "statusCode" => "503", 
                            "message" => "Bad request, unable to update product"
                        );

                        echo json_encode($result);
                        echo json_encode(array("message" => "Unable to update product."));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("message" => "All product data must be provided."));
                }
            }
            break;

            // DELETE Request
            case 'DELETE':
            // Ambil nilai ID dari query string
            $id = $_GET["id"] ?? '';
            if(!isset($id) || $id == NULL) {
                echo json_encode(array("codeStatus" => "400", "message" => "Id cannot be empty."));
            }else {
                // Set product id
                $product->id = $id;

                if($product->deleteProduct()) {
                    http_response_code(200); 
                    echo json_encode(array("message" => "Product was deleted"));

                } else {
                    http_response_code(503);
                    $result = array(
                        "statusCode" => 503,
                        "statusMessage" => "Bad request, unable to delete product"
                    );
                    echo json_encode($result);
                    echo json_encode(array("message" => "Unable to delete product"));
                }
            }
            break;
            
            default : 
            http_response_code(405); // Method Not Allowed
    }
?>