//(Muchas) Variables de objetos HTML
const nameField = document.getElementById("name");
const surnameField = document.getElementById("surname");
const usernameField = document.getElementById("username");
const passwordField = document.getElementById("password");
const oldPasswordLabel = document.getElementById("oldPasswordLabel");
const oldPasswordField = document.getElementById("oldPassword");
const confirmPasswordField = document.getElementById("passwordConfirm");
const roleSelect = document.getElementById("role");
const btnAdd = document.getElementById("btnAdd");
const btnSave = document.getElementById("btnSave");
const btnCancel = document.getElementById("btnCancel");
const selectedUserTable = document.getElementById("select");
const tbody = document.getElementById("tableLocations");
const message = document.getElementById("message");

//Variables de uso general (y carga de userID)
let userId = JSON.parse(localStorage.getItem("user_id"));
let add = false;
let ALL_USERS = [];
let selectedUser = null;

document.addEventListener("DOMContentLoaded", async () => {
  //Carga usuarios y deshabilita los botones de editar y los campos
  await loadUsers();

  nameField.disabled = true;
  surnameField.disabled = true;
  usernameField.disabled = true;
  passwordField.disabled = true;
  oldPasswordLabel.hidden = true;
  oldPasswordField.hidden = true;
  confirmPasswordField.disabled = true;
  roleSelect.disabled = true;
  btnSave.disabled = true;
  btnCancel.disabled = true;

  //maldito auto-fill de username y password por el navegador-
  usernameField.value = "";
  passwordField.value = "";
});

async function getAllUsers() {
  //Llama al metodo para obtener los usuarios y las pasa, o error.
  const data = await api.usuarios.obtenerTodos();

  if (data.exito) {
    return data || [];
  }

  //Propagacion de error para manejar despues.
  throw new Error(data.mensaje);
}

async function loadUsers() {
  //Recoge los usuarios o el error.
  try {
    ALL_USERS = await getAllUsers();
  } catch (e) {
    showMessage("Error obteniendo usuarios: " + e, true);
    ALL_USERS = [];
  }

  //Llama al siguiente metodo.
  renderUsers(ALL_USERS);
}

