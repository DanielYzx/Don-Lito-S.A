
<?php
// Inicia la sesión
session_start();
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
                        <a href="cerrar_sesion.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-danger">Cerrar sesión</a>   
                    <?php else: ?>
                        <img src="img/login.png" alt="User Icon" width="40" height="40" class="me-2">
                        <button type="button" class="btn btn-primary" onclick="showLoginForm()">Inicia sesión</button>
                    <?php endif; ?>
                    <div class="vertical-divider"></div>
                    <button type="button" class="btn" onclick="vercarrito.php;"width="40" height="40";>
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

    <div class="carrito-overlay" id="carritoContainer" style="display: none;">
    <div class="carrito-form-container">
        <button class="close-btn" id="closeCarritoBtn">&times;</button>
        <!-- Aquí va el contenido de tu carrito -->
        <?php
include 'conexion.php'; // Asegúrate de incluir la conexión a la base de datos si la necesitas

// Verifica si hay productos en el carrito
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    // Contador para el total
    $total = 0;

    echo '<div class="carrito-container">';
    echo '<h1>Tu Carrito de Compras</h1>';
    echo '<table>';
    echo '<tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th>Acciones</th>
          </tr>';

    // Recorrer los productos en el carrito
    foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
        // Consulta para obtener el nombre del producto desde la base de datos
        $sql = "SELECT nombre FROM productos WHERE id = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('i', $producto_id);
            $stmt->execute();
            $stmt->bind_result($nombre);
            $stmt->fetch();
            $stmt->close();
        }

        // Calcular el total del producto
        $subtotal = $detalle['cantidad'] * $detalle['precio'];
        $total += $subtotal;

        // Mostrar producto en la tabla
        echo '<tr>';
        echo '<td>' . htmlspecialchars($nombre) . '</td>';
        echo '<td>
                <div class="cantidad-container">
                    <input type="number" value="' . $detalle['cantidad'] . '" min="1" class="cantidad-input" data-producto-id="' . $producto_id . '" onchange="mostrarBotonActualizar(' . $producto_id . ')">
                    
                    <button class="btn-cancelar" data-producto-id="' . $producto_id . '" style="display:none;" onclick="cancelarCambio(' . $producto_id . ', ' . $detalle['cantidad'] .')">
                        <img src="img/cancelarcarrito.png" alt="Actualizar" style="width: 15px; height: 15px;">
                    </button>
                    <button class="btn-actualizar" data-producto-id="' . $producto_id . '" style="display:none;" onclick="actualizarCantidad(' . $producto_id . ')">
                        <img src="img/actualizarcarrito.png" alt="Actualizar" style="width: 15px; height: 15px;">
                    </button>
                </div>';
        echo '<td>$' . number_format($detalle['precio'], 2) . '</td>';
        echo '<td>$' . number_format($subtotal, 2) . '</td>';
        echo '<td>
                <button class="btn-eliminar" onclick="eliminarProducto(' . $producto_id . ')">Eliminar</button>
              </td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<h2>Total: $' . number_format($total, 2) . '</h2>';
    echo '<button onclick="irAComprar()">Finalizar Compra</button>'; // O un botón para continuar comprando

    echo '</div>'; // Cerrar contenedor de carrito

} else {
    echo '<p>No hay productos en el carrito.</p>';
}
?>
    </div>
 </div>
 <script>
function eliminarProducto(productoId) {
    if (confirm("¿Estás seguro de que quieres eliminar este producto del carrito?")) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'agregar_al_carrito.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error al eliminar el producto: ' + response.error);
                }
            }
        };
        xhr.send(`producto_id=${productoId}&action=remove`);
    }
}

function mostrarBotonActualizar(productoId) {
    const botonActualizar = document.querySelector(`.btn-actualizar[data-producto-id="${productoId}"]`);
    const botonCancelar = document.querySelector(`.btn-cancelar[data-producto-id="${productoId}"]`);
    botonActualizar.style.display = 'inline-block'; // Mostrar el botón de actualización
    botonCancelar.style.display = 'inline-block'; // Mostrar el botón de cancelar
}

function cancelarCambio(productoId, cantidadAnterior) {
    const inputCantidad = document.querySelector(`.cantidad-input[data-producto-id="${productoId}"]`);
    inputCantidad.value = cantidadAnterior; // Restablece el valor al anterior
    ocultarBotones(productoId); // Ocultar los botones de actualizar y cancelar
}

function ocultarBotones(productoId) {
    const botonActualizar = document.querySelector(`.btn-actualizar[data-producto-id="${productoId}"]`);
    const botonCancelar = document.querySelector(`.btn-cancelar[data-producto-id="${productoId}"]`);
    botonActualizar.style.display = 'none';
    botonCancelar.style.display = 'none';
}

function actualizarCantidad(productoId) {
    const inputCantidad = document.querySelector(`.cantidad-input[data-producto-id="${productoId}"]`);
    const nuevaCantidad = parseInt(inputCantidad.value);

    if (nuevaCantidad > 0) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'agregar_al_carrito.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error al actualizar la cantidad: ' + response.error);
                }
            }
        };
        xhr.send(`producto_id=${productoId}&action=update&cantidad=${nuevaCantidad}`);
    } else {
        alert("La cantidad debe ser mayor que cero.");
    }
}

