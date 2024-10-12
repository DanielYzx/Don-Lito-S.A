<?php 
session_start();
include 'conexion.php';

// Verificar si se recibieron los datos correctos
if (isset($_POST['producto_id'], $_POST['action'])) {
    $producto_id = (int)$_POST['producto_id'];
    $action = $_POST['action'];

    if ($action === 'remove') {
        // Eliminar producto del carrito
        if (isset($_SESSION['carrito'][$producto_id])) {
            unset($_SESSION['carrito'][$producto_id]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Producto no encontrado en el carrito.']);
        }
    } else {
        // Solo si es agregar, verificar la cantidad
        if (isset($_POST['cantidad'])) {
            $cantidad = (int)$_POST['cantidad'];

            // Consultar el precio y la descripción del producto desde la base de datos
            $sql = "SELECT precio, descripción FROM productos WHERE id = ?";
            if ($stmt = $conexion->prepare($sql)) {
                // Vincular el parámetro (ID del producto)
                $stmt->bind_param('i', $producto_id);
                $stmt->execute();
                $result = $stmt->get_result();

                // Verificar si el producto existe
                if ($result->num_rows > 0) {
                    $producto = $result->fetch_assoc();
                    $precio = (float)$producto['precio'];
                    $descripcion = $producto['descripción'];

                    // Calcular el total basado en la cantidad ingresada
                    $total = $cantidad * $precio;

                    // Inicializar el carrito si no existe
                    if (!isset($_SESSION['carrito'])) {
                        $_SESSION['carrito'] = [];
                    }

                    // Agregar producto al carrito
                    $_SESSION['carrito'][$producto_id] = [
                        'cantidad' => $cantidad,
                        'precio' => $precio,
                        'descripcion' => $descripcion,
                        'total' => $total
                    ];

                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Producto no encontrado.']);
                }

                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'error' => 'Error en la consulta.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Cantidad no especificada.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos no válidos.']);
}

$conexion->close();
?>

