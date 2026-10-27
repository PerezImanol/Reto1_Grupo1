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

let equipamientoSeleccionado = null;


// Cargar los equipamientos al abrir la página
document.addEventListener('DOMContentLoaded', cargarEquipamientos);


/******************************** CARGAR EQUIPAMIENTOS ********************************/
async function cargarEquipamientos() {
    try {
        const equipamientos = await api.equipamientos.obtenerTodos();

        tabla.innerHTML = '';

        if (!Array.isArray(equipamientos) || equipamientos.length === 0) {
            mostrarMensaje('No hay equipamientos registrados');
            return;
        }

        equipamientos.forEach(equipamiento => {
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

function mostrarMensaje(mensaje) {
    tabla.innerHTML = '';

    const fila = document.createElement('tr');
    const celda = document.createElement('td');

    celda.colSpan = 7;
    celda.textContent = mensaje;
    celda.classList.add('tabla-vacia');

    fila.appendChild(celda);
    tabla.appendChild(fila);
}


/******************************** AÑADIR EQUIPAMIENTO ********************************/

btnAnadir.addEventListener('click', () => {

    // Quitar cualquier selección anterior
    deseleccionarFila();

    // Abrir el formulario vacío
    formEquipamiento.reset();

    seccionModificar.hidden = false;
    formEquipamiento.hidden = false;

    tituloFormulario.textContent = 'Añadir equipamiento';
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