// Función para mostrar el formulario de inicio de sesión de empleados
function showLoginFormem() {
    document.getElementById('loginemFormContainer').style.display = 'flex';
}

// Función para cerrar el formulario y limpiar los campos
document.getElementById("closeBtnem").addEventListener("click", function() {
    document.getElementById("loginemFormContainer").style.display = "none";
    removeErrorParamEm();
    clearLoginFormFieldsEm();

    // Ocultar el mensaje de error si está visible
    const errorMsg = document.getElementById("errorMessageEm");
    if (errorMsg) {
        errorMsg.style.display = "none";
    }
});

// Función para remover el parámetro 'error' de la URL (empleados)
function removeErrorParamEm() {
    const url = new URL(window.location.href);
    url.searchParams.delete('error');
    window.history.replaceState({}, document.title, url); // Actualizar la URL sin recargar la página
}

// Función para limpiar los campos del formulario de inicio de sesión (empleados)
function clearLoginFormFieldsEm() {
    document.getElementById('emailEm').value = '';
    document.getElementById('passwordEm').value = '';
    document.getElementById('cargoEm').value = '';
}