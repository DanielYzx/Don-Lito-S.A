<?php
// procesar_nueva_contrasena.php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $conexion->real_escape_string($_POST['token']);
    $newPassword = password_hash($conexion->real_escape_string($_POST['new_password']), PASSWORD_DEFAULT);
    $sql = "UPDATE usuarios SET contraseña = '$newPassword', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'";

    if ($conexion->query($sql) === TRUE) {
        header("Location: index.php?success_reset=Contraseña cambiada exitosamente.");
        exit();
    } else {
        header("Location: olvido_contrasena.php?error_reset=Ocurrió un error al cambiar tu contraseña.");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

$conexion->close();
?>