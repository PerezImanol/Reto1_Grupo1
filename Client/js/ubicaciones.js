//Variables de objetos HTML
const nameField = document.getElementById("name");
const descField = document.getElementById("desc");
const btnAdd = document.getElementById("btnAdd");
const btnSave = document.getElementById("btnSave");
const btnCancel = document.getElementById("btnCancel");
const selectedLocationTable = document.getElementById("select");
const tbody = document.getElementById("tableLocations");
const message = document.getElementById("message");

//Variables de uso general
let add = false;
let ALL_LOCATIONS = [];
let selectedLocation = null;

document.addEventListener("DOMContentLoaded", async () => {
  //Carga ubicaciones y deshabilita los botones de editar y los campos
  await loadLocactions();

  nameField.disabled = true;
  descField.disabled = true;
  btnSave.disabled = true;
  btnCancel.disabled = true;
});

async function getAllLocations() {
  //Llama al metodo para obtener las ubicaciones y las pasa, o error.
  const data = await api.ubicaciones.obtenerTodos();

  if (data.exito) {
    return data.result || [];
  }

  //Propagacion de error para manejar despues.
  throw new Error(data.mensaje);
}

async function loadLocactions() {
  //Recoge las ubicaciones o el error.
  try {
    ALL_LOCATIONS = await getAllLocations();
  } catch (e) {
    showMessage("Error obteniendo ubicaciones: " + e, true);
    ALL_LOCATIONS = [];
  }

  //Llama al siguiente metodo.
  renderLocations(ALL_LOCATIONS);
}

//Metodo para cargar las ubicaciones en la tabla.
function renderLocations(locations) {
  const showLocations = document.getElementById("show");
  let count = 0;

  //Por si alguna manera falla de cargar el Table Body
  if (!tbody) return;

  //Vacia el body de la tabla
  tbody.innerHTML = "";

  //Si falla la carga o no hay nada.
  if (!Array.isArray(locations) || locations.length === 0) {
    tbody.innerHTML = `
    <tr>
      <td class="emptyRow" colspan="3">No hay ubicaciones disponibles</td>
    </tr>
    `;

    if (showLocations) {
      showLocations.textContent = "Mostrando 0 de 0 ubicaciones";
    }
    if (selectedLocationTable) {
      selectedLocationTable.textContent = "Selecciona una ubicacion";
    }
    return;
  }

  //ForEach para la creacion de la tabla entera.
  locations.forEach((location) => {
    const row = document.createElement("tr");
    const cbTd = document.createElement("td");
    const nameTd = document.createElement("td");
    const descTd = document.createElement("td");
    const deleteTd = document.createElement("td");
    const btnDelete = document.createElement("button");

    //Checkbox para seleccion
    const checkBox = document.createElement("input");
    checkBox.type = "checkbox";
    checkBox.className = "checkbox";
    cbTd.addEventListener("change", () => {
      if (checkBox.checked) {
        seleccionarFila(checkBox, location);
      } else {
        deseleccionarFila();
      }
    });

    cbTd.appendChild(checkBox);

    //Info de tabla
    const { id_ubicacion, nombre, descripcion } = location;
    nameTd.textContent = nombre ?? "";
    descTd.textContent = descripcion ?? "";

    //Setup boton borrar
    btnDelete.className = "btnDelete";
    btnDelete.onclick = async function () {
      hideMessage();

      if (!confirm("¿Estás seguro de que deseas eliminar esta ubicacion?")) {
        return;
      }

      //Intento de eliminar
      try {
        let data = await api.ubicaciones.eliminar(id_ubicacion);

        if (data.exito) {
          await loadLocactions();
          showMessage("Ubicacion eliminada correctamente");
          deseleccionarFila();
        } else {
          showMessage(data.mensaje || "Error al eliminar la ubicacion", true);
        }
      } catch (e) {
        showMessage("Error de servidor: " + e, true);
      }
    };

    //Preparacion de imagen
    const trash = document.createElement("img");
    trash.src = "../img/trash.svg";
    trash.className = "svg";

    const trashSpan = document.createElement("span");
    trashSpan.textContent = "Borrar";

    btnDelete.appendChild(trash);
    btnDelete.appendChild(trashSpan);
    deleteTd.appendChild(btnDelete);

    row.appendChild(cbTd);
    row.appendChild(nameTd);
    row.appendChild(descTd);
    deleteTd.className = "right";
    row.appendChild(deleteTd);

    //Event Listener de click de fila
    row.addEventListener("click", () => {
      selectedLocation = location;

      if (selectedLocationTable) {
        selectedLocationTable.textContent = nombre + " seleccionado";
      }

      checkBox.checked = true;
      seleccionarFila(checkBox, location);
    });

    tbody.appendChild(row);
    count++;
  });

  //Para mostrar cuenta
  if (showLocations) {
    showLocations.textContent =
      "Mostrando " + count + " de " + count + " ubicaciones";
  }
}

