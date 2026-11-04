const nameField = document.getElementById("name");
const surnameField = document.getElementById("surname");
const usernameField = document.getElementById("username");
const btnAdd = document.getElementById("btnAdd");
const btnSave = document.getElementById("btnSave");
const btnCancel = document.getElementById("btnCancel");
const selectedUserTable = document.getElementById("select");
const tbody = document.getElementById("tableLocations");
const message = document.getElementById("message");

let add = false;
let ALL_USERS = [];
let selectedUser = null;

document.addEventListener("DOMContentLoaded", async () => {
  await loadUsers();

  nameField.disabled = true;
  surnameField.disabled = true;
  usernameField.disabled = true;
  btnSave.disabled = true;
  btnCancel.disabled = true;
});

async function getAllUsers() {
  const data = await api.usuarios.obtenerTodos();

  return data || [];
}

async function loadUsers() {
  try {
    ALL_USERS = await getAllUsers();
  } catch (e) {
    showMessage("Error obteniendo usuarios: " + e, true);
    ALL_USERS = [];
  }

  renderUsers(ALL_USERS);
}

function renderUsers(users) {
  const showUsers = document.getElementById("show");
  let count = 0;

  if (!tbody) return;

  tbody.innerHTML = "";

  if (!users || users.length === 0) {
    tbody.innerHTML = `
    <tr>
      <td class="emptyRow" colspan="5">No hay usuarios disponibles</td>
    </tr>
    `;

    if (showUsers) {
      showUsers.textContent = "Mostrando 0 de 0 usuarios";
    }
    if (selectedUserTable) {
      selectedUserTable.textContent = "Selecciona un usuario";
    }
    return;
  }

  users.forEach((user) => {
    const row = document.createElement("tr");
    const cbTd = document.createElement("td");
    const nameTd = document.createElement("td");
    const surnameTd = document.createElement("td");
    const usernameTd = document.createElement("td");
    const roleTd = document.createElement("td");
    const deleteTd = document.createElement("td");
    const btnDelete = document.createElement("button");

    const checkBox = document.createElement("input");
    checkBox.type = "checkbox";
    checkBox.className = "checkbox";
    cbTd.addEventListener("change", () => {
      if (checkBox.checked) {
        seleccionarFila(checkBox, user);
      } else {
        deseleccionarFila();
      }
    });

    cbTd.appendChild(checkBox);

    const { id_usuario, nombre, apellido, username, role } = user;
    nameTd.textContent = nombre ?? "";
    surnameTd.textContent = apellido ?? "";
    usernameTd.textContent = username ?? "";
    roleTd.textContent = role ?? "";

    btnDelete.className = "btnDelete";
    btnDelete.onclick = async function () {
      if (
        !confirm(
          "¿Estás seguro de que deseas eliminar este usuario? No podra iniciar sesion.",
        )
      ) {
        return;
      }

      try {
        let data = await api.usuarios.eliminar(id_usuario);

        if (data.exito) {
          await loadLocactions();
          showMessage("Usuario eliminado correctamente");
          deseleccionarFila();
        } else {
          showMessage(data.mensaje || "Error al eliminar el usuario", true);
        }
      } catch (e) {
        showMessage("Error de servidor: " + e, true);
      }
    };

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
    row.appendChild(surnameTd);
    row.appendChild(usernameTd);
    row.appendChild(roleTd);
    deleteTd.className = "right";
    row.appendChild(deleteTd);

    row.addEventListener("click", () => {
      selectedUser = user;

      if (selectedUserTable) {
        selectedUserTable.textContent =
          nombre + " seleccionada" || "Error al seleccionar usuario";
      }

      checkBox.checked = true;
      seleccionarFila(checkBox, user);
    });

    tbody.appendChild(row);
    count++;
  });

  if (showUsers) {
    showUsers.textContent = "Mostrando " + count + " de " + count + " usuarios";
  }
}

