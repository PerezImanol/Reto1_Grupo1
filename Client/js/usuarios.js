//(Muchas) Variables de objetos HTML
const nameField = document.getElementById("name");
const surnameField = document.getElementById("surname");
const usernameField = document.getElementById("username");
const passwordField = document.getElementById("password");
const oldPasswordField = document.getElementById("oldPassword");
const oldPasswordLabel = document.getElementById("oldPasswordLabel");
const confirmPasswordField = document.getElementById("passwordConfirm");
const roleSelect = document.getElementById("role");
const btnAnadir = document.getElementById("btnAnadir");
const btnBorrar = document.getElementById("btnBorrar");
const btnCancelar = document.getElementById("btnCancelar");
const form = document.getElementById("formEquipamiento");
const sectionForm = document.getElementById("seccionModificar");
const titleForm = document.getElementById("tituloFormulario");
const tbody = document.getElementById("tablaEquipamientos");
const message = document.getElementById("mensajeUsuarios");
const modal = document.getElementById("modalConfirmarBorrado");
const btnCancelarBorrado = document.getElementById("btnCancelarBorrado");
const btnConfirmarBorrado = document.getElementById("btnConfirmarBorrado");
const buscarUsuario = document.getElementById("buscarUsuario");

let ALL_USERS = [];
let selectedUser = null;
let adding = false;
let resolveDelete = null;

document.addEventListener("DOMContentLoaded", loadUsers);

async function loadUsers() {
  try {
    const data = await api.usuarios.obtenerTodos();
    if (!data.exito)
      throw new Error(data.mensaje || "No se pudieron cargar los usuarios.");
    ALL_USERS = data.result || [];
    renderUsers();
  } catch (error) {
    ALL_USERS = [];
    renderUsers();
    showMessage("Error al cargar los usuarios: " + error.message, true);
  }
}

