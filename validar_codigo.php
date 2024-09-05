<?php
session_start();
include 'conexion.php'; // Asegúrate de tener una conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $validation_code = $_POST['validation_code'];

    // Verifica el token en la base de datos
    $stmt = $conexion->prepare("SELECT reset_token, reset_token_expiry FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['reset_token'] === $validation_code && $user['reset_token_expiry'] > date('Y-m-d H:i:s')) {
        header('Location: index.php?show_new_password=true');
    } else {
        header('Location: index.php?error_reset=Código de validación incorrecto o expirado.');
    }
}
?>
