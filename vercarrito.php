<?php
session_start();
include 'conexion.php'; // Asegúrate de incluir la conexión a la base de datos si la necesitas

// Verifica si hay productos en el carrito
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    // Contador para el total
    $total = 0;

    echo '<div class="carrito-container">';
    echo '<h1>Tu Carrito de Compras</h1>';
    echo '<table>';
    echo '<tr>
            <th>Producto</th>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th>Acciones</th>
          </tr>';

    // Recorrer los productos en el carrito
    foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
        // Consulta para obtener el nombre del producto desde la base de datos
        $sql = "SELECT nombre FROM productos WHERE id = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('i', $producto_id);
            $stmt->execute();
            $stmt->bind_result($nombre);
            $stmt->fetch();
            $stmt->close();
        }

        // Calcular el total del producto
        $subtotal = $detalle['cantidad'] * $detalle['precio'];
        $total += $subtotal;

        // Mostrar producto en la tabla
        echo '<tr>';
        echo '<td>' . htmlspecialchars($nombre) . '</td>';
        echo '<td>' . htmlspecialchars($detalle['descripcion']) . '</td>';
        echo '<td>
                <input type="number" value="' . $detalle['cantidad'] . '" min="1" class="cantidad-input" data-producto-id="' . $producto_id . '">
              </td>';
        echo '<td>$' . number_format($detalle['precio'], 2) . '</td>';
        echo '<td>$' . number_format($subtotal, 2) . '</td>';
        echo '<td>
                <button class="btn-eliminar" onclick="eliminarProducto(' . $producto_id . ')">Eliminar</button>
              </td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<h2>Total: $' . number_format($total, 2) . '</h2>';
    echo '<button onclick="irAComprar()">Finalizar Compra</button>'; // O un botón para continuar comprando

    echo '</div>'; // Cerrar contenedor de carrito

} else {
    echo '<p>No hay productos en el carrito.</p>';
}

?>


<script>
function eliminarProducto(productoId) {
    if (confirm("¿Estás seguro de que quieres eliminar este producto del carrito?")) {
        // Hacer una solicitud AJAX para eliminar el producto del carrito
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'agregar_al_carrito.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Recargar la página para mostrar los cambios
                    location.reload();
                } else {
                    alert('Error al eliminar el producto: ' + response.error);
                }
            }
        };
        xhr.send(`producto_id=${productoId}&action=remove`);
    }
}


function irAComprar() {
    // Aquí puedes redirigir a la página de pago o continuar con la compra
    window.location.href = 'pagina_pago.php'; // Cambia esto según la ruta de tu página de pago
}
</script>

<style>
.carrito-container {
    max-width: 800px;
    margin: auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f4f4f4;
}

button {
    padding: 5px 10px;
    margin: 5px;
    cursor: pointer;
}

.btn-eliminar {
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 5px;
}
</style>
