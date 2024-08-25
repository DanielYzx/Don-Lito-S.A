<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PantallaPrincipal</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <nav class="navbar navbar-light >
        <div class="container-fluid">
            <div class="row w-100 align-items-center">
                <div class="col-6 col-md-2">
                    <a class="navbar-brand" href="#">
                        <img src="img/logo.png" alt="Logo" width="80" height="80">
                    </a>
                </div>
                <div class="col-6 col-md-4 d-flex justify-content-end align-items-center order-md-2">
                    <img src="img/login.png" alt="User Icon" width="40" height="40" class="me-2">
                    <button type="button" class="btn btn-primary">Inicia sesión</button>
                    <div class="vertical-divider"></div>
                    <button type="button" width="40" height="40"  class="btn">
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
            <div class="row mt-3">
                <div class="col-12 nav-container">
                    <button class="nav-buttons" id="btnCategorias" >
                        <img src="img/menu.png" alt="menu">
                        Todas las categoría
                    </button>

                    <button class="nav-buttons">Productos frescos</button>
                    <button class="nav-buttons">Bebidas</button>
                    <button class="nav-buttons">Cuidado personal</button>
                    <button class="nav-buttons">Cuidado del hogar</button>
                    <button class="nav-buttons">Cuidado del bebé</button>
                    <button class="nav-buttons">Cuidado de la mascota</button>
                </div>
            </div>
        </div>
    </nav>
    <div class="main-content">
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
            <div class="col-auto">
                <a href="#" class="card btn text-center">
                    <div class="card-body">
                        <p class="card-text">Abarrotes</p>
                    </div>
                    <div class="card-img">
                        <img src="img/oferta1.jpg"  alt="Abarrotes">
                    </div>
                </a>
            </div>
            <div class="col-auto">
                <a href="#" class="card btn text-center">
                    <div class="card-body">
                        <p class="card-text">Carnes</p>
                    </div>
                    <div class="card-img">
                        <img src="img/banner2.jpg"  alt="Carnes">
                    </div>
                </a>
            </div>
            <div class="col-auto">
                <a href="#" class="card btn text-center">
                    <div class="card-body">
                        <p class="card-text">Frutas y Verduras</p>
                    </div>
                    <div class="card-img">
                        <img src="img/banner1.jpg"  alt="Frutasyverduras">
                    </div>
                </a>
            </div>
            <div class="col-auto">
                <a href="#" class="card btn text-center">
                    <div class="card-body">
                        <p class="card-text">Carnes</p>
                    </div>
                    <div class="card-img">
                        <img src="img/banner2.jpg"  alt="Carnes">
                    </div>
                </a>
            </div>
            <!-- Repite para más categorías -->
        </div>
    </div>
</div>
    <script src="js/bootstrap.bundle.min.js"></script>
     <!-- Contenedor para el iframe -->
     <iframe class="iframe-container" id="categoriasFrame" src="Categorias.html"></iframe>

     <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Asegúrate de que el iframe esté oculto al cargar la página
            var iframe = document.getElementById('categoriasFrame');
            iframe.classList.remove('show');
            
            // Lleva el scroll a la parte superior de la página
            window.scrollTo(0, 0);
        });

        document.getElementById('btnCategorias').addEventListener('click', function() {
            var iframe = document.getElementById('categoriasFrame');
            iframe.classList.toggle('show'); // Muestra/oculta el iframe
        });
    </script>
</body>
</html>    