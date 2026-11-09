const formulario = document.getElementById("loginForm");
const mensaje = document.getElementById("loginMessage");
const boton = document.getElementById("loginButton");

formulario.addEventListener("submit", async function (event) {
    event.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value;

    mensaje.textContent = "";
    boton.disabled = true;

    try {
        const response = await auth.login(username, password);

        if (!response.usuario) {
            throw new Error("No se han podido obtener los datos del usuario.");
        }

        const user = response.usuario;

        if (user.role === "ADMIN") {
            window.location.href = "../pages/equipamientos.html";
        } else if (user.role === "USER") {
            window.location.href = "../pagesUSER/equipamientosMovil.html";
        } else {
            mensaje.textContent = "El usuario no tiene un rol válido.";
        }

    } catch (error) {
        mensaje.textContent = error.message;
    } finally {
        boton.disabled = false;
    }
});