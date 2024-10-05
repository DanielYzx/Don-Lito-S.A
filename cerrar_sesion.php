<?php
session_start();
session_destroy(); // Destruir todas las sesiones

// Verificar si hay una URL de redirección
if (isset($_GET['redirect'])) {
    $redirect_url = urldecode($_GET['redirect']); // Decodificar la URL
} else {
    $redirect_url = 'index.php'; // Página por defecto
}

// Redirigir a la página anterior o al index
header("Location: $redirect_url");
exit();

?>