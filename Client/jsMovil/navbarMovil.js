document.addEventListener("DOMContentLoaded", async function () {

    const contenedor = document.getElementById("navbar-container");

    if (!contenedor) {
        return;
    }

    try {
        const respuesta = await fetch("navbarMovil.html");

        if (!respuesta.ok) {
            throw new Error("No se ha podido cargar el menú.");
        }

        contenedor.innerHTML = await respuesta.text();

        // Marcar el apartado de la página actual.
        const paginaActual = window.location.pathname.split("/").pop();

        document.querySelectorAll(".enlace-movil").forEach(enlace => {
            if (enlace.dataset.pagina === paginaActual) {
                enlace.classList.add("activo");
            }
        });

    } catch (error) {
        console.error("Error al cargar el navbar:", error);
    }
});