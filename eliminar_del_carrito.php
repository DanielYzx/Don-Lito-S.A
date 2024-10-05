<?php
session_start();
include 'conexion.php'; // Asegúrate de que esto incluya la conexión a tu base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $productoId = $data['productoId'];

    // Aquí debes agregar la lógica para eliminar el producto del carrito en la base de datos

    // Ejemplo de respuesta
    echo json_encode(['success' => true]);
}
?>
