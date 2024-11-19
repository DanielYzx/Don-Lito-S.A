<?php
session_start();
include 'conexion.php';
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
    $usuario_id = $_SESSION['user_id'];
    $user_email = $_SESSION['user_email']; 

    $total = 0;
    $productos_pedido = [];

    foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
        $sql = "SELECT nombre, precio FROM productos WHERE id = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('i', $producto_id);
            $stmt->execute();
            $stmt->bind_result($nombre, $precio);
            $stmt->fetch();
            $stmt->close();
        }
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
            echo 'Error: Producto con ID ' . htmlspecialchars($producto_id) . ' no se encuentra en la base de datos.';
            continue;
        }
    }

    $total_sin_iva = $total / 1.13;
    $iva = $total_sin_iva * 0.13;

    $sql = "INSERT INTO pedidos (usuario_id, fecha, total) VALUES (?, NOW(), ?)";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param('id', $usuario_id, $total);
        $stmt->execute();
        $pedido_id = $stmt->insert_id;
        $stmt->close();
    }

    foreach ($productos_pedido as $producto) {
        $sql = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('iiid', $pedido_id, $producto['id'], $producto['cantidad'], $producto['precio']);
            $stmt->execute();
            $stmt->close();
        }

        $sql_update = "UPDATE productos SET cantidad_disponible = cantidad_disponible - ? WHERE id = ?";
        if ($stmt_update = $conexion->prepare($sql_update)) {
            $stmt_update->bind_param('ii', $detalle['cantidad'], $producto_id);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // Generar el PDF
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
    $pdf_output = __DIR__ . '/pdfs/pedido_' . $pedido_id . '.pdf'; // Ruta absoluta del PDF
    $pdf->Output($pdf_output, 'F');

    // Verificar si el archivo se creó correctamente
    if (file_exists($pdf_output)) {
        // Enviar el correo
        

        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->Port = 1025;

            $mail->setFrom('no-reply@mitienda.com', 'Mi Tienda');
            $mail->addAddress($_SESSION['user_email']);

            $mail->isHTML(true);
            $mail->Subject = 'Detalles de tu Pedido';
            $mail->Body = 'Adjunto encontrarás los detalles de tu pedido. Gracias por comprar con nosotros.';
            $mail->addAttachment($pdf_output); // Adjuntar el PDF

            $mail->send();
            echo 'El pedido ha sido guardado y el correo con el PDF ha sido enviado.';
        } catch (Exception $e) {
            echo 'El pedido se guardó, pero no se pudo enviar el correo. Error: ' . $mail->ErrorInfo;
        }
    } else {
        echo 'El pedido se guardó, pero no se pudo generar el archivo PDF.';
    }

    unset($_SESSION['carrito']);
}
?>

