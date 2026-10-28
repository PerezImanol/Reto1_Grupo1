 
// Ruta base donde se encuentran las API de PHP
const API_BASE = new URL("../../Server/public/", document.currentScript.src).href;

async function peticionAPI(endpoint, metodo = "GET", datos = null) {
    // Configura el metodo HTTP, las cabeceras y el envio de cookies de sesion
    const opciones = {
        method: metodo,
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "same-origin"
    };

    // Si hay datos los convierte a JSON para enviarlos al servidor
    if (datos !== null) {
        opciones.body = JSON.stringify(datos);
    }

    // Envva la peticionn a la API indicada y luego convierte la respuesta del servidor de JSON a un objeto JS
    const respuesta = await fetch(API_BASE + endpoint, opciones);
    const resultado = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(resultado.error || resultado.mensaje || "Error en la petición");
    }

    return resultado;
}

// Agrupar las funciones pa comunicarse con las distintas APIs q tenemos
const api = {
    login: (datos) => peticionAPI("LoginAPI.php", "POST", datos),
    sesion: () => peticionAPI("LoginAPI.php"),
    logout: () => peticionAPI("LoginAPI.php", "DELETE"),

    usuarios: {
        obtenerTodos: () => peticionAPI("UsuarioAPI.php"),
        obtenerPorId: (id) => peticionAPI(`UsuarioAPI.php?id=${id}`),
        crear: (datos) => peticionAPI("UsuarioAPI.php", "POST", datos),
        actualizar: (datos) => peticionAPI("UsuarioAPI.php", "PUT", datos),
        eliminar: (id) => peticionAPI(`UsuarioAPI.php?id=${id}`, "DELETE")
    },

    equipamientos: {
        obtenerTodos: () => peticionAPI("EquipamientoAPI.php"),
        obtenerPorId: (id) => peticionAPI(`EquipamientoAPI.php?id=${id}`),
        crear: (datos) => peticionAPI("EquipamientoAPI.php", "POST", datos),
        actualizar: (datos) => peticionAPI("EquipamientoAPI.php", "PUT", datos),
        eliminar: (id) => peticionAPI(`EquipamientoAPI.php?id=${id}`, "DELETE")
    },

    ubicaciones: {
        obtenerTodos: () => peticionAPI("UbicacionAPI.php"),
        obtenerPorId: (id) => peticionAPI(`UbicacionAPI.php?id=${id}`),
        crear: (datos) => peticionAPI("UbicacionAPI.php", "POST", datos),
        actualizar: (datos) => peticionAPI("UbicacionAPI.php", "PUT", datos),
        eliminar: (id) => peticionAPI(`UbicacionAPI.php?id=${id}`, "DELETE")
    },

    historico: {
        obtenerTodos: () => peticionAPI("HistoricoUbicacionAPI.php"),
        crear: (datos) => peticionAPI("HistoricoUbicacionAPI.php", "POST", datos)
    },

    notificaciones: {
        obtenerTodos: () => peticionAPI("NotificacionAPI.php"),
        obtenerPorUsuario: (id) => peticionAPI(`NotificacionAPI.php?id_user=${id}`),
        obtenerPorId: (id) => peticionAPI(`NotificacionAPI.php?id=${id}`),
        crear: (datos) => peticionAPI("NotificacionAPI.php", "POST", datos),
        actualizar: (datos) => peticionAPI("NotificacionAPI.php", "PUT", datos),
        eliminar: (id) => peticionAPI(`NotificacionAPI.php?id=${id}`, "DELETE")
    }
};