function irAComprar() {
    window.location.href = 'pagina_pago.php'; // Cambia esto según la ruta de tu página de pago
}

function showCarrito() {
    var carritoContainer = document.getElementById('carritoContainer');
    carritoContainer.style.display = 'block';
    document.body.classList.add('no-scroll'); // Para evitar desplazamiento en la página
}

document.getElementById("closeCarritoBtn").addEventListener("click", function() {
    document.getElementById("carritoContainer").style.display = "none";
    document.body.classList.remove('no-scroll'); // Volver a permitir el desplazamiento
});

</script>

<style>
    /* Estilos para el overlay del carrito */
.carrito-overlay {
    position: fixed;
    top: 110px; /* Ajusta según la altura del navbar */
    right: 0;
    width: 65%;
    height: 82%;
    border-radius: 8px;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* Contenedor del formulario del carrito */
.carrito-form-container {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 100%; /* Ajusta el ancho para que ocupe el 100% del contenedor padre */
    max-width: 700px; /* Incrementa el ancho máximo del contenedor */
    max-height: 98%;
    margin: 20px; /* Añade margen alrededor del contenedor */
    overflow-y: auto; /* Permite scroll si el contenido del carrito es muy largo */
}


/* Botón de cierre */
.close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: transparent;
    border: none;
    font-size: 36px;
    font-weight: bold;
    cursor: pointer;
    color: #440cdf;
}

