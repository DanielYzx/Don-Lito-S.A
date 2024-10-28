<?php
session_start();
include 'conexion.php'; // Asegúrate de tener una conexión a la base de datos
require('fpdf/fpdf.php'); // Asegúrate de tener FPDF incluido

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Asegúrate de que el correo y otros detalles estén disponibles
    if (isset($_POST['email']) && isset($_POST['pedido_detalles']) && isset($_POST['total'])) {
        $email = $_POST['email'];
        $pedidoDetalles = $_POST['pedido_detalles'];
        $total = $_POST['total']; // Asume que el total también se pasa desde el formulario
        $pedido_id = time(); // Usar la marca de tiempo como ID de pedido

        // Guardar el pedido en la base de datos
        $stmt = $conexion->prepare("INSERT INTO pedidos (usuario_email, detalles, total) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $email, $pedidoDetalles, $total);

        if ($stmt->execute()) {
            // Generar el PDF
            $pdf_path = generarPDF($pedido_id, $pedidoDetalles, $total);
            
            // Enviar el correo
            if (enviarCorreo($email, $pdf_path)) {
                header('Location: index.php?success=true');
                exit();
            } else {
                header('Location: index.php?error=email=Error al enviar el correo.');
                exit();
            }
        } else {
            header('Location: index.php?error=Error al guardar el pedido.');
            exit();
        }
        $stmt->close();
    } else {
        header('Location: index.php?error=Datos faltantes.');
        exit();
    }
}

// Función para generar el PDF
function generarPDF($pedido_id, $detalles, $total) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, "Pedido ID: $pedido_id");
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, "Detalles del Pedido:\n$detalles");
    $pdf->Ln();
    $pdf->Cell(40, 10, "Total: $" . number_format($total, 2));
    
    // Guardar el PDF
    $pdf_file = "pdfs/pedido_$pedido_id.pdf"; // Asegúrate de tener la carpeta 'pdfs' creada
    $pdf->Output('F', $pdf_file);
    
    return $pdf_file;
}

// Función para enviar el correo
function enviarCorreo($destinatario, $pdf_path) {
    $subject = "Confirmación de Pedido";
    $message = "Gracias por su pedido. Adjunto encontrará la confirmación.";
    $headers = "From: no-reply@tusitio.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"boundary\"\r\n";

    // Crear el cuerpo del mensaje
    $body = "--boundary\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $message . "\r\n";
    
    // Adjuntar el PDF
    $file_content = chunk_split(base64_encode(file_get_contents($pdf_path)));
    $body .= "--boundary\r\n";
    $body .= "Content-Type: application/pdf; name=\"" . basename($pdf_path) . "\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"" . basename($pdf_path) . "\"\r\n\r\n";
    $body .= $file_content . "\r\n";
    $body .= "--boundary--";

    // Enviar el correo
    return mail($destinatario, $subject, $body, $headers);
}
?>
