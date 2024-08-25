<?php
$host = "localhost"; // o la IP del servidor MySQL
$usuario = "root"; // tu usuario de MySQL
$password = ""; // tu contraseña de MySQL
$baseDeDatos = "don_lito";

// Crear conexión
$conexion = new mysqli($host, $usuario, $password, $baseDeDatos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa";
}
?>
