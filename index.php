<?php
// Inicia la sesión
session_start();
?>

<?php
// Agrega este código antes del HTML del formulario
if (isset($_SESSION['form_data'])) {
    echo "<pre>";
    print_r($_SESSION['form_data']); // Imprime los datos del formulario en la página
    echo "</pre>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla Principal</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
 <?php include 'conexion.php'; ?> 
    <nav class="navbar navbar-light">
        <div class="container-fluid">
            <div class="row w-100 align-items-center">
                <div class="col-6 col-md-2">
                    <a class="navbar-brand" href="#">
                        <img src="img/logo.png" alt="Logo" width="80" height="80">
                    </a>
                </div>
                <div class="col-6 col-md-4 d-flex justify-content-end align-items-center order-md-2">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <span class="me-2">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="cerrar_sesion.php" class="btn btn-danger">Cerrar sesión</a>
                    <?php else: ?>
                        <img src="img/login.png" alt="User Icon" width="40" height="40" class="me-2">
                        <button type="button" class="btn btn-primary" onclick="showLoginForm()">Inicia sesión</button>
                    <?php endif; ?>
                    <div class="vertical-divider"></div>
                    <button type="button" class="btn" width="40" height="40">
                        <img src="img/carrito.png" alt="Carrito Icon">
                    </button>
                    <span>$ 0.00</span>
                </div>
                <div class="col-12 col-md-6 order-md-1">
                    <form class="d-flex mt-2 mt-md-0" action="buscar.php" method="POST">
                        <input class="form-control me-2" type="search" name="busqueda" placeholder="¿Qué estás buscando?" aria-label="Search">
                        <button type="submit" class="btn btn-success">
                            <img src="img/buscar.png" alt="Buscar" width="20" height="20">
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
   
    <!-- Aquí inicia el Contenedor del formulario de inicio de sesión -->
    <?php if (!isset($_SESSION['user_name'])): ?>
<div class="login-overlay" id="loginFormContainer">
    <div class="login-form-container">
        <button class="close-btn" id="closeBtn">&times;</button>
        <h2>Iniciar Sesión</h2>

        <!-- Mostrar mensaje de error si existe -->
<?php if (isset($_GET['error'])): ?>
    <div id="errorMessage" class="alert alert-danger" >
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

        <form id="loginForm" action="procesar_login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3 text-end">
            <a href="#" onclick="showResetForm()" class="text-muted">¿Olvidaste tu contraseña?</a>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
            <div class="mt-3 text-center">
            <a href="#" onclick="showRegisterForm()" class="text-muted">¿No tienes cuenta? Crea una ahora</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
    <!-- Aquí termina el Contenedor del formulario de inicio de sesión -->
    <!--  aqui inicia el Formulario de Registro de Usuario -->
    <?php if (!isset($_SESSION['user_name'])): ?>
<div class="register-overlay" id="registerFormContainer" style="display: <?php echo isset($_SESSION['error_register']) ? 'flex' : 'none'; ?>;">
    <div class="register-form-container">
        <button class="close-btn" id="closeRegisterBtn">&times;</button>
        <h2>Crear Cuenta</h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($_SESSION['error_register'])): ?>
            <div id="errorRegisterMessage" class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error_register']); ?>
                <?php unset($_SESSION['error_register']); // Limpiar el mensaje de error ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" action="procesar_registro.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="registerEmail" name="email" required value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="registerDireccion" name="direccion" required value="<?php echo isset($_SESSION['form_data']['direccion']) ? htmlspecialchars($_SESSION['form_data']['direccion']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="registerPassword" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Crear Cuenta</button>
            </div>
            <div class="mt-3 text-center">
                <a href="#" onclick="showLoginForm()" class="text-muted">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<!-- aqui termina el Formulario de Registro de Usuario -->
