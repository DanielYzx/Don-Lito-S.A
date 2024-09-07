<?php
session_start();
include 'conexion.php'; // Asegúrate de tener una conexión a la base de datos

if (isset($_POST['validation_code'])) {
    $email = $_SESSION['email'];
    $token = $_POST['validation_code'];

    // Verifica si el código es válido
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ? AND reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El código es válido, redirigir al formulario de nueva contraseña
        header("Location: index.php?show_new_password=true");
        exit();
    } else {
       // header("Location: index.php?error_reset=El código de validación es incorrecto o ha expirado.");
        header("Location: index.php?show_validation_code=true&error_reset=El código de validación es incorrecto o ha expirado.");
        exit();
    }
}
?>
