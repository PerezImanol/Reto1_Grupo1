document.addEventListener("DOMContentLoaded", async () => {
  const lista = document.getElementById("listaUbicaciones");
  const mensaje = document.getElementById("mensajeUbicaciones");

  try {
    const sesion = await auth.obtenerSesion();

    if (!sesion || !sesion.usuario || !sesion.exito) {
      window.location.href = "../pages/login.html";
      return;
    }

    const respuesta = await peticionAPI("UbicacionAPI.php");

    // La API devuelve las ubicaciones dentro de "result".
    const ubicaciones = respuesta.result;

    lista.innerHTML = "";

    if (!Array.isArray(ubicaciones) || ubicaciones.length === 0) {
      lista.innerHTML = "<p>No hay ubicaciones registradas.</p>";
      return;
    }

    ubicaciones.forEach((ubicacion) => {
      const tarjeta = document.createElement("article");
      tarjeta.className = "tarjeta-movil";

      const nombre =
        ubicacion.nombre ?? ubicacion.nombre_ubicacion ?? "Sin nombre";
      const descripcion = ubicacion.descripcion ?? "Sin descripción";

      tarjeta.innerHTML = `
                <h2>${nombre}</h2>
                <p>${descripcion}</p>
            `;

      lista.appendChild(tarjeta);
    });
  } catch (error) {
    mensaje.textContent = error.message;
    mensaje.classList.add("error");
    lista.innerHTML = "";
  }
});
