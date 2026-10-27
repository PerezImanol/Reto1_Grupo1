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
  //Sets up location list
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
    let newInfo = [nameField.value, descField.value];
    const response = await api.ubicaciones.actualizar(newInfo);
    const data = await response.json();

    if (data.status !== 200) {
      alert(data.mensaje);
      selectedLocation = null;
      if (selectedLocationTable) {
        selectedLocationTable.textContent = "Selecciona una ubicacion";
      }
    } else {
      alert(data.result || data.mensaje);
    }
  }
});

async function getAllLocations() {
  const response = await api.ubicaciones.obtenerTodos();

  const data = await response.json();
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
    const nameTd = document.createElement("td");
    const descTd = document.createElement("td");
    const buttonsTd = document.createElement("td");
    const btnEdit = document.createElement("button");
    const btnDelete = document.createElement("button");

    //good god the svg shit looks inefficient asf but it works
    const pencil = document.createElement("img");
    pencil.src = "../img/pencil.svg";
    pencil.className = "svg";

    const trash = document.createElement("img");
    trash.src = "../img/trash.svg";
    trash.className = "svg";

    const pencilSpan = document.createElement("span");
    pencilSpan.textContent = "Editar";

    const trashSpan = document.createElement("span");
    trashSpan.textContent = "Borrar";

    const { id_ubicacion, nombre, descripcion } = location;
    //Sets up table info
    nameTd.textContent = descripcion ?? "";
    descTd.textContent = nombre ?? "";

    btnEdit.className = "btnEdit";
    btnEdit.onclick = function () {
      if (selectedLocation == location) {
        pencilSpan.textContent = "Editando";
        nameField.disabled = false;
        descField.disabled = false;
      } else {
        pencilSpan.textContent = "Editar";
      }
    };
    btnEdit.appendChild(pencil);
    btnEdit.appendChild(pencilSpan);
    buttonsTd.appendChild(btnEdit);

    btnDelete.className = "btnDelete";
    btnDelete.onclick = async function () {
      if (!selectedLocation) {
        alert("Por favor, selecciona una ubicacion para eliminar.");
        return;
      }

      if (!confirm("¿Estás seguro de que deseas eliminar esta ubicacion?")) {
        return;
      }

      try {
        let response = await api.ubicaciones.eliminar(id_ubicacion);

        let data = {};
        if (response.ok && response.status !== 204) {
          data = await response.json();
        }

        if (response.ok) {
          alert("Ubicacion eliminada correctamente");
          selectedLocation = null;
          if (selectedLocationTable) {
            selectedLocationTable.textContent = "Selecciona una ubicacion";
          }
          await loadLocactions();
        } else {
          alert(data.error || data.result || "Error al eliminar la ubicacion");
        }
      } catch (error) {
        console.error("Error: " + error);
        alert("Error al conectar al servidor.");
      }
    };

    btnDelete.appendChild(trash);
    btnDelete.appendChild(trashSpan);
    buttonsTd.appendChild(btnDelete);

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
    });

    tbody.appendChild(row);
    count++;
  });

  if (showLocations) {
    showLocations.textContent =
      "Mostrando " + count + " de " + count + " ubicaciones";
  }
}
