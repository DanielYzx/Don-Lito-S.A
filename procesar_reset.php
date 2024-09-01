<?php
// Inicia la sesión
session_start();
include 'conexion.php';

// Verifica si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoge el correo electrónico del formulario
    $email = $conexion->real_escape_string($_POST['email']);

    // Consulta para verificar si el correo existe en la base de datos
    $sql = "SELECT id FROM usuarios WHERE correo = '$email'";
    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        // Genera un token único
        $token = bin2hex(random_bytes(50));

        // Guarda el token en la base de datos con una expiración de 1 hora
        $sql = "UPDATE usuarios SET reset_token = '$token', token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE correo = '$email'";
        if ($conexion->query($sql) === TRUE) {
            // Enviar correo electrónico con el enlace de restablecimiento
            $resetLink = "http://tu_dominio.com/restablecer_contrasena.php?token=" . $token;

            $subject = "Restablecimiento de contraseña";
            $message = "Hola,\n\nHas solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para restablecerla:\n\n" . $resetLink . "\n\nSi no solicitaste este cambio, ignora este correo.";
            $headers = "From: no-reply@tu_dominio.com";

            if (mail($email, $subject, $message, $headers)) {
                // Redirige con un mensaje de éxito
                header("Location: olvido_contrasena.php?success=Te hemos enviado un enlace de restablecimiento a tu correo electrónico.");
                exit();
            } else {
                // Redirige con un mensaje de error si el correo no se pudo enviar
                header("Location: olvido_contrasena.php?error=No pudimos enviar el enlace de restablecimiento. Por favor, inténtalo de nuevo.");
                exit();
            }
        } else {
            // Redirige con un mensaje de error si la actualización falló
            header("Location: olvido_contrasena.php?error=Ocurrió un error al procesar tu solicitud. Por favor, inténtalo de nuevo.");
            exit();
        }
    } else {
        // Redirige con un mensaje de error si el correo no existe en la base de datos
        header("Location: olvido_contrasena.php?error=No existe ninguna cuenta con ese correo electrónico.");
        exit();
    }
} else {
    // Redirige a la página de inicio si el formulario no fue enviado correctamente
    header("Location: index.php");
    exit();
}

// Cierra la conexión
$conexion->close();
?>
