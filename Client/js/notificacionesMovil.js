
document.addEventListener("DOMContentLoaded", async () => {
    const formulario = document.getElementById("formNotificacion");
    const lista = document.getElementById("listaNotificaciones");
    const mensaje = document.getElementById("mensajeNotificaciones");

    const idInput = document.getElementById("idNotificacion");
    const tituloInput = document.getElementById("tituloNotificacion");
    const descripcionInput = document.getElementById("descripcionNotificacion");
    const btnGuardar = document.getElementById("btnGuardarNotificacion");
    const btnCancelar = document.getElementById("btnCancelarEdicion");

    let usuario;
    let notificaciones = [];

    function mostrarMensaje(texto, tipo = "exito") {
        mensaje.textContent = texto;
        mensaje.className = `mensaje ${tipo}`;
    }

    function limpiarFormulario() {
        formulario.reset();
        idInput.value = "";
        btnGuardar.textContent = "Crear notificación";
        btnCancelar.hidden = true;
    }

    async function cargarNotificaciones() {
        try {
            const respuesta = await peticionAPI(
                `NotificacionAPI.php?id_user=${usuario.id_usuario}`
            );

            notificaciones = Array.isArray(respuesta) ? respuesta : [];

            lista.innerHTML = "";

            if (notificaciones.length === 0) {
                lista.innerHTML = "<p>No tienes notificaciones.</p>";
                return;
            }

            notificaciones.forEach(notificacion => {
                const tarjeta = document.createElement("article");
                tarjeta.className = "tarjeta-movil";

                const titulo = document.createElement("h3");
                titulo.textContent = notificacion.titulo;

                const descripcion = document.createElement("p");
                descripcion.textContent = notificacion.descripcion;

                const estado = document.createElement("p");
                estado.textContent = `Estado: ${notificacion.estado ?? "Pendiente"}`;

                const acciones = document.createElement("div");
                acciones.className = "acciones-tarjeta";

                const estadoActual = String(
                    notificacion.estado?.name ?? notificacion.estado ?? ""
                ).toUpperCase();

                if (estadoActual !== "REALIZADA") {

                    const btnEditar = document.createElement("button");
                    btnEditar.type = "button";
                    btnEditar.textContent = "Editar";

                    btnEditar.addEventListener("click", () => {
                        idInput.value = notificacion.id_notificacion;
                        tituloInput.value = notificacion.titulo;
                        descripcionInput.value = notificacion.descripcion;

                        btnGuardar.textContent = "Guardar cambios";
                        btnCancelar.hidden = false;

                        formulario.scrollIntoView({ behavior: "smooth" });
                    });

                    const btnEliminar = document.createElement("button");
                    btnEliminar.type = "button";
                    btnEliminar.textContent = "Eliminar";
                    btnEliminar.className = "boton-secundario";

                    btnEliminar.addEventListener("click", async () => {
                        if (!confirm("¿Quieres eliminar esta notificación?")) {
                            return;
                        }

                        try {
                            await peticionAPI(
                                `NotificacionAPI.php?id=${notificacion.id_notificacion}`,
                                "DELETE"
                            );

                            mostrarMensaje("Notificación eliminada correctamente.");
                            await cargarNotificaciones();

                        } catch (error) {
                            mostrarMensaje(error.message, "error");
                        }
                    });

                    acciones.append(btnEditar, btnEliminar);

                } else {
                    const bloqueada = document.createElement("p");
                    bloqueada.textContent = "Esta notificación está realizada y no se puede modificar ni eliminar.";
                    bloqueada.className = "notificacion-bloqueada";

                    acciones.appendChild(bloqueada);
                }
                tarjeta.append(titulo, descripcion, estado, acciones);
                lista.appendChild(tarjeta);
            });

        } catch (error) {
            mostrarMensaje(error.message, "error");
        }
    }

    try {
        const sesion = await auth.obtenerSesion();

        if (!sesion || !sesion.usuario) {
            window.location.href = "../pages/login.html";
            return;
        }

        usuario = sesion.usuario;
        await cargarNotificaciones();

    } catch (error) {
        mostrarMensaje(error.message, "error");
    }

    formulario.addEventListener("submit", async event => {
        event.preventDefault();

        const id = idInput.value;

        const datos = {
            id_user: usuario.id_usuario,
            titulo: tituloInput.value.trim(),
            descripcion: descripcionInput.value.trim()
        };

        try {
            if (id) {
                const notificacion = notificaciones.find(
                    n => String(n.id_notificacion) === String(id)
                );

                await peticionAPI("NotificacionAPI.php", "PUT", {
                    id: Number(id),
                    titulo: datos.titulo,
                    descripcion: datos.descripcion
                });

                mostrarMensaje("Notificación actualizada correctamente.");
            } else {
                await peticionAPI("NotificacionAPI.php", "POST", {
                    ...datos,
                    fecha_creacion: new Date().toISOString().slice(0, 19).replace("T", " "),
                    estado: "PENDIENTE"
                });

                mostrarMensaje("Notificación creada correctamente.");
            }

            limpiarFormulario();
            await cargarNotificaciones();

        } catch (error) {
            mostrarMensaje(error.message, "error");
        }
    });

    btnCancelar.addEventListener("click", limpiarFormulario);
});
    