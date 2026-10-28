window.navbarReady = new Promise((resolve, reject) => {
  $(function () {
    $("#navbar-placeholder").load("./navbar.html", function (_response, status, request) {
      if (status === "error") {
        reject(new Error(`No se pudo cargar el menú: ${request.status} ${request.statusText}`));
        return;
      }

      const paginaActual = window.location.pathname.split("/").pop();
      const enlaces = document.querySelectorAll(".nav-item");

      enlaces.forEach((enlace) => {
        const paginaEnlace = new URL(enlace.href, window.location.href).pathname.split("/").pop();
        if (paginaEnlace === paginaActual) {
          enlace.classList.add("activo");
          enlace.setAttribute("aria-current", "page");
        }
      });

      configurarVistaPrevia();
      cargarVistaPrevia();
      resolve();
    });
  });
});

function configurarVistaPrevia() {
  const boton = document.getElementById("btnNotificaciones");
  const menu = document.getElementById("menuNotificaciones");

  boton.addEventListener("click", async () => {
    const abrir = menu.hidden;
    menu.hidden = !abrir;
    boton.setAttribute("aria-expanded", String(abrir));

    if (abrir) {
      await cargarVistaPrevia();
    }
  });

  document.addEventListener("click", (evento) => {
    if (!evento.target.closest(".notificaciones")) {
      menu.hidden = true;
      boton.setAttribute("aria-expanded", "false");
    }
  });

  document.addEventListener("keydown", (evento) => {
    if (evento.key === "Escape" && !menu.hidden) {
      menu.hidden = true;
      boton.setAttribute("aria-expanded", "false");
      boton.focus();
    }
  });
}

async function cargarVistaPrevia() {
  const listaVistaPrevia = document.getElementById("vistaPreviaNotificaciones");
  const resumen = document.getElementById("resumenNotificaciones");
  const contador = document.getElementById("contadorNotificaciones");
  const menu = document.getElementById("menuNotificaciones");
  if (!menu.hidden) {
    listaVistaPrevia.replaceChildren(crearMensajePreview("Cargando notificaciones..."));
  }

  try {
    const sesion = await auth.obtenerSesion();
    const usuario = sesion?.usuario;
    if (!usuario) {
      resumen.textContent = "Sin sesión";
      actualizarContadorNotificaciones(0, contador);
      if (!menu.hidden) {
        listaVistaPrevia.replaceChildren(crearMensajePreview("Inicia sesión para consultar tus notificaciones."));
      }
      return;
    }

    actualizarPerfilNavbar(usuario);

    let notificaciones;
    let esAdmin = auth.esAdministrador(usuario);
    if (esAdmin) {
      const usuarios = await api.usuarios.obtenerTodos();
      if (!Array.isArray(usuarios)) {
        throw new Error("La respuesta del servidor no contiene una lista de usuarios.");
      }

      const listas = await Promise.all(
        usuarios.map((creador) => api.notificaciones.obtenerPorUsuario(creador.id_usuario))
      );
      if (!listas.every(Array.isArray)) {
        throw new Error("No se pudieron cargar todas las notificaciones.");
      }

      notificaciones = listas.flat().map((notificacion) => {
        const creador = usuarios.find((item) => Number(item.id_usuario) === Number(notificacion.id_user));
        return {
          ...notificacion,
          usuarioNombre: creador ? `${creador.nombre} ${creador.apellido}`.trim() : `Usuario #${notificacion.id_user}`
        };
      });
    } else {
      notificaciones = await api.notificaciones.obtenerPorUsuario(usuario.id_usuario);
    }

    if (!Array.isArray(notificaciones)) {
      throw new Error("La respuesta no contiene una lista de notificaciones.");
    }

    if (!esAdmin) {
      notificaciones = notificaciones.map((notificacion) => ({
        ...notificacion,
        usuarioNombre: "Tu notificación"
      }));
    }

    notificaciones = notificaciones.filter((notificacion) => {
      const estado = typeof notificacion.estado === "string"
        ? notificacion.estado
        : notificacion.estado?.name;
      return estado === "PENDIENTE";
    });

    notificaciones.sort((a, b) => obtenerFechaPreview(b.fecha_creacion) - obtenerFechaPreview(a.fecha_creacion));
    actualizarContadorNotificaciones(notificaciones.length, contador);
    resumen.textContent = `${notificaciones.length} ${notificaciones.length === 1 ? "notificación" : "notificaciones"}`;
    if (menu.hidden) {
      return;
    }
    listaVistaPrevia.replaceChildren();

    if (notificaciones.length === 0) {
      listaVistaPrevia.appendChild(crearMensajePreview("No hay notificaciones."));
      return;
    }

    notificaciones.slice(0, 5).forEach((notificacion) => {
      listaVistaPrevia.appendChild(crearElementoPreview(notificacion, esAdmin));
    });
  } catch (error) {
    console.error("Error al cargar la vista previa de notificaciones:", error);
    resumen.textContent = "No disponible";
    if (!menu.hidden) {
      listaVistaPrevia.replaceChildren(crearMensajePreview("No se pudieron cargar las notificaciones."));
    }
  }
}