//Comprobacion de nombre
nameField.addEventListener("change", () => {
  if (!nameField.value || !descField.value) {
    btnSave.disabled = true;
  } else {
    btnSave.disabled = false;
    nameField.value.trim();
    descField.value.trim();
  }
});

//Comprobacion de descripcion
descField.addEventListener("change", () => {
  if (!nameField.value || !descField.value) {
    btnSave.disabled = true;
  } else {
    btnSave.disabled = false;
    nameField.value.trim();
    descField.value.trim();
  }
});

//Acceso a añadir ubicacion, vacia campos por si hay algo
btnAdd.addEventListener("click", () => {
  hideMessage();
  add = true;

  btnSave.disabled = false;
  btnCancel.disabled = false;

  nameField.value = "";
  nameField.disabled = false;

  descField.value = "";
  descField.disabled = false;

  //No es tan obvio? Igual se puede cambiar
  if (selectedLocationTable) {
    selectedLocationTable.textContent = "Añadiendo ubicacion";
  }
});

//Metodo para añadir or modificar
btnSave.addEventListener("click", async (e) => {
  e.preventDefault();
  hideMessage();

  //Por si se modifica sin nada, hace nada al añadir
  if (!selectedLocation && !add) {
    showMessage("Por favor, selecciona una ubicacion para editar.", true);
    return;
  }

  if (add) {
    //Validacion de campos por si acaso.
    if (!nameField.value || !descField.value) {
      showMessage("Por favor, rellena los campos.", true);
    } else {
      //Preparacion de info
      let newInfo = {
        nombre: nameField.value,
        descripcion: descField.value,
      };

      //Por un constraint en la base de datos, validacion de nombre.
      for (let i = 0; i < ALL_LOCATIONS.length; i++) {
        const location = ALL_LOCATIONS[i];

        if (location.nombre == newInfo.nombre) {
          showMessage(
            "No se puede usar el mismo nombre para dos ubicaciones.",
            true,
          );
          return;
        }
      }

      //Intento de creacion
      try {
        const data = await api.ubicaciones.crear(newInfo);

        if (data.exito) {
          await loadLocactions();
          showMessage(data.mensaje);
          deseleccionarFila();
        } else {
          showMessage(data.mensaje, true);
        }
      } catch (e) {
        showMessage("Error de servidor: " + e, true);
      }
    }
  } else {
    if (!nameField.value || !descField.value) {
      showMessage("Por favor, rellena los campos.", true);
    } else {
      //Validacion de campos por si acaso.
      let newInfo = {
        id_ubicacion: selectedLocation.id_ubicacion,
        nombre: nameField.value,
        descripcion: descField.value,
      };

      //Por un constraint en la base de datos, validacion de nombre.
      for (let i = 0; i < ALL_LOCATIONS.length; i++) {
        const location = ALL_LOCATIONS[i];

        if (location.nombre == newInfo.nombre) {
          showMessage(
            "No se puede usar el mismo nombre para dos ubicaciones.",
            true,
          );
          return;
        }
      }

      //Intento de actualizacion
      try {
        const data = await api.ubicaciones.actualizar(newInfo);

        if (data.exito) {
          await loadLocactions();
          showMessage(data.mensaje);
          deseleccionarFila();
        } else {
          showMessage(data.mensaje, true);
        }
      } catch (e) {
        showMessage("Error de servidor: " + e, true);
      }
    }
  }
});

//Literalmente solo existe para llamar el metodo de deseleccionar
btnCancel.addEventListener("click", () => {
  deseleccionarFila();
});

//Metodo para seleccionar una fila
function seleccionarFila(selectedCb, location) {
  hideMessage();

  //Debido a que es para modificar...
  add = false;

  //Deseleccion del resto de checkboxes
  tbody.querySelectorAll(".checkbox").forEach((cb) => {
    if (cb !== selectedCb) {
      cb.checked = false;
    }
  });

  //Habilita todo y te muestra la informacion en los campos
  selectedLocation = location;
  btnSave.disabled = false;
  btnCancel.disabled = false;
  nameField.disabled = false;
  descField.disabled = false;

  nameField.value = location.nombre;
  descField.value = location.descripcion;
}

//Metodo para deseleccionar la fila seleccionada
function deseleccionarFila() {
  tbody.querySelectorAll(".checkbox").forEach((cb) => {
    cb.checked = false;
  });

  selectedLocation = null;
  if (selectedLocationTable) {
    selectedLocationTable.textContent = "Selecciona una ubicacion";
  }

  //Deshabilita todo lo necesario y vacia los campos
  btnSave.disabled = true;
  btnCancel.disabled = true;

  nameField.value = "";
  descField.value = "";
  nameField.disabled = true;
  descField.disabled = true;
}

//Metodo para mostrar mensajes
function showMessage(texto, esError = false) {
  message.textContent = texto;
  message.classList.toggle("error", esError);
  message.hidden = false;
}

//Metodo para esconder el mensaje activo
function hideMessage() {
  message.hidden = true;
  message.textContent = "";
  message.classList.remove("error");
}
