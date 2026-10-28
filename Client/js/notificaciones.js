const listaNotificaciones = document.getElementById("listaNotificaciones");
const filtroEstado = document.getElementById("filtroEstado");
const totalNotificaciones = document.getElementById("totalNotificaciones");
const mensajeNotificaciones = document.getElementById("mensajeNotificaciones");
let notificaciones = [];

document.addEventListener("DOMContentLoaded", cargarNotificaciones);
filtroEstado.addEventListener("change", renderizarNotificaciones);

async function cargarNotificaciones() {
  try {
    const usuario = await auth.requiereSesion();

    if (!usuario) {
      return;
    }

    document.getElementById("nombrePerfil").textContent =
      `${usuario.nombre} ${usuario.apellido}`.trim();
    document.getElementById("rolPerfil").textContent =
      usuario.role === "ADMIN" ? "Administrador/a" : "Conserje";

    if (!auth.esAdministrador(usuario)) {
      mostrarMensaje("Esta sección está disponible únicamente para administradores.", true);
      totalNotificaciones.textContent = "Acceso restringido";
      listaNotificaciones.replaceChildren();
      return;
    }

    const usuarios = await api.usuarios.obtenerTodos();
    if (!Array.isArray(usuarios)) {
      throw new Error("La respuesta del servidor no contiene una lista de usuarios.");
    }

    const notificacionesPorUsuario = await Promise.all(
      usuarios.map(async (creador) => {
        const lista = await api.notificaciones.obtenerPorUsuario(creador.id_usuario);
        if (!Array.isArray(lista)) {
          throw new Error(`No se pudieron cargar las notificaciones del usuario ${creador.id_usuario}.`);
        }

        return lista.map((notificacion) => ({
          ...notificacion,
          usuarioNombre: `${creador.nombre} ${creador.apellido}`.trim()
        }));
      })
    );
    notificaciones = notificacionesPorUsuario.flat();
    notificaciones.sort((a, b) => obtenerFechaOrden(b.fecha_creacion) - obtenerFechaOrden(a.fecha_creacion));

    renderizarNotificaciones();
  } catch (error) {
    console.error("Error al cargar las notificaciones:", error);
    mostrarMensaje(error.message || "No se pudieron cargar las notificaciones.", true);
    totalNotificaciones.textContent = "No disponible";
    listaNotificaciones.replaceChildren();
  }
}

function renderizarNotificaciones() {
  const estadoFiltro = filtroEstado.value;
  const visibles = estadoFiltro === "TODAS"
    ? notificaciones
    : notificaciones.filter((notificacion) => normalizarEstado(notificacion.estado) === estadoFiltro);

  totalNotificaciones.textContent = estadoFiltro === "TODAS"
    ? `${notificaciones.length} ${notificaciones.length === 1 ? "notificación" : "notificaciones"}`
    : `${visibles.length} de ${notificaciones.length} notificaciones`;

  listaNotificaciones.replaceChildren();

  if (visibles.length === 0) {
    const vacio = document.createElement("p");
    vacio.className = "sin-notificaciones";
    vacio.textContent = notificaciones.length === 0
      ? "No hay notificaciones registradas."
      : "No hay notificaciones con este estado.";
    listaNotificaciones.appendChild(vacio);
    return;
  }

  visibles.forEach((notificacion) => {
    listaNotificaciones.appendChild(crearTarjeta(notificacion));
  });
}

