//Variables de objetos HTML
const nameInput = document.getElementById("name");
const surnameInput = document.getElementById("surname");
const usernameInput = document.getElementById("username");
const roleInput = document.getElementById("role");
const btnSave = document.getElementById("btnSave");
const btnDelete = document.getElementById("btnDelete");
const message = document.getElementById("message");

//Variables de uso general (y carga de userID)
let userId = JSON.parse(localStorage.getItem("user_id"));
let user = null;
let editing = false;

document.addEventListener("DOMContentLoaded", async () => {
  //Carga el usuario y deshabilita los campos
  await loadUser();

  nameInput.disabled = true;
  surnameInput.disabled = true;
  usernameInput.disabled = true;
  roleInput.disabled = true;
});

async function getUser() {
  const sesion = await auth.obtenerSesion();

  if (!sesion || !sesion.usuario) {
    //Propagacion de error para manejar despues.
    throw new Error(
      "No hay sesion encontrada. Inicia de sesion de nuevo para poder ver tu perfil.",
    );
  }

  const data = sesion.usuario;

  return data || null;
}

async function loadUser() {
  //Recoge el usuario o el error.
  try {
    user = await getUser();
  } catch (e) {
    showMessage("Error obteniendo perfil: " + e, true);
    user = null;
  }

  //Llama al siguiente metodo.
  loadUserInfo(user);
}

//Metodo para cargar el usuario en la tabla.
function loadUserInfo(user) {
  //Si no hay usuario, no sirve de nada el metodo.
  if (!user) return;

  //Info del usuario
  const { nombre, apellido, username, role } = user;
  nameInput.value = nombre ?? "";
  surnameInput.value = apellido ?? "";
  usernameInput.value = username ?? "";
  roleInput.value = role ?? "";

  //Si es admin, no se puede borrar su cuenta aqui.
  if (role === "ADMIN") {
    btnDelete.disabled = true;
  }
}

//Habilitacion de campos o metodo para modificar
btnSave.addEventListener("click", async () => {
  hideMessage();

  if (!editing) {
    nameInput.disabled = false;
    surnameInput.disabled = false;
    editing = true;
    btnSave.textContent = "Guardar cambios";
  } else {
    //Validacion de campos por si acaso.
    if (!nameInput.value || !surnameInput.value) {
      showMessage("Por favor, rellena los campos.", true);
    } else {
      //Preparacion de info
      let newInfo = {
        id: user.id_usuario,
        nombre: nameInput.value,
        apellido: surnameInput.value,
        username: user.username,
        role: user.role,
      };

      //Intento de actualizacion
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

//Metodo para borrarse a si mismo
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
