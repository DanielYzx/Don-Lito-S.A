<?php
session_start();
include 'conexion.php';

echo '<h1>Tu carrito</h1>';

if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0) {
    echo '<ul>';
    foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
        // Obtener los detalles del producto desde la base de datos
        $sql = "SELECT nombre, precio, descripción FROM productos WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($producto = $result->fetch_assoc()) {
            echo '<li>';
            echo $producto['nombre'] . $descripcion['descripción'] . ' - Cantidad: ' . $cantidad . ' - Precio total: $' . ($producto['precio'] * $cantidad);
            echo '</li>';
        }
    }
    echo '</ul>';
} else {
    echo '<p>Tu carrito está vacío.</p>';
}
?>