function actualizarContadorNotificaciones(total, contador) {
  contador.textContent = String(total);
  const paginaActual = window.location.pathname.split("/").pop();
  contador.hidden = total === 0 || paginaActual === "notificaciones.html";
  document.getElementById("btnNotificaciones").setAttribute(
    "aria-label",
    total > 0
      ? `Abrir vista previa de notificaciones: ${total} ${total === 1 ? "notificación" : "notificaciones"}`
      : "Abrir vista previa de notificaciones"
  );
}

function crearElementoPreview(notificacion, esAdmin) {
  const elemento = document.createElement("li");
  elemento.className = "notificacion";

  const punto = document.createElement("span");
  punto.className = "punto-notificacion";
  punto.setAttribute("aria-hidden", "true");

  const contenido = document.createElement("div");
  contenido.className = "notificacion-contenido";

  const titulo = document.createElement("strong");
  titulo.textContent = notificacion.titulo || "Sin título";

  const descripcion = document.createElement("p");
  descripcion.textContent = notificacion.descripcion || "Sin descripción.";

  const fecha = document.createElement("time");
  const valorFecha = obtenerFechaTextoPreview(notificacion.fecha_creacion);
  const fechaParseada = new Date(valorFecha.replace(" ", "T"));
  fecha.textContent = Number.isNaN(fechaParseada.getTime())
    ? `Fecha: ${valorFecha || "no disponible"}`
    : new Intl.DateTimeFormat("es-ES", { dateStyle: "medium", timeStyle: "short" }).format(fechaParseada);

  const estado = document.createElement("span");
  estado.className = "notificacion-estado";
  estado.textContent = ` · ${formatearEstadoPreview(notificacion.estado)}`;

  contenido.append(titulo, descripcion, fecha, estado);
  if (esAdmin && notificacion.usuarioNombre) {
    const creador = document.createElement("p");
    creador.textContent = `Creada por ${notificacion.usuarioNombre}`;
    contenido.appendChild(creador);
  }
  elemento.append(punto, contenido);
  return elemento;
}

function crearMensajePreview(texto) {
  const mensaje = document.createElement("li");
  mensaje.className = "notificacion-vacia";
  mensaje.textContent = texto;
  return mensaje;
}

function actualizarPerfilNavbar(usuario) {
  const nombre = document.getElementById("nombrePerfil");
  const rol = document.getElementById("rolPerfil");
  nombre.textContent = `${usuario.nombre} ${usuario.apellido}`.trim();
  rol.textContent = usuario.role === "ADMIN" ? "Administrador/a" : "Conserje";
}

function obtenerFechaTextoPreview(fecha) {
  if (fecha && typeof fecha === "object" && fecha.date) {
    return fecha.date;
  }
  return typeof fecha === "string" ? fecha : "";
}

function obtenerFechaPreview(fecha) {
  const fechaParseada = new Date(obtenerFechaTextoPreview(fecha).replace(" ", "T"));
  return Number.isNaN(fechaParseada.getTime()) ? 0 : fechaParseada.getTime();
}

function formatearEstadoPreview(estado) {
  const valor = typeof estado === "string" ? estado : estado?.name;
  const nombres = {
    PENDIENTE: "Pendiente",
    EN_PROCESO: "En proceso",
    REALIZADA: "Realizada"
  };
  return nombres[valor] || "Sin estado";
}
