const btnEquipamiento = document.getElementById("btnEquip");
const btnUbicacion = document.getElementById("btnUbicacion");
const btnPanel = document.getElementById("btnPanel");
const btnNotificaciones = document.getElementById("btnNotificaciones");
const btnPerfil = document.getElementById("btnPerfil");
const nameField = document.getElementById("name");
const descField = document.getElementById("desc");
const btnSave = document.getElementById("btnSave");
const selectedLocationTable = document.getElementById("select");

let ALL_LOCATIONS = [];
let selectedLocation = null;

document.addEventListener("DOMContentLoaded", async () => {
  loadLocactions();

  nameField.disabled = true;
  descField.disabled = true;
  btnSave.disabled = true;
});

nameField.addEventListener("change", () => {
  if (!nameField.value || !descField.value) {
    btnSave.disabled = true;
  } else {
    btnSave.disabled = false;
    nameField.value.trim();
  }
});

descField.addEventListener("change", () => {
  if (!nameField.value || !descField.value) {
    btnSave.disabled = true;
  } else {
    btnSave.disabled = false;
    descField.value.trim();
  }
});

btnSave.addEventListener("click", async (e) => {
  e.preventDefault();
  if (!selectedLocation) {
    alert("Por favor, selecciona una ubicacion para editar.");
    return;
  }

  if (!nameField.value || !descField.value) {
    alert("Por favor, rellena los campos.");
  } else {
    let newInfo = {
      id_ubicacion: selectedLocation.id_ubicacion,
      nombre: nameField.value,
      descripcion: descField.value,
    };

    try {
      const data = await api.ubicaciones.actualizar(newInfo);

      if (data.status == 200) {
        alert(data.mensaje);
        selectedLocation = null;
        if (selectedLocationTable) {
          selectedLocationTable.textContent = "Selecciona una ubicacion";
        }

        await loadLocactions();
      } else {
        alert(data.result || data.mensaje);
      }
    } catch (e) {
      alert("Error de servidor: " + e);
    }
  }
});

async function getAllLocations() {
  const data = await api.ubicaciones.obtenerTodos();

  return data.result || [];
}

async function loadLocactions() {
  try {
    ALL_LOCATIONS = await getAllLocations();
  } catch (e) {
    console.error("Error obteniendo ubicaciones: " + e);
    ALL_LOCATIONS = [];
  }

  renderLocations(ALL_LOCATIONS);
}

function renderLocations(locations) {
  const tbody = document.getElementById("tableLocations");
  const showLocations = document.getElementById("show");
  let count = 0;

  if (!tbody) return;

  tbody.innerHTML = "";

  if (!locations || locations.length === 0) {
    const row = document.createElement("tr");
    row.classList.add("emptyRow");
    const cell = document.createElement("td");
    cell.colSpan = 3;
    cell.textContent = "No hay ubicaciones disponibles";
    row.appendChild(cell);
    tbody.appendChild(row);
    if (showLocations) {
      showLocations.textContent = "Mostrando 0 de 0 ubicaciones";
    }
    if (selectedLocationTable) {
      selectedLocationTable.textContent = "Selecciona una ubicacion";
    }
    return;
  }

  locations.forEach((location) => {
    const row = document.createElement("tr");
    const cbTd = document.createElement("td");
    const nameTd = document.createElement("td");
    const descTd = document.createElement("td");
    const buttonsTd = document.createElement("td");
    const btnDelete = document.createElement("button");

    const checkBox = document.createElement("input");
    checkBox.type = "checkbox";
    checkBox.className = "checkbox";
    cbTd.addEventListener("change", () => {
      if (checkBox.checked) {
        seleccionarFila(tbody, checkBox, location);
      } else {
        deseleccionarFila(tbody);
      }
    });

    cbTd.appendChild(checkBox);

    const { id_ubicacion, nombre, descripcion } = location;
    nameTd.textContent = nombre ?? "";
    descTd.textContent = descripcion ?? "";

    btnDelete.className = "btnDelete";
    btnDelete.onclick = async function () {
      if (!confirm("¿Estás seguro de que deseas eliminar esta ubicacion?")) {
        return;
      }

      try {
        let data = await api.ubicaciones.eliminar(id_ubicacion);

        if (data.result == "Success") {
          alert("Ubicacion eliminada correctamente");
          selectedLocation = null;
          if (selectedLocationTable) {
            selectedLocationTable.textContent = "Selecciona una ubicacion";
          }
          await loadLocactions();
        } else {
          alert(
            data.mensaje || data.result || "Error al eliminar la ubicacion",
          );
        }
      } catch (e) {
        alert("Error de servidor: " + e);
      }
    };

    const trash = document.createElement("img");
    trash.src = "../img/trash.svg";
    trash.className = "svg";

    const trashSpan = document.createElement("span");
    trashSpan.textContent = "Borrar";

    btnDelete.appendChild(trash);
    btnDelete.appendChild(trashSpan);
    buttonsTd.appendChild(btnDelete);

    row.appendChild(cbTd);
    row.appendChild(nameTd);
    row.appendChild(descTd);
    buttonsTd.className = "right";
    row.appendChild(buttonsTd);

    row.addEventListener("click", () => {
      selectedLocation = location;

      if (selectedLocationTable) {
        selectedLocationTable.textContent =
          nombre + " seleccionada" || "Error al seleccionar ubicacion";
      }

      checkBox.checked = true;
      seleccionarFila(tbody, checkBox, location);
    });

    tbody.appendChild(row);
    count++;
  });

  if (showLocations) {
    showLocations.textContent =
      "Mostrando " + count + " de " + count + " ubicaciones";
  }

  function seleccionarFila(tbody, selectedCb, location) {
    tbody.querySelectorAll(".checkbox").forEach((cb) => {
      if (cb !== selectedCb) {
        cb.checked = false;
      }
    });

    selectedLocation = location;
    btnSave.disabled = false;
    nameField.disabled = false;
    descField.disabled = false;

    nameField.value = location.nombre;
    descField.value = location.descripcion;
  }

  function deseleccionarFila(tbody) {
    tbody.querySelectorAll("checkbox").forEach((cb) => {
      cb.checked = false;
    });

    selectedLocation = null;
    if (selectedLocationTable) {
      selectedLocationTable.textContent = "Selecciona una ubicacion";
    }

    // Deshabilitar el botón de borrar
    btnSave.disabled = true;

    nameField.textContent = "";
    descField.textContent = "";
    nameField.disabled = true;
    descField.disabled = true;
  }
}
