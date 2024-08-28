<?php
include 'conexion.php';

// Obtener el ID de la categoría desde la URL
$categoria_id = $_GET['categoria_id'];

// Consulta para obtener los productos de la categoría seleccionada
$sql = "SELECT nombre, precio FROM productos WHERE categoria_id = $categoria_id";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="productos-container">';
    while ($producto = $result->fetch_assoc()) {
        echo '<div class="producto">';
        echo '<h2>' . $producto["nombre"] . '</h2>';
       // echo '<p>' . $producto["descripcion"] . '</p>';
        echo '<p>Precio: $' . $producto["precio"] . '</p>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>No hay productos disponibles en esta categoría.</p>';
}

$conexion->close();
?>