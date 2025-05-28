<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "acopio"; 

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
