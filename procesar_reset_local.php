<?php
session_start();
include 'conexion.php'; // Asegúrate de tener una conexión a la base de datos
// Verifica si se debe mostrar el formulario de validación de código
// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\Don-Lito-S.A\PHPMailer\src\Exception.php';
require 'C:\xampp\htdocs\Don-Lito-S.A\PHPMailer\src\PHPMailer.php';
require 'C:\xampp\htdocs\Don-Lito-S.A\PHPMailer\src\SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Verificar si el correo existe
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generar token y actualizar en la base de datos
        $token = bin2hex(random_bytes(2));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $conexion->prepare("UPDATE usuarios SET reset_token = ?, reset_token_expiry = ? WHERE correo = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Enviar el correo con PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->SMTPAuth = false;
            $mail->Port = 1025;

            $mail->setFrom('no-reply@tu-dominio.com', 'Tu Sitio Web');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Restablecer Contraseña';
            $mail->Body = "Tu código de restablecimiento es: $token";

            $mail->send();
            
            // Guardar email en sesión
            $_SESSION['email'] = $email;
            
            // Redirigir al formulario de validación de código
            header("Location: index.php?show_validation_code=true");
            exit();
        } catch (Exception $e) {
            header("Location: index.php?error_reset=No se pudo enviar el correo.");
            exit();
        }
    } else {
        header("Location: index.php?error_reset=El correo no está registrado.");
        exit();
    }
}
?>