function crearTarjeta(notificacion) {
  const estado = normalizarEstado(notificacion.estado);
  const tarjeta = document.createElement("article");
  tarjeta.className = `tarjeta-notificacion estado-borde-${claseEstado(estado)}`;

  const encabezado = document.createElement("div");
  encabezado.className = "titulo-notificacion";

  const titulo = document.createElement("div");
  titulo.className = "identificacion-notificacion";

  const identificador = document.createElement("span");
  identificador.className = "id-notificacion";
  identificador.textContent = `Notificación #${notificacion.id_notificacion}`;

  const tituloTexto = document.createElement("h2");
  tituloTexto.textContent = notificacion.titulo || "Sin título";
  titulo.append(identificador, tituloTexto);

  const controlEstado = document.createElement("div");
  controlEstado.className = "control-estado-notificacion";

  const etiquetaEstado = document.createElement("label");
  etiquetaEstado.textContent = "Estado";
  etiquetaEstado.htmlFor = `estado-${notificacion.id_notificacion}`;

  const selectorEstado = document.createElement("select");
  selectorEstado.id = `estado-${notificacion.id_notificacion}`;
  selectorEstado.className = `estado-notificacion estado-${claseEstado(estado)}`;
  selectorEstado.setAttribute("aria-label", `Estado de la notificación ${notificacion.id_notificacion}`);

  [
    ["PENDIENTE", "Pendiente"],
    ["EN_PROCESO", "En proceso"],
    ["REALIZADA", "Realizada"]
  ].forEach(([valor, texto]) => {
    const opcion = document.createElement("option");
    opcion.value = valor;
    opcion.textContent = texto;
    selectorEstado.appendChild(opcion);
  });
  selectorEstado.value = estado;
  selectorEstado.addEventListener("change", () => cambiarEstado(notificacion, selectorEstado, estado));

  controlEstado.append(etiquetaEstado, selectorEstado);
  encabezado.append(titulo, controlEstado);

  const contenido = document.createElement("div");
  contenido.className = "contenido-notificacion";

  const descripcion = document.createElement("p");
  descripcion.textContent = notificacion.descripcion || "Sin descripción.";

  const metadatos = document.createElement("div");
  metadatos.className = "metadatos-notificacion";

  const fecha = crearFecha(notificacion.fecha_creacion);
  const creador = document.createElement("span");
  creador.textContent = `Creada por ${notificacion.usuarioNombre || `Usuario #${notificacion.id_user}`}`;

  metadatos.append(fecha, creador);
  contenido.append(descripcion, metadatos);
  tarjeta.append(encabezado, contenido);

  return tarjeta;
}

async function cambiarEstado(notificacion, selector, estadoAnterior) {
  const nuevoEstado = selector.value;
  selector.disabled = true;
  ocultarMensaje();

  try {
    await api.notificaciones.actualizar({
      id: Number(notificacion.id_notificacion),
      titulo: notificacion.titulo,
      descripcion: notificacion.descripcion,
      fecha_creacion: obtenerFechaParaAPI(notificacion.fecha_creacion),
      estado: nuevoEstado,
      id_user: Number(notificacion.id_user)
    });

    notificacion.estado = nuevoEstado;
    renderizarNotificaciones();
    mostrarMensaje("Estado de la notificación actualizado correctamente.");
  } catch (error) {
    selector.value = estadoAnterior;
    selector.className = `estado-notificacion estado-${claseEstado(estadoAnterior)}`;
    selector.disabled = false;
    console.error("Error al actualizar el estado de la notificación:", error);
    mostrarMensaje(error.message || "No se pudo actualizar el estado.", true);
  }
}

function crearFecha(valor) {
  const time = document.createElement("time");
  const fecha = new Date(obtenerFechaParaAPI(valor).replace(" ", "T"));

  if (fecha && !Number.isNaN(fecha.getTime())) {
    time.dateTime = fecha.toISOString();
    time.textContent = `Creada el ${new Intl.DateTimeFormat("es-ES", {
      dateStyle: "medium",
      timeStyle: "short"
    }).format(fecha)}`;
  } else {
    time.textContent = `Fecha: ${valor || "no disponible"}`;
  }

  return time;
}

function obtenerFechaParaAPI(valor) {
  if (valor && typeof valor === "object" && valor.date) {
    return valor.date;
  }
  return typeof valor === "string" ? valor : "";
}

function obtenerFechaOrden(valor) {
  const fecha = new Date(obtenerFechaParaAPI(valor).replace(" ", "T"));
  return Number.isNaN(fecha.getTime()) ? 0 : fecha.getTime();
}

function normalizarEstado(estado) {
  const valor = typeof estado === "string" ? estado : estado?.name;
  return ["PENDIENTE", "EN_PROCESO", "REALIZADA"].includes(valor) ? valor : "PENDIENTE";
}

function claseEstado(estado) {
  return estado.toLowerCase().replace("_", "-");
}

function mostrarMensaje(texto, esError = false) {
  mensajeNotificaciones.textContent = texto;
  mensajeNotificaciones.classList.toggle("mensaje-error", esError);
  mensajeNotificaciones.hidden = false;
  mensajeNotificaciones.setAttribute("role", esError ? "alert" : "status");
}

function ocultarMensaje() {
  mensajeNotificaciones.hidden = true;
  mensajeNotificaciones.textContent = "";
  mensajeNotificaciones.classList.remove("mensaje-error");
}
