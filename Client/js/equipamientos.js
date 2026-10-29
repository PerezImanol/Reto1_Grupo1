const tabla = document.getElementById('tablaEquipamientos');
const seccionModificar = document.getElementById('seccionModificar');
const formEquipamiento = document.getElementById('formEquipamiento');
const btnBorrar = document.getElementById('btnBorrar');
const btnAnadir = document.getElementById('btnAnadir');
const btnCancelar = document.getElementById('btnCancelar');
const tituloFormulario = document.getElementById('tituloFormulario');

const buscador = document.getElementById('search');
const columnaFiltro = document.getElementById('columnaFiltro');
const btnBuscar = document.getElementById('btnBuscar');

const mensajeEquipamientos = document.getElementById('mensajeEquipamientos');
const modalConfirmarBorrado = document.getElementById('modalConfirmarBorrado');
const textoConfirmacionBorrado = document.getElementById('textoConfirmacionBorrado');
const btnCancelarBorrado = document.getElementById('btnCancelarBorrado');
const btnConfirmarBorrado = document.getElementById('btnConfirmarBorrado');

let modoFormulario = 'modificar';

let equipamientoSeleccionado = null;
let listaEquipamientos = [];
let ordenColumnas = {};

// Cargar los equipamientos al abrir la página
document.addEventListener('DOMContentLoaded', cargarEquipamientos);


/******************************** CARGAR EQUIPAMIENTOS ********************************/
async function cargarEquipamientos() {
    try {
        listaEquipamientos = await api.equipamientos.obtenerTodos();

        tabla.innerHTML = '';

        if (!Array.isArray(listaEquipamientos) || listaEquipamientos.length === 0) {
            tabla.innerHTML = `
                <tr>
                    <td colspan="7" class="tabla-vacia">
                        No hay equipamientos registrados.
                    </td>
                </tr>
            `;
            return;
        }

        listaEquipamientos.forEach(equipamiento => {
            const fila = document.createElement('tr');

            // Checkbox para seleccionar la fila
            const celdaSeleccion = document.createElement('td');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.classList.add('checkbox-fila');
            checkbox.setAttribute('aria-label', 'Seleccionar equipamiento');
            celdaSeleccion.appendChild(checkbox);
            fila.appendChild(celdaSeleccion);

            // Datos del equipamiento
            const datos = [
                equipamiento.nombre,
                equipamiento.descripcion,
                equipamiento.marca,
                equipamiento.modelo,
                equipamiento.categoria,
                equipamiento.nombreUbicacion
            ];
            
            datos.forEach(dato => {
                const celda = document.createElement('td');
                celda.textContent = dato ?? '-';
                fila.appendChild(celda);
            });

            // Seleccionar o deseleccionar usando solamente el checkbox
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    seleccionarFila(fila, checkbox, equipamiento);
                } else {
                    deseleccionarFila();
                }
            });

            tabla.appendChild(fila);
        });

        deseleccionarFila();

    } catch (error) {
        console.error('Error al cargar los equipamientos:', error);
        mostrarMensaje('Error al cargar los equipamientos');
    }
}


/******************************** SELECCIONAR FILA ********************************/
function seleccionarFila(filaSeleccionada, checkboxSeleccionado, equipamiento) {

    // Desmarcar las demás filas
    tabla.querySelectorAll('.checkbox-fila').forEach(checkbox => {
        if (checkbox !== checkboxSeleccionado) {
            checkbox.checked = false;
        }
    });

    // Quitar el color de las demás filas
    tabla.querySelectorAll('tr').forEach(fila => {
        fila.classList.remove('fila-seleccionada');
    });

    // Marcar la fila elegida
    filaSeleccionada.classList.add('fila-seleccionada');
    modoFormulario = 'modificar';
    equipamientoSeleccionado = equipamiento;

    // Habilitar el botón de borrar
    btnBorrar.disabled = false;

    // Mostrar el formulario con los datos de la fila
    seccionModificar.hidden = false;
    formEquipamiento.hidden = false;

    tituloFormulario.textContent = 'Modificar equipamiento';

    formEquipamiento.elements.namedItem('nombre').value =
        equipamiento.nombre ?? '';

    formEquipamiento.elements.namedItem('descripcion').value =
        equipamiento.descripcion ?? '';

    formEquipamiento.elements.namedItem('marca').value =
        equipamiento.marca ?? '';

    formEquipamiento.elements.namedItem('modelo').value =
        equipamiento.modelo ?? '';

    formEquipamiento.elements.namedItem('categoria').value =
        equipamiento.categoria ?? '';

    formEquipamiento.elements.namedItem('nombreUbicacion').value =
        equipamiento.nombreUbicacion ?? '';
}


