const btnEquipamiento = document.getElementById("btnEquip");
const btnUbicacion = document.getElementById("btnUbicacion");
const btnPanel = document.getElementById("btnPanel");
const btnNotificaciones = document.getElementById("btnNotificaciones");
const btnPerfil = document.getElementById("btnPerfil");
let ALL_LOCATIONS = [];
let selectedLocation = null;

btnEquipamiento.addEventListener("click", () => {
  window.location.href = "../pages/equipamientos.html";
});

btnUbicacion.addEventListener("click", () => {
  window.location.href = "../pages/ubicaciones.html";
});

btnPanel.addEventListener("click", () => {
  window.location.href = "../pages/usuarios.html";
});

btnNotificaciones.addEventListener("click", () => {
  window.location.href = "../pages/notificaciones.html";
});

btnPerfil.addEventListener("click", () => {
  window.location.href = "../pages/perfil.html";
});

document.addEventListener("DOMContentLoaded", async () => {
  //Sets up location list
  loadLocactions();
});

async function getAllLocations() {
  const response = await fetch("../../Server/public/UbicacionAPI.php");

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
  const selectedLocationTable = document.getElementById("select");
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
    pencil.id = pencil;

    const trash = document.createElement("img");
    trash.src = "../img/trash.svg";
    trash.className = "svg";
    trash.id = trash;

    const pencilSpan = document.createElement("span");
    pencilSpan.textContent = "Editar";

    const trashSpan = document.createElement("span");
    trashSpan.textContent = "Borrar";

    const { nombre, descripcion } = location;
    nameTd.textContent = descripcion ?? "";
    descTd.textContent = nombre ?? "";

    btnEdit.className = "btnEdit";
    btnEdit.onclick = function () {
      //Edit stuff goes here

      if (selectedLocation == location) {
        pencilSpan.textContent = "Editando";
      } else {
        pencilSpan.textContent = "Editar";
      }
    };
    btnEdit.appendChild(pencil);
    btnEdit.appendChild(pencilSpan);
    buttonsTd.appendChild(btnEdit);

    btnDelete.className = "btnDelete";
    btnDelete.onclick = function () {
      //Delete stuff goes here
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
