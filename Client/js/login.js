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
    if (response.usuario) {
      const user = response.usuario;
      localStorage.setItem("user_id", JSON.stringify(user.id_usuario));
      window.location.href = "../pages/equipamientos.html";
    }
  } catch (error) {
    mensaje.textContent = error.message;
  } finally {
    boton.disabled = false;
  }
});
