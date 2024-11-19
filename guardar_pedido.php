<?php
session_start();
include 'conexion.php';

header('Content-Type: application/json');  // Asegúrate de especificar que la respuesta será JSON

// Verifica si el carrito tiene productos
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    // Recupera el ID del usuario (esto depende de tu sistema de autenticación)
    $usuario_id = $_SESSION['user_id'];  // Asegúrate de tener el usuario logueado
    $total = 0;

    // Calcula el total
    foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
        $total += $detalle['cantidad'] * $detalle['precio'];
    }

    // Inserta el pedido en la base de datos
    $sql = "INSERT INTO pedidos (usuario_id, fecha, total) VALUES (?, NOW(), ?)";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param('id', $usuario_id, $total);
        $stmt->execute();
        $pedido_id = $stmt->insert_id;  // Obtén el ID del pedido recién insertado
        $stmt->close();

        // Inserta los detalles del pedido y actualiza la cantidad de productos
        foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
            // Inserta el detalle del pedido
            $sql_detalle = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
            if ($stmt_detalle = $conexion->prepare($sql_detalle)) {
                $stmt_detalle->bind_param('iiid', $pedido_id, $producto_id, $detalle['cantidad'], $detalle['precio']);
                $stmt_detalle->execute();
                $stmt_detalle->close();
            }

            // Actualiza la cantidad disponible del producto
            $sql_update = "UPDATE productos SET cantidad_disponible = cantidad_disponible - ? WHERE id = ?";
            if ($stmt_update = $conexion->prepare($sql_update)) {
                $stmt_update->bind_param('ii', $detalle['cantidad'], $producto_id);
                $stmt_update->execute();
                $stmt_update->close();
            }
        }

        // Limpia el carrito después de guardar el pedido
        unset($_SESSION['carrito']);

        // Responde con éxito
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar el pedido']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'El carrito está vacío']);
}
?>
