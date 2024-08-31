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
                    <img src="img/login.png" alt="User Icon" width="40" height="40" class="me-2">
                    <?php if (isset($_SESSION['usuario_nombre'])): ?>
                        <span>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></span>
                        <a href="logout.php" class="btn btn-danger ms-3">Cerrar sesión</a>
                    <?php else: ?>
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

    <!--  aqui inicia el Contenedor del formulario de inicio de sesión -->
    <div class="login-overlay" id="loginFormContainer" style="display: none;">
            <div class="login-form-container">
            <button class="close-btn" id="closeBtn">&times;</button>
                <h2>Iniciar Sesión</h2>
                <form action="procesar_login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </form>
            </div>
        </div>
         <!-- aqui termina el Contenedor del formulario de inicio de sesión -->
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
        });



    </script>
</body>
</html>
