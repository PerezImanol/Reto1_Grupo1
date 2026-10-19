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
        await auth.login(username, password);

        window.location.href = "dashboard.html";
    } catch (error) {
        mensaje.textContent = error.message;
    } finally {
        boton.disabled = false;
    }
});
