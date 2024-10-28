<?php
session_start();
include 'conexion.php'; // Asegúrate de incluir la conexión a la base de datos

// Verificar si se recibe la acción y el producto
if (isset($_POST['action']) && isset($_POST['producto_id'])) {
    $producto_id = intval($_POST['producto_id']);
    $accion = $_POST['action'];

    // Consulta para obtener el precio del producto
    $sql = "SELECT precio FROM productos WHERE id = ?";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param('i', $producto_id);
        $stmt->execute();
        $stmt->bind_result($precio);
        $stmt->fetch();
        $stmt->close();
    }

    if ($accion === 'add' && isset($_POST['cantidad'])) {
        $nueva_cantidad = intval($_POST['cantidad']);

        // Validar que la cantidad sea válida
        if ($nueva_cantidad > 0) {
            // Si el producto ya existe en el carrito, sumamos la cantidad
            if (isset($_SESSION['carrito'][$producto_id])) {
                $_SESSION['carrito'][$producto_id]['cantidad'] += $nueva_cantidad;
            } else {
                // Si no existe, lo añadimos al carrito
                $_SESSION['carrito'][$producto_id] = [
                    'cantidad' => $nueva_cantidad,
                    'precio' => $precio // Almacena el precio del producto
                ];
            }

            // Respuesta de éxito
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'La cantidad debe ser mayor que cero.']);
        }
    } elseif ($accion === 'update' && isset($_POST['cantidad'])) {
        $nueva_cantidad = intval($_POST['cantidad']);

        // Validar que la cantidad sea válida
        if ($nueva_cantidad > 0) {
            // Actualizar la cantidad en la sesión
            if (isset($_SESSION['carrito'][$producto_id])) {
                $_SESSION['carrito'][$producto_id]['cantidad'] = $nueva_cantidad;
                // Respuesta de éxito
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'El producto no se encuentra en el carrito.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'La cantidad debe ser mayor que cero.']);
        }
    } elseif ($accion === 'remove') {
        // Lógica para eliminar el producto del carrito
        unset($_SESSION['carrito'][$producto_id]);
        echo json_encode(['success' => true]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
}
?>





