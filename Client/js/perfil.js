const nameInput = document.getElementById("name");
const surnameInput = document.getElementById("surname");
const usernameInput = document.getElementById("username");
const roleInput = document.getElementById("role");
const btnSave = document.getElementById("btnSave");
const btnDelete = document.getElementById("btnDelete");
const message = document.getElementById("message");

let user = null;
let editing = false;

document.addEventListener("DOMContentLoaded", async () => {
  nameInput.disabled = true;
  surnameInput.disabled = true;
  usernameInput.disabled = true;
  roleInput.disabled = true;

  await loadUser();
});

async function getUser() {
  let data = null;
  data = await auth.obtenerSesion();

  return data.usuario;
}

async function loadUser() {
  try {
    user = await getUser();
  } catch (e) {
    showMessage("Error obteniendo perfil: " + e, true);
    user = null;
  }

  loadUserInfo(user);
}

function loadUserInfo(user) {
  if (!user) return;

  const { nombre, apellido, username, role } = user;
  nameInput.value = nombre ?? "";
  surnameInput.value = apellido ?? "";
  usernameInput.value = username ?? "";
  roleInput.value = role ?? "";

  if (role === "ADMIN") {
    btnDelete.disabled = true;
  }
  console.log(user);
}

btnSave.addEventListener("click", async () => {
  hideMessage();

  if (!editing) {
    nameInput.disabled = false;
    surnameInput.disabled = false;
    editing = true;
    btnSave.textContent = "Guardar cambios";
  } else {
    if (!nameInput.value || !surnameInput.value) {
      showMessage("Por favor, rellena los campos.", true);
    } else {
      let newInfo = {
        id: user.id_usuario,
        nombre: nameInput.value,
        apellido: surnameInput.value,
        username: user.username,
        role: user.role,
      };

      try {
        const data = await api.usuarios.actualizar(newInfo);

        if (data.exito) {
          await loadUser();
          showMessage(data.mensaje);
          nameInput.disabled = true;
          surnameInput.disabled = true;
          editing = false;
          btnSave.textContent = "Editar datos";
        } else {
          showMessage(data.mensaje, true);
        }
      } catch (e) {
        showMessage("Error de servidor: " + e, true);
      }
    }
  }
});

btnDelete.addEventListener("click", async () => {
  hideMessage();

  if (
    !confirm(
      "¿Estás seguro de que deseas borrar tu cuenta? No podras iniciar sesion despues!",
    )
  ) {
    return;
  }

  try {
    let data = await api.usuarios.eliminar(id_usuario);

    if (data.exito) {
      await auth.logout();
    } else {
      showMessage(data.mensaje || "Error al eliminarte a ti mismo.", true);
    }
  } catch (e) {
    showMessage("Error de servidor: " + e, true);
  }
});

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
