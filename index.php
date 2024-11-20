<?php
// Inicia la sesión
session_start();

$_SESSION['pagina_anterior'] = $_SERVER['REQUEST_URI']; // Almacena la URL actual

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla Principal</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="estilocarrito.css">
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
                        <a href="cerrar_sesion.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-danger">Cerrar sesión</a>   
                    <?php else: ?>
                        <img src="img/login.png" alt="User Icon" width="40" height="40" class="me-2">
                        <button type="button" class="btn btn-primary" onclick="showLoginForm()">Inicia sesión</button>
                        <div class="vertical-divider"></div>
                        <img src="img/login2.png" alt="User Icon" width="40" height="40" class="me-2">
                        <button type="button" class="btn btn-primary" onclick="showLoginFormem()">Inicia sesión Empleados</button>
                    <?php endif; ?>
                    <div class="vertical-divider"></div>
                    <button type="button" class="btn" onclick="window.location.href='vercarrito.php';" width="40" height="40">
               <img src="img/carrito.png" alt="Carrito Icon">
                </button>
                <span id="totalCarrito">
                    <?php 
                    echo isset($_SESSION['total_con_iva']) ? '$ ' . number_format($_SESSION['total_con_iva'], 2) : '$ 0.00';
                        ?>
                </span>
    
                </div>
                <div class="col-12 col-md-6 order-md-1">
                <form class="d-flex mt-2 mt-md-0" action="buscar.php" method="GET">
                        <input class="form-control me-2" type="search" name="busqueda" placeholder="¿Qué estás buscando?" aria-label="Search" 
                                value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
                                 <button type="submit" class="btn btn-success">
                                <img src="img/buscar.png" alt="Buscar" width="20" height="20">
                                </button>
                </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <!-- Aquí inicia el Contenedor del formulario de inicio de sesión empleados-->
        <?php if (!isset($_SESSION['user_name'])): ?>
<div class="login-overlay-em" id="loginemFormContainer" style="display: none;"> <!-- Oculto por defecto -->
    <div class="login-form-container">
        <button class="close-btn" id="closeBtnem">&times;</button>
        <h2>Iniciar Sesión</h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($_GET['error'])): ?>
            <div id="errorMessageem" class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['erroremS']); ?>
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
            <div class="mb-3">
                <label for="cargo" class="form-label">Cargo</label>
                <input type="cargo" class="form-control" id="cargo" name="cargo" readonly>
            </div>
            <div class="mb-3 text-end">
                <a href="#" onclick="showResetForm()" class="text-muted">¿Olvidaste tu contraseña?</a>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
 <!-- Aquí termina el Contenedor del formulario de inicio de sesión empleados-->
   

    <!-- Aquí inicia el Contenedor del formulario de inicio de sesión -->
    <?php if (!isset($_SESSION['user_name'])): ?>
<div class="login-overlay" id="loginFormContainer" style="display: none;">
    <div class="login-form-container">
        <button class="close-btn" id="closeBtn">&times;</button>
        <h2>Iniciar Sesión</h2>


        <!-- Mostrar mensaje de error si existe -->
<?php if (isset($_GET['error'])): ?>
    <div id="errorMessage" class="alert alert-danger" >
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

        <form id="loginForm" action="procesar_login.php" method="POST"">
        <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
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

        <!-- Paso 1: Ingresar correo -->
      
        <form id="resetForm" action="procesar_reset_local.php" method="POST" style="display: <?php echo isset($_GET['show_reset_form']) ? 'block' : 'none'; ?>;">
            <div class="mb-3">
                <label for="resetEmail" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="resetEmail" name="email" required>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Obtener Código de Restablecimiento</button>
            </div>
        </form>
        <!-- Paso 2: Ingresar código de validación (oculto inicialmente) -->
        <form id="validationForm" action="validar_codigo.php" method="POST" style="display: <?php echo isset($_GET['show_validation_code']) ? 'block' : 'none'; ?>;">  
        <div class="mb-3">
                <label for="validationCode" class="form-label">Código de Validación</label>
                <input type="text" class="form-control" id="validationCode" name="validation_code" required>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Validar Código</button>
            </div>
        </form>

        <!-- Paso 3: Ingresar nueva contraseña (oculto inicialmente) -->
        <form id="newPasswordForm" action="procesar_nueva_contrasena.php" method="POST" style="display: <?php echo isset($_GET['show_new_password_form']) ? 'block' : 'none'; ?>;">
   <!-- Campo de email oculto -->
   <input type="hidden" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
    <div class="mb-3">
        <label for="newPassword" class="form-label">Nueva Contraseña</label>
        <input type="password" class="form-control" id="newPassword" name="new_password" required>
    </div>
    <div class="mb-3">
        <label for="confirmNewPassword" class="form-label">Confirmar Nueva Contraseña</label>
        <input type="password" class="form-control" id="confirmNewPassword" name="confirm_new_password" required>
    </div>
    <div class="d-flex justify-content-center">
        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
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
                        echo '<a href="prueba.php?categoria_id=' . $row["id"] . '" class="card btn text-center">'; // Redirige a productos.php con el ID de la categoría
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

