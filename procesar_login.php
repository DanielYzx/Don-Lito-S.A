<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $redirect_url = $_POST['redirect_url']; // URL de redirección

    // Verificar si el correo existe en la base de datos
    $stmt = $conexion->prepare("SELECT id, nombre, correo, contraseña FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Obtener el nombre del usuario y la contraseña hasheada de la base de datos
        $row = $result->fetch_assoc();
        $hashed_password = $row['contraseña'];
        $user_name = $row['nombre'];
        $user_id = $row['id'];
        $user_email = $row['correo'];

        // Verificar la contraseña ingresada con el hash almacenado
        if (password_verify($password, $hashed_password)) {
            // Inicio de sesión exitoso, guarda el nombre e ID del usuario en la sesión
            $_SESSION['user_name'] = $user_name;
            $_SESSION['user_id'] = $user_id; // Almacenar el ID del usuario en la sesión
            $_SESSION['user_email'] = $user_email; // Almacenar el correo del usuario en la sesión

            // Verificación de la sesión
            if (isset($_SESSION['user_email'])) {
                // Redirigir a la URL original
                header("Location: " . $redirect_url); 
                exit();
            } else {
                // Si no está en la sesión
                echo 'El correo electrónico no está disponible en la sesión.';
            }
        } else {
            // Contraseña incorrecta
            $error = "Usuario o contraseña incorrectos";
            header("Location: " . $redirect_url . "?error=" . urlencode($error)); // Redirige con mensaje de error
            exit();
        }
    } else {
        // Correo no encontrado
        $error = "El correo no está registrado.";
        header("Location: " . $redirect_url . "?error=" . urlencode($error)); // Redirige con mensaje de error
        exit();
    }
}
?>
