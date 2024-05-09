<?php
    class Product {
        // Database connection and table name
        private $conn;
        private $tableName = "product";

        // Object properties
        public $id;
        public $productName;
        public $price;
        public $productType;
        public $stock;

        // Constructor with $db as database connection
        public function __construct($db) {
            $this->conn = $db;
        }

        // Get All Products
        function read() {
            $query = "SELECT  id, productName, price, productType, stock FROM " . $this->tableName . " ORDER BY id ASC";

            // Prepare query statement
            $stmt = $this->conn->prepare($query);

            // execute query
            $stmt->execute();
            return $stmt;
        }

        // Get product by id
        function readOne() {
            // Query to read single product
            $query = "SELECT id, productName, price, productType, stock FROM " . $this->tableName . " WHERE id = ?";

            // Prepare query statement
            $stmt = $this->conn->prepare($query);

            // // Bind id of product to be updated
            $stmt->bindParam(1, $this->id);

            // Execute query
            $stmt->execute();

            // Get retrieved row
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set values to object properties
            $this->productName = $row['productName'];
            $this->price = $row['price'];
            $this->productType = $row['productType'];
            $this->stock = $row['stock'];
        }

        // Create product
        function createProduct() {
            // Query to insert record
            $query = "INSERT INTO " . $this->tableName . " SET 
                productName=:productName,
                price=:price,
                productType=:productType,
                stock=:stock";
            
            // Prepare query
            $stmt = $this->conn->prepare($query);

            // bind values
            $stmt->bindParam(":productName", $this->productName);
            $stmt->bindParam(":price", $this->price);
            $stmt->bindParam(":productType", $this->productType);
            $stmt->bindParam(":stock", $this->stock);

            // Execute query
            if($stmt->execute()){
                return true;
            }

            return false;
        }

        // Update product
        function updateProduct(){
            // Query to Update
            $query = "UPDATE " . $this->tableName . " SET 
            productName = :productName,
            price = :price,
            productType = :productType,
            stock = :stock
            WHERE id = :id";

            // Prepare query statement
            $stmt = $this->conn->prepare($query);

            // Bind values
            $stmt->bindParam(":productName", $this->productName);
            $stmt->bindParam(":price", $this->price);
            $stmt->bindParam(":productType", $this->productType);
            $stmt->bindParam(":stock", $this->stock);
            $stmt->bindParam(":id", $this->id);

            // Execute the query
            if($stmt->execute()){
                return true;
            }

            return false;
        }

        // Delete product
        function deleteProduct(){
            $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";

            // prepare query
            $stmt = $this->conn->prepare($query);

            // Bind id of product to be deleted
            $stmt->bindParam(1, $this->id);

            // Execute query
            if($stmt->execute()){
                return true;
            }
            return false;
        }

    }

?>