<!-- Sección de recomendaciones -->
<!-- Sección de recomendaciones -->
<div class="container my-4">
    <h3 class="text-center">Recomendaciones para ti</h3>
    <div class="row justify-content-center">
        <?php
        include 'conexion.php';
        function usuarioLogueado() {
            return isset($_SESSION['user_id']);
        }
        

        // Validar si el usuario está logueado
        if (isset($_SESSION['user_id'])) {
            // ID del usuario logueado
            $usuario_id = $_SESSION['user_id'];

            // Verificar si el usuario ha realizado algún pedido
            $sql = "SELECT COUNT(*) AS pedidos_count FROM pedidos WHERE usuario_id = $usuario_id";
            $result = mysqli_query($conexion, $sql);
            $row = mysqli_fetch_assoc($result);
            $pedidos_count = $row['pedidos_count'];

            // Si el usuario no tiene pedidos
            if ($pedidos_count == 0) {
                echo '<p class="text-center">Por el momento no tienes recomendaciones personalizadas, pero puedes ver nuestros productos más populares.</p>';
                echo '<h3 class="text-center">Estos son los productos más vendidos</h3>';

                // Obtener productos más vendidos desde la API
                $api_url = "http://127.0.0.1:5000/productos-mas-vendidos";
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $api_url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);

                if ($response === false) {
                    echo "Error al obtener productos más vendidos.";
                } else {
                    $data = json_decode($response, true);

                    if (isset($data) && count($data) > 0) {
                        // Obtener los IDs de los productos más vendidos
                        $productos_ids = array_column($data, 'producto_id');
                        $productos_ids_str = implode(',', $productos_ids);

                        // Consultar los detalles de los productos en la base de datos
                        $sql_productos = "SELECT * FROM productos WHERE id IN ($productos_ids_str)";
                        $result_productos = mysqli_query($conexion, $sql_productos);

                        if ($result_productos && mysqli_num_rows($result_productos) > 0) {
                            echo '<div class="productos-container">';

                            while ($producto = mysqli_fetch_assoc($result_productos)) {
                                echo '<div class="card-product">';
                                echo '<div class="card-product-img">';
                                echo '<img src="img/oferta1.jpg" alt="Imagen por defecto">';
                                echo '</div>';
                                echo '<div class="card-product-body">';
                                echo '<h2 class="card-product-title">' . htmlspecialchars($producto["nombre"]) . '</h2>';
                                echo '<p class="card-product-price">Precio: $' . htmlspecialchars($producto["precio"]) . '</p>';
                                echo '<p class="card-product-description">' . htmlspecialchars($producto["descripción"]) . '</p>';
                                echo '<p class="card-product-existencias">Existencias: ' . htmlspecialchars($producto["cantidad_disponible"]) . '</p>';

                                if (usuarioLogueado()) {
                                    // Determina si el producto está en el carrito
                                    $enCarrito = isset($_SESSION['carrito'][$producto["id"]]);
                                    
                                    // Cambia el icono según el estado en el carrito
                                    if ($enCarrito) {
                                        echo '<button class="agregar-carrito-btn" onclick="eliminarDelCarrito(event, this)" data-producto-id="' . htmlspecialchars($producto["id"]) . '">';
                                        echo '<img src="img/eliminarcarrito.png" alt="Eliminar del carrito" style="width: 25px; height: 25px;"></button>';
                                    } else {
                                        echo '<button class="agregar-carrito-btn" onclick="handleAddToCart(event, this)" ' . ($producto["cantidad_disponible"] <= 0 ? ' disabled' : '') . ' data-producto-id="' . htmlspecialchars($producto["id"]) . '">';
                                        echo '<img src="img/agregarcarrito.png" alt="Añadir al carrito" style="width: 25px; height: 25px;"></button>';
                                    }
                                } else {
                                    echo '<button class="agregar-carrito-btn" onclick="showLoginForm()" ' . ($producto["cantidad_disponible"] <= 0 ? ' disabled' : '') . ' data-producto-id="' . htmlspecialchars($producto["id"]) . '">';
                                    echo '<img src="img/agregarcarrito.png" alt="Añadir al carrito" style="width: 25px; height: 25px;"></button>';
                                }
                                
                                echo '</div>'; // Cerrar el cuerpo de la tarjeta
                                echo '</div>'; // Cerrar la tarjeta del producto
                               
                            }

                            echo '</div>'; // Cerrar contenedor de productos
                        } else {
                            echo "No se encontraron detalles de los productos más vendidos.";
                        }
                    } else {
                        echo "No se encontraron productos más vendidos.";
                    }
                }
                curl_close($curl);
            } else {
                // Si tiene pedidos, obtener recomendaciones personalizadas
                $api_url = "http://127.0.0.1:5000/recomendaciones/" . $usuario_id;
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $api_url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);

                if ($response === false) {
                    echo "Error al obtener recomendaciones.";
                } else {
                    $data = json_decode($response, true);

                    if (isset($data['recomendaciones']) && count($data['recomendaciones']) > 0) {
                        $productos_ids = array_column($data['recomendaciones'], 'producto_id');
                        $productos_ids_str = implode(',', $productos_ids);

                        // Consultar los detalles de los productos recomendados en la base de datos
                        $sql_productos = "SELECT * FROM productos WHERE id IN ($productos_ids_str)";
                        $result_productos = mysqli_query($conexion, $sql_productos);

                        if ($result_productos && mysqli_num_rows($result_productos) > 0) {
                            echo '<div class="productos-container">';

                            while ($producto = mysqli_fetch_assoc($result_productos)) {
                                echo '<div class="card-product">';
                                echo '<div class="card-product-img">';
                                echo '<img src="img/oferta1.jpg" alt="Imagen por defecto">';
                                echo '</div>';
                                echo '<div class="card-product-body">';
                                echo '<h2 class="card-product-title">' . htmlspecialchars($producto["nombre"]) . '</h2>';
                                echo '<p class="card-product-price">Precio: $' . htmlspecialchars($producto["precio"]) . '</p>';
                                echo '<p class="card-product-description">' . htmlspecialchars($producto["descripción"]) . '</p>';
                                echo '<p class="card-product-existencias">Existencias: ' . htmlspecialchars($producto["cantidad_disponible"]) . '</p>';
                                
                                if (usuarioLogueado()) {
                                    // Determina si el producto está en el carrito
                                    $enCarrito = isset($_SESSION['carrito'][$producto["id"]]);
                                    
                                    // Cambia el icono según el estado en el carrito
                                    if ($enCarrito) {
                                        echo '<button class="agregar-carrito-btn" onclick="eliminarDelCarrito(event, this)" data-producto-id="' . htmlspecialchars($producto["id"]) . '">';
                                        echo '<img src="img/eliminarcarrito.png" alt="Eliminar del carrito" style="width: 25px; height: 25px;"></button>';
                                    } else {
                                        echo '<button class="agregar-carrito-btn" onclick="handleAddToCart(event, this)" ' . ($producto["cantidad_disponible"] <= 0 ? ' disabled' : '') . ' data-producto-id="' . htmlspecialchars($producto["id"]) . '">';
                                        echo '<img src="img/agregarcarrito.png" alt="Añadir al carrito" style="width: 25px; height: 25px;"></button>';
                                    }
                                } else {
                                    echo '<button class="agregar-carrito-btn" onclick="showLoginForm()" ' . ($producto["cantidad_disponible"] <= 0 ? ' disabled' : '') . ' data-producto-id="' . htmlspecialchars($producto["id"]) . '">';
                                    echo '<img src="img/agregarcarrito.png" alt="Añadir al carrito" style="width: 25px; height: 25px;"></button>';
                                }

                                echo '</div>';// Cerrar el cuerpo de la tarjeta
                                echo '</div>';// Cerrar la tarjeta del producto
                            }

                            echo '</div>'; // Cerrar contenedor de productos
                        } else {
                            echo "No se encontraron detalles de las recomendaciones.";
                        }
                    } else {
                        echo "No se encontraron recomendaciones.";
                    }
                }
                curl_close($curl);
            }
        } else {
            // Si no está logueado
            echo '<p class="text-center">Inicia sesión para ver recomendaciones personalizadas.</p>';
            echo '<h3 class="text-center">Estos son los productos más vendidos</h3>';

            // Obtener productos más vendidos desde la API
            $api_url = "http://127.0.0.1:5000/productos-mas-vendidos";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);

            if ($response === false) {
                echo "Error al obtener productos más vendidos.";
            } else {
                $data = json_decode($response, true);

                if (isset($data) && count($data) > 0) {
                    $productos_ids = array_column($data, 'producto_id');
                    $productos_ids_str = implode(',', $productos_ids);

                    // Consultar los detalles de los productos más vendidos
                    $sql_productos = "SELECT * FROM productos WHERE id IN ($productos_ids_str)";
                    $result_productos = mysqli_query($conexion, $sql_productos);

                    if ($result_productos && mysqli_num_rows($result_productos) > 0) {
                        echo '<div class="productos-container">';

                        while ($producto = mysqli_fetch_assoc($result_productos)) {
                            echo '<div class="card-product">';
                            echo '<div class="card-product-img">';
                            echo '<img src="img/oferta1.jpg" alt="Imagen por defecto">';
                            echo '</div>';
                            echo '<div class="card-product-body">';
                            echo '<h2 class="card-product-title">' . htmlspecialchars($producto["nombre"]) . '</h2>';
                            echo '<p class="card-product-price">Precio: $' . htmlspecialchars($producto["precio"]) . '</p>';
                            echo '<p class="card-product-description">' . htmlspecialchars($producto["descripción"]) . '</p>';
                            echo '<p class="card-product-existencias">Existencias: ' . htmlspecialchars($producto["cantidad_disponible"]) . '</p>';
                           

                            echo '</div>';
                            echo '</div>';
                        }

                        echo '</div>'; // Cerrar contenedor de productos
                    } else {
                        echo "No se encontraron detalles de los productos más vendidos.";
                    }
                } else {
                    echo "No se encontraron productos más vendidos.";
                }
            }
            curl_close($curl);
        }
        ?>
    </div>




    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="validacionesformularios.js"></script>
    <script src="empleados.js"></script>
    <script src="validacionesproductos.js"></script>
    <script src="validacioncarrito.js"></script>
   <script src="scroll.js"></script>    
   <script>
    