<!-- Formulario para Restablecer Contraseña -->
<?php if (!isset($_SESSION['user_name'])): ?>
<div class="reset-overlay" id="resetFormContainer">
    <div class="reset-form-container">
        <button class="close-btn" id="closeResetBtn">&times;</button>
        <h2>Restablecer Contraseña</h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($_GET['error_reset'])): ?>
            <div id="errorResetMessage" class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error_reset']); ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensaje de éxito si existe -->
        <?php if (isset($_GET['success_reset'])): ?>
            <div id="successResetMessage" class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success_reset']); ?>
            </div>
        <?php endif; ?>

        <form id="resetForm" action="procesar_reset.php" method="POST">
            <div class="mb-3">
                <label for="resetEmail" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="resetEmail" name="email" required>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Enviar enlace de restablecimiento</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<!-- aqui termina el Formulario de olvido su contraseña -->
    <div class="row mt-3">
        <div class="col-12 nav-container">
            <!-- Contenedor de los botones -->
            <div class="buttons-container">
            <div class="dropdown">
    <button class="nav-buttons dropdown-toggle" id="btnCategorias" type="button">
        <img src="img/menu.png" alt="menu">
        Todas las categorías
    </button>
    <div class="dropdown-menu" id="subMenuCategorias">
    <?php
// Consulta para obtener todas las categorías
$sql = "SELECT id, nombre FROM categorias";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="dropdown-submenu">';
        echo '<a class="dropdown-item" href="productos.php?categoria_id=' . $row["id"] . '">' . $row["nombre"] . '</a>';
        echo '</div>';
    }
} else {
    echo '<a class="dropdown-item" href="#">No hay categorías disponibles</a>';
}
?>
    </div>
</div>
                <!-- Otros botones -->
<button class="nav-buttons" onclick="location.href='productos.php?categoria_id=1'">Electrónica</button>
<button class="nav-buttons" onclick="location.href='productos.php?categoria_id=2'">Ropa</button>
<button class="nav-buttons" onclick="location.href='productos.php?categoria_id=3'">Hogar</button>
<button class="nav-buttons" onclick="location.href='productos.php?categoria_id=4'">Deportes</button>
<button class="nav-buttons" onclick="location.href='productos.php?categoria_id=5'">Bebidas</button>
            </div>
        </div>
    </div>

        <!-- Carrusel de imágenes de ofertas -->
        <div id="carouselOfertas" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="img/oferta1.jpg" class="d-block w-100" alt="Oferta 1">
                </div>
                <div class="carousel-item">
                    <img src="img/banner1.jpg" class="d-block w-100" alt="Oferta 2">
                </div>
                <div class="carousel-item">
                    <img src="img/banner2.jpg" class="d-block w-100" alt="Oferta 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselOfertas" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselOfertas" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="container my-4">
            <h3 class="text-center">Categorías destacadas</h3>
            <div class="row justify-content-center">
            <?php
                // Consulta para obtener las categorías destacadas
                $sql = "SELECT id,nombre, imagen FROM categorias"; // Asegúrate de que esta consulta se ajuste a tu base de datos
                $result = $conexion->query($sql);

                if ($result->num_rows > 0) {
                    // Generar tarjetas dinámicamente
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="col-auto">';
                       // echo '<a href="#" class="card btn text-center">';
                        echo '<a href="productos.php?categoria_id=' . $row["id"] . '" class="card btn text-center">'; // Redirige a productos.php con el ID de la categoría
                        echo '<div class="card-body">';
                        echo '<p class="card-text">' . $row["nombre"] . '</p>';
                        echo '</div>';
                        echo '<div class="card-img">';
                        echo '<img src="img/oferta1.jpg" alt="Imagen por defecto">';
                        echo '</div>';
                        //  aqui comienza Mostrar la imagen desde la base de datos
                       // echo '<div class="card-img">';
                        // Mostrar la imagen desde la base de datos
                        //echo '<img src="img/' . $row["imagen"] . '" alt="' . $row["nombre"] . '">';
                        //echo '</div>';
                        // a qui termina Mostrar la imagen desde la base de datos
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo "No se encontraron categorías destacadas.";
                }

                // Cerrar conexión
                $conexion->close();
            ?>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
   <script src="scroll.js"></script>    
   <script>
 // Función para mostrar el formulario
function showLoginForm() {
    document.getElementById('loginFormContainer').style.display = 'flex';
}

// Función para cerrar el formulario
document.getElementById("closeBtn").addEventListener("click", function() {
    document.getElementById("loginFormContainer").style.display = "none";
    removeErrorParam(); // Limpiar el parámetro 'error'


    // Ocultar el mensaje de error
    const errorMsg = document.getElementById("errorMessage");
    if (errorMsg) {
        errorMsg.style.display = "none";

    }

    // Limpiar los campos del formulario
    clearFormFields();
    

});

// Función para limpiar los campos del formulario
function clearFormFields() {
    document.getElementById("loginForm").reset();
}

