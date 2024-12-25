// Función para mostrar un popup
function mostrarPopup() {
    const url = prompt("Please enter the URL:", "https://www.google.com/");

    if (url) {
        // Abre la URL en una nueva pestaña o ventana
        window.open(url, '_blank');
    }
}

// Función para manejar el evento de pulsación de tecla
function manejarKeyPress(event) {
    // Verifica si la tecla presionada es la tecla "v" (código 118)
    if (event.keyCode === 118) {
        mostrarPopup();
    }
}

// Registra un escuchador de eventos para keydown
document.addEventListener("keydown", manejarKeyPress);


