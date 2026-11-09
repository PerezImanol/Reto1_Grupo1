
document.addEventListener("DOMContentLoaded", async () => {
    const nombre = document.getElementById("perfilNombre");
    const apellidos = document.getElementById("perfilApellidos");
    const usuario = document.getElementById("perfilUsuario");
    const rol = document.getElementById("perfilRol");
    const mensaje = document.getElementById("mensajePerfil");
    const btnCerrarSesion = document.getElementById("btnCerrarSesion");

    try {
        const sesion = await auth.obtenerSesion();

        if (!sesion || !sesion.usuario) {
            window.location.href = "../pages/login.html";
            return;
        }

        const datos = sesion.usuario;

        nombre.textContent = datos.nombre || "No disponible";
        apellidos.textContent = datos.apellido || datos.apellidos || "No disponible";
        usuario.textContent = datos.username || "No disponible";
        rol.textContent = datos.role || "USER";

    } catch (error) {
        mensaje.textContent = error.message;
        mensaje.classList.add("error");
    }

    btnCerrarSesion.addEventListener("click", async () => {
        try {
            await api.logout();
            window.location.href = "../pages/login.html";
        } catch (error) {
            mensaje.textContent = error.message;
            mensaje.classList.add("error");
        }
    });
});