/******************************** DESELECCIONAR FILA ********************************/

function deseleccionarFila() {

    tabla.querySelectorAll('.checkbox-fila').forEach(checkbox => {
        checkbox.checked = false;
    });

    tabla.querySelectorAll('tr').forEach(fila => {
        fila.classList.remove('fila-seleccionada');
    });

    equipamientoSeleccionado = null;

    // Deshabilitar el botón de borrar
    btnBorrar.disabled = true;

    // Ocultar el formulario
    seccionModificar.hidden = true;
    formEquipamiento.hidden = true;
}


/******************************** MOSTRAR MENSAJE ********************************/

function mostrarMensaje(texto, esError = false) {
    mensajeEquipamientos.textContent = texto;
    mensajeEquipamientos.classList.toggle('mensaje-error', esError);
    mensajeEquipamientos.hidden = false;
    mensajeEquipamientos.setAttribute('role', esError ? 'alert' : 'status');
}

function ocultarMensaje() {
    mensajeEquipamientos.hidden = true;
    mensajeEquipamientos.textContent = '';
    mensajeEquipamientos.classList.remove('mensaje-error');
}


/******************************** AÑADIR EQUIPAMIENTO ********************************/
btnAnadir.addEventListener('click', () => {
    deseleccionarFila();

    formEquipamiento.reset();

    modoFormulario = 'crear';

    seccionModificar.hidden = false;
    formEquipamiento.hidden = false;

    tituloFormulario.textContent = 'Añadir equipamiento';

    formEquipamiento.elements.namedItem('nombreUbicacion').value = 'Almacén';
});


/******************************** CANCELAR ********************************/

btnCancelar.addEventListener('click', () => {
    deseleccionarFila();
});


/******************************** BUSCADOR ********************************/
columnaFiltro.addEventListener('change', () => {
    const columna = columnaFiltro.options[
        columnaFiltro.selectedIndex
    ].text;

    buscador.placeholder = `Buscar por ${columna.toLowerCase()}...`;
    buscador.value = '';
});



/**************************************BUSCADOR *********************************************/
btnBuscar.addEventListener('click', buscarEquipamientos);

buscador.addEventListener('keydown', evento => {
    if (evento.key === 'Enter') {
        buscarEquipamientos();
    }
});

function buscarEquipamientos() {
    const texto = buscador.value.trim().toLowerCase();
    const columna = columnaFiltro.value;

    const filas = tabla.querySelectorAll('tr');

    const indice = {
        nombre: 1,
        descripcion: 2,
        marca: 3,
        modelo: 4,
        categoria: 5,
        nombreUbicacion: 6
    };

    filas.forEach(fila => {
        // Ignorar las filas que muestran mensajes
        if (fila.cells.length !== 7) {
            return;
        }

        const valor = fila.cells[indice[columna]].textContent.toLowerCase();

        fila.style.display = valor.includes(texto) ? '' : 'none';
    });
}


/******************************** GUARDAR EQUIPAMIENTO ********************************/

formEquipamiento.addEventListener('submit', async evento => {
    evento.preventDefault();

    if (modoFormulario === 'modificar' && !equipamientoSeleccionado) {
        mostrarMensaje('Selecciona un equipamiento para modificarlo', true);
        return;
    }

    const datos = {
        nombre: formEquipamiento.elements.namedItem('nombre').value.trim(),
        descripcion: formEquipamiento.elements.namedItem('descripcion').value.trim(),
        marca: formEquipamiento.elements.namedItem('marca').value.trim(),
        modelo: formEquipamiento.elements.namedItem('modelo').value.trim(),
        categoria: formEquipamiento.elements.namedItem('categoria').value
    };

    if (modoFormulario === 'crear') {
        datos.id_ubicacion_actual = 1;
    } else {
        datos.id_equipamiento = equipamientoSeleccionado.id_equipamiento;
        datos.id_ubicacion_actual = equipamientoSeleccionado.id_ubicacion_actual;
    }

    try {
        const respuesta = await fetch(API_BASE + 'EquipamientoAPI.php', {
            method: modoFormulario === 'crear' ? 'POST' : 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify(datos)
        });

        const textoRespuesta = await respuesta.text();

        let resultado;

        try {
            resultado = JSON.parse(textoRespuesta);
        } catch {
            throw new Error(
                'El servidor no ha devuelto un JSON válido. Comprueba EquipamientoAPI.php.'
            );
        }

        if (!respuesta.ok || resultado.status !== 'success') {
            throw new Error(
                resultado.mensaje || resultado.error ||
                'No se pudo guardar el equipamiento'
            );
        }

        const mensaje = modoFormulario === 'crear'
            ? 'Equipamiento añadido correctamente.'
            : 'Equipamiento actualizado correctamente.';

        formEquipamiento.reset();
        deseleccionarFila();

        await cargarEquipamientos();

        mostrarMensaje(mensaje);
        // Subir arriba para mostrar la notificación.
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

    } catch (error) {
        console.error('Error al guardar el equipamiento:', error);
        mostrarMensaje('Error al guardar: ' + error.message, true);
    }
});