function renderUsers() {
  tbody.innerHTML = "";

  const texto = buscarUsuario.value.trim().toLowerCase();

  const usuariosFiltrados = ALL_USERS.filter(
    (user) =>
      (user.nombre || "").toLowerCase().includes(texto) ||
      (user.apellido || "").toLowerCase().includes(texto) ||
      (user.username || "").toLowerCase().includes(texto) ||
      (user.role || "").toLowerCase().includes(texto),
  );

  if (usuariosFiltrados.length === 0) {
    const mensaje =
      ALL_USERS.length === 0 ?
        "No hay usuarios disponibles."
      : "No se encontraron usuarios.";

    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="tabla-vacia">${mensaje}</td>
      </tr>
    `;
    return;
  }

  usuariosFiltrados.forEach((user) => {
    const row = document.createElement("tr");
    const checkCell = document.createElement("td");
    const checkbox = document.createElement("input");

    checkbox.type = "checkbox";
    checkbox.className = "checkbox-fila";
    checkbox.checked = selectedUser?.id_usuario == user.id_usuario;

    checkbox.addEventListener("change", () => {
      if (checkbox.checked) selectUser(user);
      else resetForm();
    });

    checkCell.appendChild(checkbox);
    row.appendChild(checkCell);

    [user.nombre, user.apellido, user.username, user.role].forEach((value) => {
      const cell = document.createElement("td");
      cell.textContent = value ?? "";
      row.appendChild(cell);
    });

    if (checkbox.checked) row.classList.add("fila-seleccionada");

    row.addEventListener("click", (event) => {
      if (event.target !== checkbox) selectUser(user);
    });

    tbody.appendChild(row);
  });
}

buscarUsuario.addEventListener("input", renderUsers);

function selectUser(user) {
  selectedUser = user;
  adding = false;
  hideMessage();
  nameField.value = user.nombre || "";
  surnameField.value = user.apellido || "";
  usernameField.value = user.username || "";
  roleSelect.value = String(user.role || "user").toLowerCase();
  passwordField.value = "";
  oldPasswordField.value = "";
  confirmPasswordField.value = "";
  oldPasswordLabel.hidden = false;
  titleForm.textContent = "Modificar usuario";
  btnBorrar.disabled = false;
  showForm();
  renderUsers();
}

function showForm() {
  sectionForm.hidden = false;
  form.hidden = false;
  window.setTimeout(
    () => sectionForm.scrollIntoView({ behavior: "smooth", block: "start" }),
    0,
  );
}

function resetForm() {
  selectedUser = null;
  adding = false;
  form.reset();
  oldPasswordLabel.hidden = true;
  sectionForm.hidden = true;
  form.hidden = true;
  btnBorrar.disabled = true;
  renderUsers();
}

btnAnadir.addEventListener("click", () => {
  selectedUser = null;
  adding = true;
  hideMessage();
  form.reset();

  roleSelect.value = "user";

  // Ocultar contraseña actual al añadir
  oldPasswordLabel.hidden = true;
  oldPasswordField.value = "";

  titleForm.textContent = "Añadir usuario";
  btnBorrar.disabled = true;

  showForm();
  renderUsers();
  nameField.focus();
});

btnCancelar.addEventListener("click", resetForm);

form.addEventListener("submit", async (event) => {
  event.preventDefault();
  hideMessage();

  const nombre = nameField.value.trim();
  const apellido = surnameField.value.trim();
  const username = usernameField.value.trim();

  if (!nombre || !apellido || !username)
    return showMessage("Rellena nombre, apellidos y nombre de usuario.", true);
  if (!passwordField.value)
    return showMessage("Introduce una contraseña nueva.", true);
  if (passwordField.value !== confirmPasswordField.value)
    return showMessage("Las contraseñas no coinciden.", true);
  if (!adding && !oldPasswordField.value)
    return showMessage("Introduce la contraseña actual.", true);

  const duplicado = ALL_USERS.some(
    (user) =>
      String(user.username).toLowerCase() === username.toLowerCase() &&
      user.id_usuario != selectedUser?.id_usuario,
  );
  if (duplicado)
    return showMessage("Ese nombre de usuario ya está registrado.", true);

  const wasAdding = adding;
  const info = { nombre, apellido, username, role: roleSelect.value };

  try {
    let result;
    if (wasAdding) {
      info.password = passwordField.value;
      result = await api.usuarios.crear(info);
    } else {
      info.id = selectedUser.id_usuario;
      info.password = oldPasswordField.value;
      info.newPassword = passwordField.value;
      result = await api.usuarios.actualizar(info);
    }

    if (!result.exito)
      throw new Error(result.mensaje || "No se pudieron guardar los cambios.");
    resetForm();
    await loadUsers();
    showMessage(
      result.mensaje ||
        (wasAdding ?
          "Usuario añadido correctamente."
        : "Usuario modificado correctamente."),
    );
  } catch (error) {
    showMessage("Error al guardar: " + error.message, true);
  }
});

btnBorrar.addEventListener("click", async () => {
  if (!selectedUser || !(await confirmDelete())) return;

  try {
    const result = await api.usuarios.eliminar(selectedUser.id_usuario);
    if (!result.exito)
      throw new Error(result.mensaje || "No se pudo eliminar el usuario.");
    resetForm();
    await loadUsers();
    showMessage(result.mensaje || "Usuario eliminado correctamente.");
  } catch (error) {
    showMessage("Error al eliminar: " + error.message, true);
  }
});

function confirmDelete() {
  modal.hidden = false;
  return new Promise((resolve) => {
    resolveDelete = resolve;
  });
}

function closeDelete(value) {
  modal.hidden = true;
  if (resolveDelete) resolveDelete(value);
  resolveDelete = null;
}

btnCancelarBorrado.addEventListener("click", () => closeDelete(false));
btnConfirmarBorrado.addEventListener("click", () => closeDelete(true));

modal.addEventListener("click", (event) => {
  if (event.target === modal) closeDelete(false);
});

function showMessage(text, error = false) {
  message.textContent = text;
  message.classList.toggle("mensaje-error", error);
  message.hidden = false;

  if (error) {
    message.scrollIntoView({
      behavior: "smooth",
      block: "center",
    });
  }
}

//Metodo para esconder el mensaje activo
function hideMessage() {
  message.hidden = true;
  message.textContent = "";
  message.classList.remove("mensaje-error");
}
