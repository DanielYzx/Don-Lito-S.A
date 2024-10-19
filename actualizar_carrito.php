<?php
session_start();
include 'conexion.php';

$producto_id = $_POST['producto_id'];
$cantidad = $_POST['cantidad'];

// Verifica la cantidad disponible
$sql = "SELECT cantidad_disponible FROM productos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $producto_id);
$stmt->execute();
$stmt->bind_result($cantidad_disponible);
$stmt->fetch();
$stmt->close();

if ($cantidad > $cantidad_disponible) {
    $cantidad = $cantidad_disponible;
    $response = array('error' => true, 'message' => "Lo sentimos, solo quedan $cantidad_disponible unidades en stock.", 'cantidad_disponible' => $cantidad_disponible);
} else {
    $response = array('error' => false);
}

$_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad; // Actualiza la cantidad en la sesión

// Genera el HTML del carrito
ob_start();
include 'conexion.php';

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    $total = 0;
    echo '<div class="carrito-container">';
    echo '<h1>Tu Carrito de Compras</h1>';
    echo '<table>';
    echo '<tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th>Acciones</th>
          </tr>';

    foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
        $sql = "SELECT nombre, cantidad_disponible FROM productos WHERE id = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('i', $producto_id);
            $stmt->execute();
            $stmt->bind_result($nombre, $cantidad_disponible);
            $stmt->fetch();
            $stmt->close();
        }

        if ($detalle['cantidad'] > $cantidad_disponible) {
            echo '<tr>';
            echo '<td colspan="5">Lo sentimos, la cantidad solicitada de ' . htmlspecialchars($nombre) . ' excede la cantidad disponible. Solo quedan ' . $cantidad_disponible . ' unidades.</td>';
            echo '</tr>';
            $detalle['cantidad'] = $cantidad_disponible;
        }

        $subtotal = $detalle['cantidad'] * $detalle['precio'];
        $total += $subtotal;

        echo '<tr>';
        echo '<td>' . htmlspecialchars($nombre) . '</td>';
        echo '<td>
                <div class="cantidad-container">
                    <input type="number" value="' . $detalle['cantidad'] . '" min="1" class="cantidad-input" data-producto-id="' . $producto_id . '" onchange="mostrarBotonActualizar(' . $producto_id . ')">
                    <button class="btn-cancelar" data-producto-id="' . $producto_id . '" style="display:none;" onclick="cancelarCambio(' . $producto_id . ', ' . $detalle['cantidad'] .')">
                        <img src="img/cancelarcarrito.png" alt="Cancelar" style="width: 15px; height: 15px;">
                    </button>
                    <button class="btn-actualizar" data-producto-id="' . $producto_id . '" style="display:none;" onclick="actualizarCantidad(' . $producto_id . ')">
                        <img src="img/actualizarcarrito.png" alt="Actualizar" style="width: 15px; height: 15px;">
                    </button>
                </div>';
        echo '<td>$' . number_format($detalle['precio'], 2) . '</td>';
        echo '<td>$' . number_format($subtotal, 2) . '</td>';
        echo '<td>
                <button class="btn-eliminar" onclick="eliminarProducto(' . $producto_id . ')">Eliminar</button>
              </td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<h2>Total: $' . number_format($total, 2) . '</h2>';
    echo '<button onclick="irAComprar()">Finalizar Compra</button>';
    echo '</div>';
} else {
    echo '<p>No hay productos en el carrito.</p>';
}

$response['html'] = ob_get_clean();
echo json_encode($response);
?>

