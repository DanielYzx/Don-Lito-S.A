<?php
include('conexion.php'); // Incluir el archivo de conexión a la base de datos

if (isset($_POST['busqueda'])) {
    $busqueda = $conexion->real_escape_string($_POST['busqueda']); // Sanitizar la entrada

    // Consulta a la base de datos
    $query = "SELECT * FROM productos WHERE nombre LIKE '%$busqueda%' OR descripción LIKE '%$busqueda%'";
    $resultado = $conexion->query($query);

    if ($resultado->num_rows > 0) {
        echo "<h2>Resultados de búsqueda:</h2>";
        while ($fila = $resultado->fetch_assoc()) {
            echo "<div>";
            echo "<h3>" . $fila['nombre'] . "</h3>";
            echo "<p>" . $fila['descripción'] . "</p>";
            echo "<p>Precio: $" . $fila['precio'] . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No se encontraron resultados para '$busqueda'.</p>";
    }
} else {
    echo "<p>No se ha realizado ninguna búsqueda.</p>";
}

$conexion->close(); // Cerrar la conexión
?>
