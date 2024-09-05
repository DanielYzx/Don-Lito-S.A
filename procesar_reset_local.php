<?php
session_start();
require 'PHPMailer/PHPMailerAutoload.php';
include 'conexion.php'; // Asegúrate de tener una conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Verifica si el correo electrónico existe en la base de datos
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(4)); // Genera un token aleatorio

        // Guarda el token en la base de datos con un timestamp
        $stmt = $conexion->prepare("UPDATE usuarios SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE correo = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Enviar correo con el token
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'localhost'; // Configura MailHog
        $mail->Port = 1025; // Puerto de MailHog
        $mail->setFrom('noreply@example.com', 'Tu Aplicación');
        $mail->addAddress($email);
        $mail->Subject = 'Enlace de Restablecimiento de Contraseña';
        $mail->Body = 'Haz clic en el siguiente enlace para restablecer tu contraseña: 
        http://localhost/tu_proyecto/index.php?show_new_password=true';

        if ($mail->send()) {
            header('Location: index.php?success_reset=Se ha enviado un enlace a tu correo electrónico.');
        } else {
            header('Location: index.php?error_reset=Hubo un problema al enviar el correo.');
        }
    } else {
        header('Location: index.php?error_reset=El correo electrónico no está registrado.');
    }
}
?>