//Metodo para cargar los usuarios en la tabla.
function renderUsers(users) {
  const showUsers = document.getElementById("show");
  let count = 0;

  //Por si alguna manera falla de cargar el Table Body
  if (!tbody) return;

  //Vacia el body de la tabla
  tbody.innerHTML = "";

  //Si falla la carga o no hay nada.
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

  //ForEach para la creacion de la tabla entera.
  users.forEach((user) => {
    const row = document.createElement("tr");
    const cbTd = document.createElement("td");
    const nameTd = document.createElement("td");
    const surnameTd = document.createElement("td");
    const usernameTd = document.createElement("td");
    const roleTd = document.createElement("td");
    const deleteTd = document.createElement("td");
    const btnDelete = document.createElement("button");

    //Checkbox para seleccion
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

    //Info de tabla
    const { id_usuario, nombre, apellido, username, role } = user;
    nameTd.textContent = nombre ?? "";
    surnameTd.textContent = apellido ?? "";
    usernameTd.textContent = username ?? "";
    roleTd.textContent = role ?? "";

    //Setup boton borrar
    btnDelete.className = "btnDelete";
    btnDelete.onclick = async function () {
      hideMessage();

      //Comprobacion de si el usuario es a si mismo

      if (id_usuario === userId) {
        if (
          !confirm(
            "¿Estás seguro de que deseas borrar tu cuenta? No podras iniciar sesion despues!",
          )
        ) {
          return;
        }
      } else {
        if (
          !confirm(
            "¿Estás seguro de que deseas eliminar este usuario? No podra iniciar sesion despues.",
          )
        ) {
          return;
        }
      }

      //Intento de eliminar
      try {
        const data = await api.usuarios.eliminar(id_usuario);

        if (data.exito) {
          //Si te borrar a ti mismo, logout, sino mensaje normal
          if (id_usuario === userId) {
            await auth.logout();
          } else {
            await loadUsers();
            showMessage("Usuario eliminado correctamente");
            deseleccionarFila();
          }
        } else {
          if (id_usuario === userId) {
            showMessage(
              data.mensaje || "Error al eliminarte a ti mismo.",
              true,
            );
          } else {
            showMessage(data.mensaje || "Error al eliminar el usuario", true);
          }
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
    row.appendChild(surnameTd);
    row.appendChild(usernameTd);
    row.appendChild(roleTd);
    deleteTd.className = "right";
    row.appendChild(deleteTd);

    //Event Listener de click de fila
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

  //Para mostrar cuenta
  if (showUsers) {
    showUsers.textContent = "Mostrando " + count + " de " + count + " usuarios";
  }
}

//Comprobacion de nombre
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

//Comprobacion de apellido
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

//Comprobacion de username
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

//Faltan comprobaciones

//Acceso a añadir usuario, vacia campos por si hay algo
btnAdd.addEventListener("click", () => {
  hideMessage();
  add = true;

  btnSave.disabled = false;
  btnCancel.disabled = false;

  nameField.value = "";
  nameField.disabled = false;

  surnameField.value = "";
  surnameField.disabled = false;

  usernameField.value = "";
  usernameField.disabled = false;

  passwordField.value = "";
  passwordField.disabled = false;

  oldPasswordField.value = "";

  confirmPasswordField.value = "";
  confirmPasswordField.disabled = false;

  roleSelect.disabled = false;

  //No es tan obvio? Igual se puede cambiar
  if (selectedUserTable) {
    selectedUserTable.textContent = "Añadiendo usuario";
  }
});

//Metodo para añadir or modificar
btnSave.addEventListener("click", async (e) => {
  e.preventDefault();
  hideMessage();

  //Por si se modifica sin nada, hace nada al añadir
  if (!selectedUser && !add) {
    showMessage("Por favor, selecciona un usuario para editar.", true);
    return;
  }

  //Comprobacion de contraseña y comfirmacion
  if (!passwordField.value) {
    showMessage("Introduce una contraseña.", true);
    return;
  } else if (
    passwordField.value &&
    passwordField.value != confirmPasswordField.value
  ) {
    showMessage("Las contraseñas no coinciden.", true);
    return;
  }

  if (add) {
    //Validacion de campos por si acaso.
    if (!nameField.value || !surnameField.value || !usernameField.value) {
      showMessage("Por favor, rellena los campos.", true);
    } else {
      //Preparacion de info
      let newInfo = {
        nombre: nameField.value,
        apellido: surnameField.value,
        username: usernameField.value,
        password: passwordField.value,
        role: roleSelect.value,
      };

      //Por un constraint en la base de datos, validacion de username.
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

      //Intento de creacion
      try {
        const data = await api.usuarios.crear(newInfo);

        if (data.exito) {
          await loadUsers();
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
    //Validacion de campos por si acaso.
    if (!nameField.value || !surnameField.value || !usernameField.value) {
      showMessage("Por favor, rellena los campos.", true);
    } else {
      let newInfo = {
        id: selectedUser.id_usuario,
        nombre: nameField.value,
        apellido: surnameField.value,
        username: usernameField.value,
        password: oldPasswordField.value,
        newPassword: passwordField.value,
        role: roleSelect.value,
      };

      //Por un constraint en la base de datos, validacion de username.
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

      //Intento de actualizacion
      try {
        const data = await api.usuarios.actualizar(newInfo);

        if (data.exito) {
          showMessage("El usuario ha sido creado correctamente.");
          await loadUsers();
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

function seleccionarFila(selectedCb, user) {
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
  selectedUser = user;
  btnSave.disabled = false;
  btnCancel.disabled = false;
  nameField.disabled = false;
  surnameField.disabled = false;
  usernameField.disabled = false;
  passwordField.disabled = false;
  oldPasswordLabel.hidden = false;
  oldPasswordField.hidden = false;
  confirmPasswordField.disabled = false;
  roleSelect.disabled = false;

  nameField.value = user.nombre;
  surnameField.value = user.apellido;
  usernameField.value = user.username;

  //Setup de rol en el Select
  if (user.role == "USER") {
    roleSelect.selectedIndex = 0;
  } else {
    roleSelect.selectedIndex = 1;
  }
}

//Metodo para deseleccionar la fila seleccionada
function deseleccionarFila() {
  hideMessage();

  tbody.querySelectorAll(".checkbox").forEach((cb) => {
    cb.checked = false;
  });

  selectedLocation = null;
  if (selectedUserTable) {
    selectedUserTable.textContent = "Selecciona un usuario";
  }

  //Deshabilita todo lo necesario y vacia los campos
  btnSave.disabled = true;
  btnCancel.disabled = true;

  nameField.value = "";
  nameField.disabled = true;

  surnameField.value = "";
  surnameField.disabled = true;

  usernameField.value = "";
  usernameField.disabled = true;

  passwordField.value = "";
  passwordField.disabled = true;

  oldPasswordLabel.hidden = true;
  oldPasswordField.value = "";
  oldPasswordField.hidden = true;

  confirmPasswordField.value = "";
  confirmPasswordField.disabled = true;

  roleSelect.disabled = true;
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
