const listaEquipamientos = document.getElementById('listaEquipamientos');
const buscadorEquipamientos = document.getElementById('buscar');
const botonBuscar = document.getElementById('btnBuscar');
const criterioOrdenacion = document.getElementById('criterios-ordenacion');
const totalEquipamientos = document.getElementById('totalEquipamientos');
const mensajeEquipamientos = document.getElementById('mensajeEquipamientos');

let equipamientos = [];
let ubicaciones = [];

document.addEventListener('DOMContentLoaded', cargarDatos);
botonBuscar.addEventListener('click', renderizarEquipamientos);
buscadorEquipamientos.addEventListener('input', renderizarEquipamientos);
criterioOrdenacion.addEventListener('change', renderizarEquipamientos);
buscadorEquipamientos.addEventListener('keydown', (evento) => {
    if (evento.key === 'Enter') {
        renderizarEquipamientos();
    }
});
listaEquipamientos.addEventListener('change', cambiarUbicacion);

async function cargarDatos() {
    try {
        const [resultadoEquipamientos, resultadoUbicaciones] = await Promise.all([
            api.equipamientos.obtenerTodos(),
            api.ubicaciones.obtenerTodos()
        ]);

        if (!Array.isArray(resultadoEquipamientos)) {
            throw new Error('La respuesta de equipamientos no tiene un formato válido.');
        }

        if (!resultadoUbicaciones || !Array.isArray(resultadoUbicaciones.result)) {
            throw new Error('La respuesta de ubicaciones no tiene un formato válido.');
        }

        equipamientos = resultadoEquipamientos;
        ubicaciones = resultadoUbicaciones.result;
        renderizarEquipamientos();
    } catch (error) {
        console.error('Error al cargar equipamientos y ubicaciones:', error);
        totalEquipamientos.textContent = 'No disponible';
        listaEquipamientos.replaceChildren(crearEstadoLista('No se pudieron cargar los equipamientos.'));
        mostrarMensaje(error.message || 'Error al cargar los datos.', true);
    }
}

function renderizarEquipamientos() {
    const textoBusqueda = buscadorEquipamientos.value.trim().toLocaleLowerCase('es');
    const criterio = criterioOrdenacion.value;
    const campoOrden = criterio.startsWith('n') ? 'nombre' : 'categoria';
    const direccion = criterio.endsWith('Desc') ? -1 : 1;
    const filtrados = equipamientos
        .filter((equipamiento) => {
            const contenido = [
                equipamiento.nombre,
                equipamiento.descripcion,
                equipamiento.marca,
                equipamiento.modelo,
                equipamiento.categoria,
                equipamiento.nombreUbicacion
            ].join(' ').toLocaleLowerCase('es');

            return contenido.includes(textoBusqueda);
        })
        .sort((a, b) => compararTexto(a[campoOrden], b[campoOrden]) * direccion);

    totalEquipamientos.textContent = filtrados.length === equipamientos.length
        ? `${equipamientos.length} ${equipamientos.length === 1 ? 'equipamiento' : 'equipamientos'}`
        : `${filtrados.length} de ${equipamientos.length} equipamientos`;

    if (filtrados.length === 0) {
        const texto = equipamientos.length === 0
            ? 'No hay equipamientos registrados.'
            : 'No se encontraron equipamientos con esa búsqueda.';
        listaEquipamientos.replaceChildren(crearEstadoLista(texto));
        return;
    }

    const tarjetas = filtrados.map(crearTarjetaEquipamiento);
    listaEquipamientos.replaceChildren(...tarjetas);
}

