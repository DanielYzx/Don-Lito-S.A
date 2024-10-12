// Inicializar la cantidad guardada
let cantidadAnterior = {};

// Mostrar los botones de "Cancelar" y "Actualizar" al cambiar la cantidad manualmente o con los botones
document.querySelectorAll('.cantidad-input').forEach(function(input) {
    input.addEventListener('input', function() {
        const productoId = input.getAttribute('data-producto-id');

        // Validar que el valor no sea menor que 1
        if (parseInt(input.value) < 1) {
            input.value = 1; // Establece el valor a 1 si es menor que 1
        }

        if (!cantidadAnterior[productoId]) {
            cantidadAnterior[productoId] = input.value;
        }

        const opciones = this.parentNode.nextElementSibling;
        opciones.style.display = "flex";

        // Deshabilitar el botón de agregar/eliminar
        const botonCarrito = this.parentNode.nextElementSibling.nextElementSibling;
        botonCarrito.disabled = true;
        botonCarrito.classList.add('btn-deshabilitado');
    });
});

// Mostrar los botones de "Cancelar" y "Actualizar" al hacer click en los botones de sumar/restar
document.querySelectorAll('.cantidad-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        const input = this.parentNode.querySelector('.cantidad-input');
        let value = parseInt(input.value);
        const productoId = input.getAttribute('data-producto-id');

        if (!cantidadAnterior[productoId]) {
            cantidadAnterior[productoId] = value;
        }

        const opciones = this.parentNode.nextElementSibling;
        opciones.style.display = "flex";

        // Deshabilitar el botón de agregar/eliminar
        const botonCarrito = this.parentNode.nextElementSibling.nextElementSibling;
        botonCarrito.disabled = true;
        botonCarrito.classList.add('btn-deshabilitado');

        if (this.classList.contains('restar')) {
            if (value > 1) {
                input.value = value - 1;
            }
        } else if (this.classList.contains('sumar')) {
            input.value = value + 1;
        }
    });
});

// Funcionalidad para Cancelar o Actualizar la cantidad
document.querySelectorAll('.btn-cancelar-cambio').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const input = this.parentNode.previousElementSibling.querySelector('.cantidad-input');
        const productoId = input.getAttribute('data-producto-id'); 

        input.value = cantidadAnterior[productoId] || 1; 
        this.parentNode.style.display = 'none';

        // Rehabilitar el botón de agregar/eliminar
        const botonCarrito = this.parentNode.nextElementSibling;
        botonCarrito.disabled = false;
        botonCarrito.classList.remove('btn-deshabilitado');
    });
});

document.querySelectorAll('.btn-actualizar-cambio').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const input = this.parentNode.previousElementSibling.querySelector('.cantidad-input');
        const cantidad = parseInt(input.value);
        const cantidadDisponible = parseInt(input.getAttribute('data-disponible')); 
        const productoId = input.getAttribute('data-producto-id'); 

        // Validar si la cantidad ingresada es mayor que la disponible
        if (cantidad > cantidadDisponible) {
            alert(`Lo sentimos no se puede actualizar la cantidad porque no hay suficientes productos en existencia. Solo hay ${cantidadDisponible} disponibles.`);
            input.value = cantidadDisponible; // Actualiza el input a la cantidad disponible
            return; // Salir de la función
        }

        cantidadAnterior[productoId] = cantidad; // Guardar la nueva cantidad
        this.parentNode.style.display = 'none'; // Ocultar opciones

        // Rehabilitar el botón de agregar/eliminar
        const botonCarrito = this.parentNode.nextElementSibling;
        botonCarrito.disabled = false;
        botonCarrito.classList.remove('btn-deshabilitado');
    });
});









