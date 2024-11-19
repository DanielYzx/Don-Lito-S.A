<?php
include 'conexion.php'; // Incluye la conexión a la base de datos
session_start(); // Asegúrate de iniciar la sesión
require_once('tcpdf/tcpdf.php'); // Incluye la librería TCPDF

// Configurar la zona horaria para El Salvador
date_default_timezone_set('America/El_Salvador'); // Ajusta la zona horaria a El Salvador

// Verifica si hay productos en el carrito
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
     // Recupera el ID del usuario (esto depende de tu sistema de autenticación)
     $usuario_id = $_SESSION['user_id'];  // Asegúrate de tener el usuario logueado
     $total = 0; 

     // Obtener el nombre del usuario desde la base de datos
     $sql = "SELECT nombre FROM usuarios WHERE id = ?";
     if ($stmt = $conexion->prepare($sql)) {
         $stmt->bind_param('i', $usuario_id);
         $stmt->execute();
         $stmt->bind_result($nombre_usuario);
         $stmt->fetch();
         $stmt->close();
     }

     // Crear un nuevo objeto TCPDF
     $pdf = new TCPDF();
     $pdf->AddPage(); // Añadir una página
     $pdf->SetFont('helvetica', '', 12); // Definir la fuente

     // Fecha actual en la esquina derecha
     $fecha_actual = date('d/m/Y'); // Formato de la fecha
     $pdf->SetFont('helvetica', '', 10); // Cambiar tamaño de fuente para la fecha
     $pdf->Cell(0, 10, 'Fecha: ' . $fecha_actual, 0, 1, 'R'); // Alineación derecha
     $pdf->Ln(5); // Salto de línea

     // Título principal
     $pdf->SetFont('helvetica', 'B', 18); // Fuente más grande y negrita
     $pdf->Cell(0, 10, 'Tu Carrito de Compras', 0, 1, 'C');
     $pdf->Ln(10);

     // Nombre del usuario
     $pdf->SetFont('helvetica', 'I', 12); // Cursiva para el nombre del usuario
     $pdf->Cell(0, 10, 'Cliente: ' . $nombre_usuario, 0, 1, 'L');
     $pdf->Ln(5);

     // Contador de productos
     $contador_productos = count($_SESSION['carrito']);
     $pdf->SetFont('helvetica', '', 12);
     $pdf->Cell(0, 10, $contador_productos . ' artículo(s) agregado(s)', 0, 1, 'L');
     $pdf->Ln(10);

     // Tabla con los productos
     $pdf->SetFont('helvetica', 'B', 10); // Fuente en negrita para los encabezados
     $pdf->SetFillColor(173, 216, 230); // Celeste para los encabezados
     $pdf->Cell(60, 10, 'Producto', 1, 0, 'C', 1);
     $pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C', 1);
     $pdf->Cell(40, 10, 'Precio', 1, 0, 'C', 1);
     $pdf->Cell(40, 10, 'Total', 1, 1, 'C', 1);

     // Recorrer los productos en el carrito
     $total = 0;
     $fill = false; // Variable para alternar color de fondo de las filas
     foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
         // Validar si el precio es válido
         if (!isset($detalle['precio']) || $detalle['precio'] <= 0) {
             continue; // Si el precio no es válido, omite este producto
         }

         // Consulta para obtener el nombre del producto y el precio desde la base de datos
         $sql = "SELECT nombre, precio FROM productos WHERE id = ?";
         if ($stmt = $conexion->prepare($sql)) {
             $stmt->bind_param('i', $producto_id);
             $stmt->execute();
             $stmt->bind_result($nombre, $precio);
             $stmt->fetch();
             $stmt->close();
         }

         // Usar el precio de la base de datos si está disponible
         $precio = $precio ?? $detalle['precio'];

         // Calcular el total del producto
         $subtotal = $detalle['cantidad'] * $precio;
         $total += $subtotal;

         // Alternar el color de fondo para las filas
         $fill = !$fill; // Cambia el estado de $fill para alternar el color

         // Mostrar fila en la tabla con color de fondo alterno
         $pdf->SetFont('helvetica', '', 10);
         $pdf->SetFillColor($fill ? 240 : 255, $fill ? 255 : 240, $fill ? 255 : 240); // Color alterno
         $pdf->Cell(60, 10, $nombre, 1, 0, 'L', 1);
         $pdf->Cell(30, 10, $detalle['cantidad'], 1, 0, 'C', 1);
         $pdf->Cell(40, 10, '$' . number_format($precio, 2), 1, 0, 'C', 1);
         $pdf->Cell(40, 10, '$' . number_format($subtotal, 2), 1, 1, 'C', 1);
     }

     // Calcular el total y el IVA
     $total_sin_iva = $total / 1.13;
     $iva = $total_sin_iva * 0.13;
     $total_con_iva = $total;

     // Mostrar el subtotal, IVA y total
     $pdf->Ln(10);
     $pdf->SetFont('helvetica', '', 12); // Asegúrate de usar la misma fuente que para 'Cliente'
     $pdf->Cell(0, 10, 'Subtotal: $' . number_format($total_sin_iva, 2), 0, 1, 'L');
     $pdf->Cell(0, 10, 'IVA (13%): $' . number_format($iva, 2), 0, 1, 'L');

     // Hacer que "Total a pagar" resalte
     $pdf->SetFont('helvetica', 'B', 14); // Usar negrita para resaltar el total
     $pdf->SetTextColor(255, 0, 0); // Cambiar el color del texto a rojo para el total
     $pdf->Cell(0, 10, 'Total a pagar: $' . number_format($total_con_iva, 2), 0, 1, 'L');

     // Salida del PDF
     $pdf->Output('carrito_compras.pdf', 'I'); // Mostrar el PDF en el navegador
} else {
    echo 'No hay productos en el carrito.';
}
?>
