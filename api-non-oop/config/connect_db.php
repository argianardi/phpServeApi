<?php 
    define('HOST', 'localhost');
    define('USER', 'root');
    define('DB', 'learn_restful_api');
    define('PASS', '');

    $conn = new mysqli(HOST, USER, PASS, DB) or die('Could not connect to MySQL: ' . mysqli_connect_error());

?>