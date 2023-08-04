<?php 

include("datosConexion.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
}catch (PDOException $error) {
    die("No se puede conectar a la base de datos $dbname :" . $error->getMessage());
}



?>