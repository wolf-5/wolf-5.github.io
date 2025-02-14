async function runJailbreak() {
    // Hide jailbreak button and show console
    document.getElementById("run-jb-parent").style.opacity = "0";
    document.getElementById("console-parent").style.opacity = "1";

 // Mostrar el mensaje "★ Activating Xploit ..."
    let consoleElement = document.getElementById("console");
    consoleElement.innerHTML = "★ Activating Xploit ...";

    // Esperar 5 segundos y luego borrar el contenido
    setTimeout(function() {
        consoleElement.innerHTML = ""; // Borra el mensaje
    }, 5000); // 5 segundos de espera

   setTimeout(async () => {
    let wk_exploit_type = localStorage.getItem("wk_exploit_type");
    if (wk_exploit_type == "psfree") {
        debug_log("[ PSFree - Step 1]");
        await run_psfree();  // Ejecuta PSFree después de 2 segundos
    } else if (wk_exploit_type == "fontface") {
        await run_fontface();
    }
}, 2000); // Espera 2 segundos antes de iniciar el PSFree
}

// Ejecuta automáticamente el Jailbreak al cargar la página
window.onload = async function() {
    await runJailbreak();
};

function onload_setup() {
    if (document.documentElement.hasAttribute("manifest")) {
        add_cache_event_toasts();
    }

    document.documentElement.style.overflowX = 'hidden';

    if (localStorage.getItem("wk_exploit_type") == null) {
        localStorage.setItem("wk_exploit_type", "psfree");
    }
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showToast(message, timeout = 2000) {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;

    toastContainer.appendChild(toast);

    // Trigger reflow and enable animation
    toast.offsetHeight;

    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.add('hide');
        toast.addEventListener('transitionend', () => {
            toast.remove();
        });
    }, timeout);
}