// Cerrar el formulario si se hace clic fuera de él
window.onclick = function(event) {
    var loginFormContainer = document.getElementById('loginFormContainer');
    if (event.target == loginFormContainer) {
        loginFormContainer.style.display = 'none';
        removeErrorParam(); // Limpiar el parámetro 'error' al cerrar haciendo clic fuera
    }


};

// Mantén el formulario abierto si hay un error en la URL
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        showLoginForm();
        removeErrorParam(); // Limpiar el parámetro 'error' después de mostrar el formulario
    }
};

// Función para limpiar el parámetro 'error' de la URL
function removeErrorParam() {
    const url = new URL(window.location);
    url.searchParams.delete('error');
    window.history.replaceState({}, document.title, url);
}

// Ocultar el contenedor del formulario
document.getElementById("loginFormContainer").style.display = "none";

// aqui inicia la funcion del formulario de registro

// Función para mostrar el formulario de registro
function showRegisterForm() {
    document.getElementById('registerFormContainer').style.display = 'flex';
}


// Función para limpiar los campos del formulario de registro
//function clearRegisterFormFields() {
  //  document.getElementById("registerForm").reset();
//}


function clearRegisterFormFields() {
    console.log("Limpiando campos del formulario");
    document.getElementById("registerForm").reset();
}

document.getElementById("closeRegisterBtn").addEventListener("click", function() {
    console.log("Formulario cerrado");
    document.getElementById("registerFormContainer").style.display = "none";
    removeRegisterErrorParam(); // Limpiar el parámetro 'error_register'

    // Ocultar el mensaje de error
    const errorRegisterMsg = document.getElementById("errorRegisterMessage");
    if (errorRegisterMsg) {
        errorRegisterMsg.style.display = "none";
    }

    // Limpiar los campos del formulario
    clearRegisterFormFields();
});



// Mantén el formulario abierto si hay un error en la URL
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error_register')) {
        showRegisterForm();
        removeRegisterErrorParam(); // Limpiar el parámetro 'error_register' después de mostrar el formulario
    }
};

// Función para limpiar el parámetro 'error_register' de la URL
function removeRegisterErrorParam() {
    const url = new URL(window.location);
    url.searchParams.delete('error_register');
    window.history.replaceState({}, document.title, url);
}

// Asegúrate de que el formulario esté oculto si no hay errores
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('error_register')) {
        document.getElementById("registerFormContainer").style.display = "none";
    }
});
///////////////////////////////////////////////////////////////////////////////////////


// aqui inician la Función para  el formulario de restablecimiento de contraseña
// Función para mostrar el formulario de restablecimiento de contraseña
function showResetForm() {
    document.getElementById('resetFormContainer').style.display = 'flex';
}

// Función para cerrar el formulario de restablecimiento de contraseña
document.getElementById("closeResetBtn").addEventListener("click", function() {
    document.getElementById("resetFormContainer").style.display = "none";
    removeResetErrorParam(); // Limpiar el parámetro 'error_reset'
    removeResetSuccessParam(); // Limpiar el parámetro 'success_reset'
    
    // Limpiar los campos del formulario
    clearResetFormFields();
});

// Función para limpiar los campos del formulario de restablecimiento de contraseña
function clearResetFormFields() {
    document.getElementById("resetForm").reset();
}

// Mantén el formulario abierto si hay un error o un mensaje de éxito en la URL
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error_reset')) {
        showResetForm();
        removeResetErrorParam(); // Limpiar el parámetro 'error_reset' después de mostrar el formulario
    }
    if (urlParams.has('success_reset')) {
        showResetForm();
        removeResetSuccessParam(); // Limpiar el parámetro 'success_reset' después de mostrar el formulario
    }
};

// Función para limpiar el parámetro 'error_reset' de la URL
function removeResetErrorParam() {
    const url = new URL(window.location);
    url.searchParams.delete('error_reset');
    window.history.replaceState({}, document.title, url);
}

// Función para limpiar el parámetro 'success_reset' de la URL
function removeResetSuccessParam() {
    const url = new URL(window.location);
    url.searchParams.delete('success_reset');
    window.history.replaceState({}, document.title, url);
}

// Ocultar el contenedor del formulario de restablecimiento de contraseña
document.getElementById("resetFormContainer").style.display = "none";





</script>
</script>
</body>
</html>     