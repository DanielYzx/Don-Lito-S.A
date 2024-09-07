<?php
session_start();
include 'conexion.php'; // Asegúrate de tener una conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; // Asegúrate de pasar el email en el formulario de nueva contraseña
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if ($new_password === $confirm_new_password) {
        // Hashear la nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Actualiza la contraseña en la base de datos
        $stmt = $conexion->prepare("UPDATE usuarios SET contraseña = ?, reset_token = NULL, reset_token_expiry = NULL WHERE correo = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            // Si la actualización fue exitosa
            header('Location: index.php?success_reset=Contraseña actualizada correctamente.');
        } else {
            // Error al ejecutar la actualización
            header('Location: index.php?error_reset=Error al actualizar la contraseña.');
        }
        $stmt->close();
    } else {
        // Las contraseñas no coinciden
        header('Location: index.php?error_reset=Las contraseñas no coinciden.');
    }
}
?>