.close-btn:hover {
    color: #ff0000; /* Cambia el color al pasar el mouse */
}
.register-form-container::-webkit-scrollbar {
    display: none;  /* Oculta el scroll en Chrome, Safari y Edge */
}
</style>

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

        <!-- Formulario de Registro de Usuario -->
        <?php if (!isset($_SESSION['user_name'])): ?>
            <div class="register-overlay" id="registerFormContainer" style="display: <?php echo isset($_SESSION['error_register']) ? 'flex' : 'none'; ?>;">
                <div class="register-form-container">
                    <button class="close-btn" id="closeRegisterBtn">&times;</button>
                    <h2>Crear Cuenta</h2>

                    <!-- Mensaje de error si existe -->
                    <?php if (isset($_SESSION['error_register'])): ?>
                        <div id="errorRegisterMessage" class="alert alert-danger">
                            <?php echo htmlspecialchars($_SESSION['error_register']); ?>
                            <?php unset($_SESSION['error_register']); ?>
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

        <!-- Formulario para Restablecer Contraseña -->
        <?php if (!isset($_SESSION['user_name'])): ?>
            <div class="reset-overlay" id="resetFormContainer">
                <div class="reset-form-container">
                    <button class="close-btn" id="closeResetBtn">&times;</button>
                    <h2>Restablecer Contraseña</h2>

                    <!-- Mensaje de error si existe -->
                    <?php if (isset($_GET['error_reset'])): ?>
                        <div id="errorResetMessage" class="alert alert-danger">
                            <?php echo htmlspecialchars($_GET['error_reset']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Mensaje de éxito si existe -->
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
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmNewPassword" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirmNewPassword" name="confirm_new_password" required>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <!-- aqui termina el Formulario de olvido su contraseña -->
 <div class="row mt-3">
    <div class="col-12 nav-container">
        <!-- Contenedor de los botones de categorías -->
        <div class="buttons-container">
            <!-- Dropdown para todas las categorías -->
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

            <!-- Botones de categorías adicionales -->
            <button class="nav-buttons" onclick="location.href='productos.php?categoria_id=1'">Electrónica</button>
            <button class="nav-buttons" onclick="location.href='productos.php?categoria_id=2'">Ropa</button>
            <button class="nav-buttons" onclick="location.href='productos.php?categoria_id=3'">Hogar</button>
            <button class="nav-buttons" onclick="location.href='productos.php?categoria_id=4'">Deportes</button>
            <button class="nav-buttons" onclick="location.href='productos.php?categoria_id=5'">Bebidas</button>
        </div>
    </div>
</div>

<!-- Carrusel de imágenes de ofertas -->


<!-- Categorías destacadas -->
<div class="contenedor-principal">
<?php
include 'conexion.php';


// Verificar si el usuario está logueado
function usuarioLogueado() {
    return isset($_SESSION['user_id']); // O el nombre de la variable de sesión que uses para almacenar el ID del usuario
}

// Obtener el ID de la categoría desde la URL y asegurarse de que es un número entero
$categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;

if ($categoria_id > 0) {
    // Preparar la consulta SQL
    $sql = "SELECT id, nombre, precio, descripción, imagen, cantidad_disponible 
            FROM productos 
            WHERE categoria_id = ?";

    // Preparar la sentencia
    if ($stmt = $conexion->prepare($sql)) {
        // Vincular el parámetro (sólo números enteros)
        $stmt->bind_param('i', $categoria_id);

        // Ejecutar la sentencia
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Contenedor de productos
            echo '<div class="productos-container">';

            while ($producto = $result->fetch_assoc()) {
                 // Mostrar producto en una tarjeta personalizada
            echo '<div class="card-product">';
            echo '<div class="card-product-img">';
            echo '<img src="img/oferta1.jpg" alt="Imagen por defecto">'; // Cambia la ruta de la imagen si es necesario
            echo '</div>';
            echo '<div class="card-product-body">';
            echo '<h2 class="card-product-title">' . $producto["nombre"] . '</h2>';
            echo '<p class="card-product-price">Precio: $' . $producto["precio"] . '</p>';
            echo '<p class="card-product-description">' . $producto["descripción"] . '</p>';
            echo '<p class="card-product-existencias">Existencias: ' . $producto["cantidad_disponible"] . '</p>';

            // Sección de cantidad
            echo '<div class="producto-cantidad">';
            echo '<button class="cantidad-btn restar">-</button>';
            echo '<input type="number" value="1" min="1" class="cantidad-input" data-producto-id="' . $producto["id"] . '" data-disponible="' . $producto["cantidad_disponible"] . '">';
            echo '<button class="cantidad-btn sumar">+</button>';
            echo '</div>';

            // Botones de cancelar y actualizar ocultos
            echo '<div class="opciones-cantidad" style="display:none;">';
            echo '<button class="btn-cancelar-cambio"><img src="img/cancelarcarrito.png" alt="Cancelar" style="width: 20px; height: 20px;"></button>';
            echo '<button class="btn-actualizar-cambio"><img src="img/actualizarcarrito.png" alt="Actualizar" style="width: 20px; height: 20px;"></button>';
            echo '</div>';

                // Botón de agregar al carrito o eliminar del carrito
                //echo '<button class="agregar-carrito-btn" onclick="handleAddToCart(event, this)" '. ($producto["cantidad_disponible"] <= 0 ? ' disabled' : '') .' data-producto-id="' . htmlspecialchars($producto["id"]) . '">';
                //echo '<img src="img/agregarcarrito.png" alt="Añadir al carrito" style="width: 25px; height: 25px;"></button>';
                if (usuarioLogueado()) {
                    // Botón para agregar o eliminar del carrito
                    echo '<button class="agregar-carrito-btn" onclick="handleAddToCart(event, this)" '. ($producto["cantidad_disponible"] <= 0 ? ' disabled' : '') .' data-producto-id="' . htmlspecialchars($producto["id"]) . '">';
                    echo '<img src="img/agregarcarrito.png" alt="Añadir al carrito" style="width: 25px; height: 25px;"></button>';
                } else {
                    // Si no está logueado, mostramos el botón para redirigir al formulario de login
                   // echo '<button class="btn btn-primary" onclick="showLoginForm()">Inicia sesión</button>';
                    echo '<button class="agregar-carrito-btn" onclick="showLoginForm()" '. ($producto["cantidad_disponible"] <= 0 ? ' disabled' : '') .' data-producto-id="' . htmlspecialchars($producto["id"]) . '">';
                    echo '<img src="img/agregarcarrito.png" alt="Añadir al carrito" style="width: 25px; height: 25px;"></button>';
                }
                echo '</div>'; // Cerrar el cuerpo de la tarjeta
                echo '</div>'; // Cerrar la tarjeta del producto
            }

            echo '</div>'; // Cerrar el contenedor de productos
        } else {
            echo '<p>No hay productos disponibles en esta categoría.</p>';
        }

        // Cerrar la sentencia
        $stmt->close();
    } else {
        echo '<p>Error en la consulta: ' . $conexion->error . '</p>';
    }
} else {
    echo '<p>Categoría no válida.</p>';
}

// Cerrar la conexión
$conexion->close();
?>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="scroll.js"></script>
<script src="validacionesformularios.js"></script>
<script src="validacionesproductos.js"></script>
<script>
function handleAddToCart(event, button) {
    const img = button.querySelector('img');
    const productId = button.getAttribute('data-producto-id');
    const input = button.parentNode.querySelector('.cantidad-input');
    const cantidad = parseInt(input.value);

    // Aquí podrías verificar si el usuario está logueado desde el frontend
    const isLoggedIn = <?php echo json_encode(usuarioLogueado()); ?>; // Convertir el valor de PHP a JS

    if (!isLoggedIn) {
        // Si no está logueado, mostrar el formulario de inicio de sesión
        showLoginForm();
        return;
    }

    // Determinar la acción (agregar o eliminar del carrito)
    const action = img.src.includes('agregarcarrito.png') ? 'add' : 'remove';

    // Hacer una solicitud AJAX
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
            } else {
                alert('Error: ' + response.error);
            }
        } else {
            alert('Error al procesar la solicitud.');
        }
    };

    // Enviar solo producto_id y cantidad al servidor
    xhr.send(`producto_id=${productId}&cantidad=${cantidad}&action=${action}`);
}
</script>

</body>
</html>