/******************************** BORRAR EQUIPAMIENTO ********************************/
btnBorrar.addEventListener('click', async () => {
    if (!equipamientoSeleccionado) {
        mostrarMensaje('Selecciona un equipamiento para eliminarlo.', true);
        return;
    }

    const confirmar = await confirmarEliminacion(
        equipamientoSeleccionado.nombre
    );

    if (!confirmar) {
        return;
    }

    ocultarMensaje();

    try {
        const respuesta = await fetch(
            API_BASE + 'EquipamientoAPI.php?id=' +
            equipamientoSeleccionado.id_equipamiento,
            {
                method: 'DELETE',
                credentials: 'same-origin'
            }
        );

        const resultado = await respuesta.json();

        if (!respuesta.ok || resultado.status !== 'success') {
            throw new Error(
                resultado.mensaje || 'No se pudo eliminar el equipamiento.'
            );
        }

        deseleccionarFila();
        await cargarEquipamientos();

        mostrarMensaje('Equipamiento eliminado correctamente.');
        // Subir arriba para mostrar la notificación.
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    } catch (error) {
        console.error('Error al eliminar:', error);
        mostrarMensaje(
            error.message || 'Error al eliminar el equipamiento.',
            true
        );
    }
});

/******************************** CONFIRMAR ELIMINACIÓN ********************************/
function confirmarEliminacion(nombre) {
    return new Promise(resolve => {
        textoConfirmacionBorrado.textContent =
            `Seguro que quieres eliminar ${nombre}?`;

        modalConfirmarBorrado.hidden = false;

        function cerrarModal(confirmado) {
            modalConfirmarBorrado.hidden = true;

            btnConfirmarBorrado.removeEventListener('click', confirmar);
            btnCancelarBorrado.removeEventListener('click', cancelar);

            resolve(confirmado);
        }

        function confirmar() {
            cerrarModal(true);
        }

        function cancelar() {
            cerrarModal(false);
        }

        btnConfirmarBorrado.addEventListener('click', confirmar);
        btnCancelarBorrado.addEventListener('click', cancelar);
    });
}
/******************************** ORDENAR TABLA ********************************/
function ordenarTabla(campo, imagen) {
    if (ordenColumnas[campo] === 'asc') {
        ordenColumnas[campo] = 'desc';
    } else {
        ordenColumnas[campo] = 'asc';
    }

    const orden = ordenColumnas[campo];

    // Quitar la línea de todas las columnas
    document.querySelectorAll('thead th').forEach(th => {
        th.classList.remove('columna-ordenada');
    });

    // Poner la línea únicamente en la columna seleccionada
    imagen.closest('th').classList.add('columna-ordenada');

    listaEquipamientos.sort((a, b) => {
        const valorA = String(a[campo] ?? '');
        const valorB = String(b[campo] ?? '');

        const resultado = valorA.localeCompare(valorB, 'es', {
            numeric: true,
            sensitivity: 'base'
        });

        return orden === 'asc' ? resultado : -resultado;
    });

    mostrarTablaOrdenada();
}

/******************************** MOSTRAR TABLA ORDENADA ********************************/
function mostrarTablaOrdenada() {
    tabla.innerHTML = '';

    listaEquipamientos.forEach(equipamiento => {
        const fila = document.createElement('tr');

        const celdaSeleccion = document.createElement('td');
        const checkbox = document.createElement('input');

        checkbox.type = 'checkbox';
        checkbox.classList.add('checkbox-fila');
        checkbox.setAttribute('aria-label', 'Seleccionar equipamiento');

        celdaSeleccion.appendChild(checkbox);
        fila.appendChild(celdaSeleccion);

        const datos = [
            equipamiento.nombre,
            equipamiento.descripcion,
            equipamiento.marca,
            equipamiento.modelo,
            equipamiento.categoria,
            equipamiento.nombreUbicacion
        ];

        datos.forEach(dato => {
            const celda = document.createElement('td');
            celda.textContent = dato ?? '-';
            fila.appendChild(celda);
        });

        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                seleccionarFila(fila, checkbox, equipamiento);
            } else {
                deseleccionarFila();
            }
        });

        tabla.appendChild(fila);
    });

    deseleccionarFila();
}