nameField.addEventListener("change", () => {
  if (!nameField.value || !surnameField.value || !usernameField.value) {
    btnSave.disabled = true;
  } else {
    btnSave.disabled = false;
    nameField.value.trim();
    surnameField.value.trim();
    usernameField.value.trim();
  }
});

surnameField.addEventListener("change", () => {
  if (!nameField.value || !surnameField.value || !usernameField.value) {
    btnSave.disabled = true;
  } else {
    btnSave.disabled = false;
    nameField.value.trim();
    surnameField.value.trim();
    usernameField.value.trim();
  }
});

usernameField.addEventListener("change", () => {
  if (!nameField.value || !surnameField.value || !usernameField.value) {
    btnSave.disabled = true;
  } else {
    btnSave.disabled = false;
    nameField.value.trim();
    surnameField.value.trim();
    usernameField.value.trim();
  }
});

btnAdd.addEventListener("click", () => {
  add = true;

  btnSave.disabled = false;
  btnCancel.disabled = false;
  nameField.disabled = false;
  surnameField.disabled = false;
  usernameField.disabled = false;

  if (selectedUserTable) {
    selectedUserTable.textContent = "Añadiendo usuario";
  }
});

btnSave.addEventListener("click", async (e) => {
  e.preventDefault();

  if (!selectedUser && !add) {
    showMessage("Por favor, selecciona un usuario para editar.", true);
    return;
  }

  if (add) {
    if (!nameField.value || !surnameField.value || !usernameField.value) {
      showMessage("Por favor, rellena los campos.", true);
    } else {
      let newInfo = {
        nombre: nameField.value,
        apellido: surnameField.value,
        username: usernameField.value,
      };

      for (let i = 0; i < ALL_USERS.length; i++) {
        const user = ALL_USERS[i];

        if (user.username == newInfo.username) {
          showMessage(
            "No se puede usar el mismo nombre de usuario para dos usuarios.",
            true,
          );
          return;
        }
      }

      try {
        const data = await api.usuarios.crear(newInfo);

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
    if (!nameField.value || !surnameField.value || !usernameField.value) {
      showMessage("Por favor, rellena los campos.", true);
    } else {
      let newInfo = {
        id_usuario: selectedUser.id_usuario,
        nombre: nameField.value,
        apellido: surnameField.value,
        username: usernameField.value,
      };

      for (let i = 0; i < ALL_USERS.length; i++) {
        const user = ALL_USERS[i];

        if (user.username == newInfo.username) {
          showMessage(
            "No se puede usar el mismo nombre de usuario para dos usuarios.",
            true,
          );
          return;
        }
      }

      try {
        const data = await api.usuarios.actualizar(newInfo);

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

btnCancel.addEventListener("click", () => {
  deseleccionarFila();
});

function seleccionarFila(selectedCb, user) {
  add = false;
  tbody.querySelectorAll(".checkbox").forEach((cb) => {
    if (cb !== selectedCb) {
      cb.checked = false;
    }
  });

  selectedUser = user;
  btnSave.disabled = false;
  btnCancel.disabled = false;
  nameField.disabled = false;
  surnameField.disabled = false;
  usernameField.disabled = false;

  nameField.value = user.nombre;
  surnameField.value = user.apellido;
  usernameField = user.username;
}

function deseleccionarFila() {
  tbody.querySelectorAll(".checkbox").forEach((cb) => {
    cb.checked = false;
  });

  selectedLocation = null;
  if (selectedUserTable) {
    selectedUserTable.textContent = "Selecciona un usuario";
  }

  btnSave.disabled = true;
  btnCancel.disabled = true;

  nameField.value = "";
  surnameField.value = "";
  usernameField.value = "";
  nameField.disabled = true;
  surnameField.disabled = true;
  usernameField.disabled = true;
}

function showMessage(texto, esError = false) {
  message.textContent = texto;
  message.classList.toggle("error", esError);
  message.hidden = false;
}

function hideMessage() {
  message.hidden = true;
  message.textContent = "";
  message.classList.remove("error");
}
