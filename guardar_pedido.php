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
// Configurar la zona horaria para El Salvador
date_default_timezone_set('America/El_Salvador'); // Ajusta la zona horaria a El Salvador

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

        // Obtener el nombre del usuario desde la base de datos
     $sql = "SELECT nombre, dirección FROM usuarios WHERE id = ?";
     if ($stmt = $conexion->prepare($sql)) {
         $stmt->bind_param('i', $usuario_id);
         $stmt->execute();
         $stmt->bind_result($nombre_usuario, $direccion_usuario);
         $stmt->fetch();
         $stmt->close();
     }
    }


   // Generar el PDF
   $pdf = new TCPDF();
   $pdf->AddPage();
   $pdf->SetFont('helvetica', '', 12);

// Agregar el logo como marca de agua para cubrir toda la página
// Ruta del logo
$logo_path = __DIR__ . '/img/logo.png'; 

// Verificar si el archivo del logo existe
if (file_exists($logo_path)) {
    // Guardar el estado gráfico actual
    $pdf->setAlpha(0.2); // Reducir opacidad para un efecto suave
    
    // Obtener las dimensiones de la página
    $page_width = $pdf->getPageWidth();
    $page_height = $pdf->getPageHeight();
    
    // Obtener las dimensiones de la imagen usando getimagesize()
    list($img_width, $img_height) = getimagesize($logo_path);
    
    // Ajustar el logo a todo el ancho, manteniendo la proporción
    $logo_width = $page_width;
    $logo_height = ($logo_width / $img_width) * $img_height;
    
    // Verificar si el logo es más alto que la página y ajustar si es necesario
    if ($logo_height > $page_height) {
        $logo_height = $page_height;
        $logo_width = ($logo_height / $img_height) * $img_width;
    }
    
    // Agregar el logo con las dimensiones ajustadas
    $pdf->Image($logo_path, 0, 0, $logo_width, $logo_height, '', '', '', false, 300);
    
    // Restaurar la opacidad a 1 para el resto del contenido
    $pdf->setAlpha(1);
} else {
    echo 'El archivo del logo no se encontró en la ruta especificada.';
}

   // Fecha actual en la esquina derecha
   $fecha_actual = date('d/m/Y');
   $pdf->SetFont('helvetica', '', 10);
   $pdf->Cell(0, 10, 'Fecha: ' . $fecha_actual, 0, 1, 'R');
   //$pdf->Ln(5);

   // Número del pedido - Movido debajo de la fecha
$pdf->Cell(0, 10, 'Pedido N°: ' . $pedido_id, 0, 1, 'R');
//$pdf->Ln(5);  // Salto de línea después del número de pedido

   // Título principal
   $pdf->SetFont('helvetica', 'B', 18);
   $pdf->Cell(0, 10, 'Detalle de Compras', 0, 1, 'C');
   $pdf->Ln(10);

   // Nombre del cliente
   $pdf->SetFont('helvetica', 'I', 12);
   $pdf->Cell(0, 10, 'Cliente: ' . $nombre_usuario, 0, 1, 'L');

   // Dirección del cliente
   $pdf->SetFont('helvetica', '', 12);
   $pdf->Cell(0, 10, 'Dirección: ' . $direccion_usuario, 0, 1, 'L');

  

       // Contador de productos
       $contador_productos = count($_SESSION['carrito']);
       $pdf->SetFont('helvetica', '', 12);
       $pdf->Cell(0, 10, $contador_productos . ' artículo(s) agregado(s)', 0, 1, 'L');
       $pdf->Ln(10);

   // Tabla con los productos
   $pdf->SetFont('helvetica', 'B', 10);
   $pdf->SetFillColor(173, 216, 230);
   $pdf->Cell(60, 10, 'Producto', 1, 0, 'C', 1);
   $pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C', 1);
   $pdf->Cell(40, 10, 'Precio', 1, 0, 'C', 1);
   $pdf->Cell(40, 10, 'Total', 1, 1, 'C', 1);

  // Recorrer los productos en el carrito
$fill = false;
$total = 0; // Asegúrate de reiniciar el total
foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
    $sql = "SELECT nombre, precio FROM productos WHERE id = ?";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param('i', $producto_id);
        $stmt->execute();
        $stmt->bind_result($nombre, $precio);
        $stmt->fetch();
        $stmt->close();
    }

    $subtotal = $detalle['cantidad'] * $precio;
    $total += $subtotal;  // Sumar al total

    $fill = !$fill;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor($fill ? 240 : 255, $fill ? 255 : 240, $fill ? 255 : 240);
    $pdf->Cell(60, 10, $nombre, 1, 0, 'L', 1);
    $pdf->Cell(30, 10, $detalle['cantidad'], 1, 0, 'C', 1);
    $pdf->Cell(40, 10, '$' . number_format($precio, 2), 1, 0, 'C', 1);
    $pdf->Cell(40, 10, '$' . number_format($subtotal, 2), 1, 1, 'C', 1);
}

// Ahora, solo calculamos el IVA y el total a pagar una vez al final, sin duplicar cálculos
$total_sin_iva = $total / 1.13;
$iva = $total_sin_iva * 0.13;
$total_con_iva = $total; // Este ya está calculado, no se repite.

   $pdf->Ln(10);
   $pdf->SetFont('helvetica', '', 12);
   $pdf->Cell(0, 10, 'Subtotal: $' . number_format($total_sin_iva, 2), 0, 1, 'L');
   $pdf->Cell(0, 10, 'IVA (13%): $' . number_format($iva, 2), 0, 1, 'L');

   $pdf->SetFont('helvetica', 'B', 14);
   $pdf->SetTextColor(255, 0, 0);
   $pdf->Cell(0, 10, 'Total a pagar: $' . number_format($total_con_iva, 2), 0, 1, 'L');

     // Guardar el PDF
     $pdf_output = __DIR__ . '/pdfs/pedido_' . $pedido_id . '.pdf';
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
           // Mostrar mensaje con botón de aceptar y redirigir a index.php
        echo '
        <script>
            alert("El pedido ha sido guardado y el correo con el PDF ha sido enviado.");
            window.location.href = "index.php"; // Redirigir a index.php
        </script>';

        } catch (Exception $e) {
            echo 'El pedido se guardó, pero no se pudo enviar el correo. Error: ' . $mail->ErrorInfo;
        }
    } else {
        echo 'El pedido se guardó, pero no se pudo generar el archivo PDF.';
    }

    unset($_SESSION['carrito']);
}
?>

