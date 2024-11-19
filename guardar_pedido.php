<?php
session_start();
include 'conexion.php';


$_SESSION['pagina_anterior'] = $_SERVER['REQUEST_URI']; // Almacena la URL actual
// Verificamos si la variable 'user_email' está disponible
if (isset($_SESSION['user_email'])) {
    echo "Correo registrado: " . $_SESSION['user_email'];
} else {
    echo "No hay correo registrado en la sesión.";
}

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'C:\xampp\htdocs\Don-Lito-S.A\PHPMailer\src\Exception.php';
require 'C:\xampp\htdocs\Don-Lito-S.A\PHPMailer\src\PHPMailer.php';
require 'C:\xampp\htdocs\Don-Lito-S.A\PHPMailer\src\SMTP.php';

// Incluir TCPDF
require_once('tcpdf/tcpdf.php');

// Crear la carpeta 'pdfs' si no existe
if (!file_exists('pdfs')) {
    mkdir('pdfs', 0777, true);
}

if (isset($_POST['guardar_pedido']) && isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    // Recopilar los datos del pedido
    $usuario_id = $_SESSION['user_id'];
    $user_email = $_SESSION['user_email'];  // Asignar el correo electrónico del usuario a la sesión

    $total = 0;
    $productos_pedido = [];

    foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
        // Consulta para obtener el nombre y precio del producto desde la base de datos
        $sql = "SELECT nombre, precio FROM productos WHERE id = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('i', $producto_id);
            $stmt->execute();
            $stmt->bind_result($nombre, $precio);
            $stmt->fetch();
            $stmt->close();
        }

        // Asegurarse de que se hayan obtenido el nombre y el precio
        if (isset($nombre) && isset($precio)) {
            $cantidad = $detalle['cantidad'];
            $subtotal = $cantidad * $precio;
            $total += $subtotal;
            $productos_pedido[] = [
                'id' => $producto_id,
                'nombre' => $nombre,
                'cantidad' => $cantidad,
                'precio' => $precio,
                'subtotal' => $subtotal
            ];
        } else {
            // Manejar el caso en que no se pudo obtener el producto de la base de datos
            echo 'Error: Producto con ID ' . htmlspecialchars($producto_id) . ' no se encuentra en la base de datos.';
            continue; // Saltar a la siguiente iteración del bucle
        }
    }

    // Calcular el total sin IVA y el IVA
    $total_sin_iva = $total / 1.13;
    $iva = $total_sin_iva * 0.13;

    // Insertar el pedido en la base de datos
    $sql = "INSERT INTO pedidos (usuario_id, fecha, total) VALUES (?, NOW(), ?)";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param('id', $usuario_id, $total);
        $stmt->execute();
        $pedido_id = $stmt->insert_id;
        $stmt->close();
    }

    // Insertar los detalles del pedido
    foreach ($productos_pedido as $producto) {
        $sql = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('iiid', $pedido_id, $producto['id'], $producto['cantidad'], $producto['precio']);
            $stmt->execute();
            $stmt->close();
        }

        // Actualiza la cantidad disponible del producto
        $sql_update = "UPDATE productos SET cantidad_disponible = cantidad_disponible - ? WHERE id = ?";
        if ($stmt_update = $conexion->prepare($sql_update)) {
            $stmt_update->bind_param('ii', $detalle['cantidad'], $producto_id);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // Generar el PDF del pedido
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $html = '<h1>Detalles del Pedido</h1>';
    $html .= '<p>Pedido ID: ' . $pedido_id . '</p>';
    $html .= '<p>Usuario ID: ' . $usuario_id . '</p>';
    $html .= '<table border="1" cellpadding="5">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>';
    foreach ($productos_pedido as $producto) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($producto['nombre']) . '</td>
                    <td>' . htmlspecialchars($producto['cantidad']) . '</td>
                    <td>$' . number_format($producto['precio'], 2) . '</td>
                    <td>$' . number_format($producto['subtotal'], 2) . '</td>
                  </tr>';
    }
    $html .= '<tr>
                <td colspan="3" align="right">Subtotal sin IVA</td>
                <td>$' . number_format($total_sin_iva, 2) . '</td>
              </tr>';
    $html .= '<tr>
                <td colspan="3" align="right">IVA (13%)</td>
                <td>$' . number_format($iva, 2) . '</td>
              </tr>';
    $html .= '<tr>
                <td colspan="3" align="right"><b>Total</b></td>
                <td><b>$' . number_format($total, 2) . '</b></td>
              </tr>';
    $html .= '</table>';

    $pdf->writeHTML($html);
    $pdf_output = realpath('.') . DIRECTORY_SEPARATOR . 'pdfs' . DIRECTORY_SEPARATOR . 'pedido_' . $pedido_id . '.pdf';
    $pdf->Output($pdf_output, 'F');

    // Verificar si el correo electrónico está disponible
    if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
        // Enviar el PDF por correo electrónico
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor de correo
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'localhost'; // Mailhog corre en localhost
            $mail->Port = 1025; // Puerto por defecto de Mailhog

            // Información del remitente
            $mail->setFrom('no-reply@mitienda.com', 'Mi Tienda');
            $mail->addAddress($_SESSION['user_email']); // Correo del usuario

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Detalles de tu Pedido';
            $mail->Body    = 'Adjunto encontrarás los detalles de tu pedido. Gracias por comprar con nosotros.';

            // Adjuntar el PDF
            $mail->addAttachment($pdf_output);

            $mail->send();
            echo 'El pedido ha sido guardado y el correo ha sido enviado.';
        
        } catch (Exception $e) {
            echo 'El pedido ha sido guardado pero no se pudo enviar el correo. Error: ', $mail->ErrorInfo;
        }
    } else {
        echo 'No se pudo enviar el correo porque no hay un correo registrado en la sesión.';
    }

    // Vaciar el carrito de compras
    unset($_SESSION['carrito']);
}
?>