// Función para manejar añadir o eliminar del carrito
function handleAddToCart(event, button) {
    const img = button.querySelector('img');
    const productId = button.getAttribute('data-producto-id');
    const isLoggedIn = <?php echo json_encode(usuarioLogueado()); ?>;
    if (!isLoggedIn) {
        showLoginForm();
        return;
    }
    const action = img.src.includes('agregarcarrito.png') ? 'add' : 'remove';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'agregar_al_carrito.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                if (action === 'add') {
                    img.src = 'img/eliminarcarrito.png';
                    img.alt = 'Eliminar del carrito';
                    button.style.backgroundColor = '#dc3545';
                    alert('Producto añadido al carrito.');
                } else {
                    img.src = 'img/agregarcarrito.png';
                    img.alt = 'Añadir al carrito';
                    button.style.backgroundColor = '#28a745';
                    alert('Producto eliminado del carrito.');

                }
                // Actualizar el total con IVA sin recargar la página
                actualizarTotalConIVA();

            } else {
                alert('Error: ' + response.error);
            }
        } else {
            alert('Error al procesar la solicitud.');
        }
    };
    const postData = `producto_id=${productId}&action=${action}` + (action === 'add' ? '&cantidad=1' : '');
    xhr.send(postData);
}

function actualizarTotalConIVA() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'obtener_total_con_iva.php', true); // Llama a un archivo PHP que te devolverá el total actualizado
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                const totalConIVA = document.getElementById('totalCarrito');
                totalConIVA.textContent = '$ ' + parseFloat(response.totalConIVA).toFixed(2); // Actualiza el total en la página
            } else {
                console.error('Error al actualizar el total con IVA:', response.error);
            }
        } else {
            console.error('Error al realizar la solicitud para el total con IVA.');
        }
    };
    xhr.send();
}


  </script>
</body>
</html>     