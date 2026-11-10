const tabla = document.getElementById("tablaHistorial");
const filtroEquipamiento = document.getElementById("filtroEquipamiento");
const mensajeHistorial = document.getElementById("mensajeHistorial");

let listaHistoriales = [];

// Obtener el ID del equipamiento recibido por la URL.
const parametrosURL = new URLSearchParams(window.location.search);
const idEquipamientoURL = parametrosURL.get("id");

/******************************** CARGAR HISTORIALES ********************************/

document.addEventListener("DOMContentLoaded", iniciarPagina);

async function iniciarPagina() {
  try {
    // Cargar los equipamientos para rellenar el selector.
    const equipamientos = await api.equipamientos.obtenerTodos();

    equipamientos.forEach((equipamiento) => {
      const opcion = document.createElement("option");

      opcion.value = equipamiento.id_equipamiento;
      opcion.textContent = equipamiento.nombre;

      filtroEquipamiento.appendChild(opcion);
    });

    // Comprobar si venimos desde la página de equipamientos.
    if (idEquipamientoURL !== null) {
      if (!/^\d+$/.test(idEquipamientoURL) || Number(idEquipamientoURL) <= 0) {
        throw new Error("El ID del equipamiento no es válido.");
      }

      const equipamientoExiste = equipamientos.some(
        (equipamiento) =>
          String(equipamiento.id_equipamiento) === idEquipamientoURL,
      );

      if (!equipamientoExiste) {
        throw new Error("No se ha encontrado el equipamiento seleccionado.");
      }

      // Seleccionar automáticamente el equipamiento recibido.
      filtroEquipamiento.value = idEquipamientoURL;

      await cargarHistoriales(idEquipamientoURL);
    } else {
      // Si entramos desde el menú, mostrar todos los historiales.
      await cargarHistoriales();
    }
  } catch (error) {
    console.error("Error al iniciar el historial:", error);
    mostrarMensaje(error.message, true);
    mostrarTablaVacia("No se pudieron cargar los historiales.");
  }
}

/******************************** CARGAR HISTORIALES ********************************/

async function cargarHistoriales(idEquipamiento = null) {
  try {
    tabla.innerHTML = `
            <tr>
                <td colspan="5">Cargando historiales...</td>
            </tr>
        `;

    ocultarMensaje();

    if (idEquipamiento) {
      const data = await api.historico.obtenerPorEquipamiento(idEquipamiento);
      listaHistoriales = data.result;
    } else {
      const data = await api.historico.obtenerTodos();
      listaHistoriales = data.result;
    }

    mostrarHistoriales();
  } catch (error) {
    console.error("Error al cargar los historiales:", error);
    mostrarMensaje("Error al cargar los historiales: " + error.message, true);
    mostrarTablaVacia("No se pudieron cargar los historiales.");
  }
}

/******************************** MOSTRAR HISTORIALES ********************************/

function mostrarHistoriales() {
  tabla.innerHTML = "";

  if (!Array.isArray(listaHistoriales) || listaHistoriales.length === 0) {
    mostrarTablaVacia("No hay historiales de ubicación registrados.");
    return;
  }

  listaHistoriales.forEach((historico) => {
    const fila = document.createElement("tr");

    const datos = [
      historico.id_historico,
      historico.nombre_equipamiento,
      historico.nombre_ubicacion,
      formatearFecha(historico.fecha_inicio),
      historico.fecha_fin ? formatearFecha(historico.fecha_fin) : "Actualmente",
    ];

    datos.forEach((dato) => {
      const celda = document.createElement("td");
      celda.textContent = dato ?? "-";
      fila.appendChild(celda);
    });

    tabla.appendChild(fila);
  });
}

/******************************** FORMATEAR FECHAS ********************************/

function formatearFecha(fecha) {
  if (!fecha) {
    return "-";
  }

  const fechaConvertida = new Date(fecha.replace(" ", "T"));

  if (isNaN(fechaConvertida.getTime())) {
    return fecha;
  }

  return fechaConvertida.toLocaleString("es-ES", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

/******************************** FILTRAR POR EQUIPAMIENTO ********************************/

filtroEquipamiento.addEventListener("change", () => {
  const idEquipamiento = filtroEquipamiento.value;

  if (idEquipamiento === "") {
    cargarHistoriales();
  } else {
    cargarHistoriales(idEquipamiento);
  }
});

/******************************** MOSTRAR TABLA VACÍA ********************************/

function mostrarTablaVacia(texto) {
  tabla.innerHTML = `
        <tr>
            <td colspan="5" class="tabla-vacia">
                ${texto}
            </td>
        </tr>
    `;
}

/******************************** MOSTRAR MENSAJES ********************************/

function mostrarMensaje(texto, esError = false) {
  mensajeHistorial.textContent = texto;
  mensajeHistorial.hidden = false;
  mensajeHistorial.classList.toggle("mensaje-error", esError);
  mensajeHistorial.setAttribute("role", esError ? "alert" : "status");
}

function ocultarMensaje() {
  mensajeHistorial.hidden = true;
  mensajeHistorial.textContent = "";
  mensajeHistorial.classList.remove("mensaje-error");
}