function crearTarjetaEquipamiento(equipamiento) {
    const tarjeta = document.createElement('article');
    tarjeta.className = 'tarjeta-equipamiento';

    const cabecera = document.createElement('div');
    cabecera.className = 'cabecera-tarjeta-equipamiento';

    const identificacion = document.createElement('div');
    identificacion.className = 'identificacion-equipamiento';

    const id = document.createElement('span');
    id.className = 'id-equipamiento';
    id.textContent = `ID ${equipamiento.id_equipamiento ?? '-'}`;

    const nombre = document.createElement('h2');
    nombre.textContent = equipamiento.nombre || 'Equipamiento sin nombre';

    const categoria = document.createElement('span');
    categoria.className = 'categoria-equipamiento';
    categoria.textContent = formatearCategoria(equipamiento.categoria);

    identificacion.append(id, nombre);
    cabecera.append(identificacion, categoria);

    const descripcion = document.createElement('p');
    descripcion.className = 'descripcion-equipamiento';
    descripcion.textContent = equipamiento.descripcion || 'Sin descripción';

    const detalles = document.createElement('p');
    detalles.className = 'detalles-equipamiento';
    detalles.append(
        crearDetalle('Marca', equipamiento.marca),
        crearDetalle('Modelo', equipamiento.modelo)
    );

    const campoUbicacion = document.createElement('div');
    campoUbicacion.className = 'campo-ubicacion';

    const etiquetaUbicacion = document.createElement('label');
    const idSelect = `ubicacion-${equipamiento.id_equipamiento}`;
    etiquetaUbicacion.htmlFor = idSelect;
    etiquetaUbicacion.textContent = 'Ubicación actual';

    const selectorUbicacion = document.createElement('select');
    selectorUbicacion.id = idSelect;
    selectorUbicacion.dataset.equipamientoId = String(equipamiento.id_equipamiento);
    selectorUbicacion.setAttribute('aria-label', `Cambiar ubicación de ${equipamiento.nombre || 'equipamiento'}`);

    const ubicacionActual = ubicaciones.find((ubicacion) =>
        String(ubicacion.id_ubicacion) === String(equipamiento.id_ubicacion_actual)
    );

    if (!ubicacionActual) {
        const opcionActual = document.createElement('option');
        opcionActual.value = '';
        opcionActual.textContent = equipamiento.nombreUbicacion
            ? `${equipamiento.nombreUbicacion} (no disponible)`
            : 'Ubicación actual no disponible';
        opcionActual.disabled = true;
        opcionActual.selected = true;
        selectorUbicacion.append(opcionActual);
    }

    ubicaciones.forEach((ubicacion) => {
        const opcion = document.createElement('option');
        opcion.value = String(ubicacion.id_ubicacion);
        opcion.textContent = ubicacion.nombre || `Ubicación ${ubicacion.id_ubicacion}`;
        opcion.selected = String(ubicacion.id_ubicacion) === String(equipamiento.id_ubicacion_actual);
        selectorUbicacion.append(opcion);
    });

    if (ubicaciones.length === 0) {
        selectorUbicacion.disabled = true;
        const opcionVacia = document.createElement('option');
        opcionVacia.textContent = 'No hay ubicaciones disponibles';
        opcionVacia.selected = true;
        selectorUbicacion.append(opcionVacia);
    }

    campoUbicacion.append(etiquetaUbicacion, selectorUbicacion);
    tarjeta.append(cabecera, descripcion, detalles, campoUbicacion);

    return tarjeta;
}

function crearDetalle(etiqueta, valor) {
    const detalle = document.createElement('span');
    detalle.textContent = `${etiqueta}: ${valor || '-'}`;
    return detalle;
}

function crearEstadoLista(texto) {
    const estado = document.createElement('p');
    estado.className = 'estado-lista';
    estado.textContent = texto;
    return estado;
}

function compararTexto(a, b) {
    return String(a ?? '').localeCompare(String(b ?? ''), 'es', {
        sensitivity: 'base'
    });
}

function formatearCategoria(categoria) {
    if (!categoria) {
        return 'Sin categoría';
    }

    return String(categoria)
        .toLocaleLowerCase('es')
        .replace(/_/g, ' ')
        .split(' ')
        .map((palabra) => palabra
            ? palabra.charAt(0).toLocaleUpperCase('es') + palabra.slice(1)
            : palabra)
        .join(' ');
}

async function cambiarUbicacion(evento) {
    const selector = evento.target.closest('select[data-equipamiento-id]');

    if (!selector) {
        return;
    }

    const equipamiento = equipamientos.find((elemento) =>
        String(elemento.id_equipamiento) === selector.dataset.equipamientoId
    );
    const ubicacionElegida = ubicaciones.find((ubicacion) =>
        String(ubicacion.id_ubicacion) === selector.value
    );

    if (!equipamiento || !ubicacionElegida) {
        selector.value = String(equipamiento?.id_ubicacion_actual ?? '');
        mostrarMensaje('Selecciona una ubicación existente.', true);
        return;
    }

    if (String(equipamiento.id_ubicacion_actual) === String(ubicacionElegida.id_ubicacion)) {
        return;
    }

    selector.disabled = true;
    ocultarMensaje();

    try {
        const respuesta = await api.equipamientos.actualizar({
            id_equipamiento: equipamiento.id_equipamiento,
            nombre: equipamiento.nombre ?? '',
            descripcion: equipamiento.descripcion ?? '',
            marca: equipamiento.marca ?? '',
            modelo: equipamiento.modelo ?? '',
            categoria: equipamiento.categoria ?? 'OTROS',
            id_ubicacion_actual: ubicacionElegida.id_ubicacion
        });

        if (!respuesta || respuesta.status !== 'success') {
            throw new Error(respuesta?.mensaje || 'No se pudo actualizar la ubicación.');
        }

        equipamiento.id_ubicacion_actual = ubicacionElegida.id_ubicacion;
        equipamiento.nombreUbicacion = ubicacionElegida.nombre;
        renderizarEquipamientos();
        mostrarMensaje(`Ubicación de "${equipamiento.nombre}" actualizada correctamente.`);
    } catch (error) {
        console.error('Error al actualizar la ubicación del equipamiento:', error);
        selector.value = String(equipamiento.id_ubicacion_actual);
        selector.disabled = false;
        mostrarMensaje(error.message || 'No se pudo actualizar la ubicación.', true);
    }
}

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
    mensajeEquipamientos.setAttribute('role', 'status');
}