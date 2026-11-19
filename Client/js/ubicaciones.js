const nameField = document.getElementById("name");
const descField = document.getElementById("desc");
const btnAnadir = document.getElementById("btnAnadir");
const btnBorrar = document.getElementById("btnBorrar");
const btnCancelar = document.getElementById("btnCancelar");
const form = document.getElementById("formEquipamiento");
const sectionForm = document.getElementById("seccionModificar");
const titleForm = document.getElementById("tituloFormulario");
const tbody = document.getElementById("tablaEquipamientos");
const message = document.getElementById("mensajeUbicaciones");
const modal = document.getElementById("modalConfirmarBorrado");
const btnCancelarBorrado = document.getElementById("btnCancelarBorrado");
const btnConfirmarBorrado = document.getElementById("btnConfirmarBorrado");
const buscarUbicacion = document.getElementById("buscarUbicacion");

let ALL_LOCATIONS = [];
let selectedLocation = null;
let adding = false;
let resolveDelete = null;

document.addEventListener("DOMContentLoaded", loadLocations);

async function loadLocations() {
    try {
        const data = await api.ubicaciones.obtenerTodos();
        if (!data.exito) throw new Error(data.mensaje || "No se pudieron cargar las ubicaciones.");
        ALL_LOCATIONS = data.result || [];
        renderLocations();
    } catch (error) {
        ALL_LOCATIONS = [];
        renderLocations();
        showMessage("Error al cargar las ubicaciones: " + error.message, true);
    }
}

function renderLocations() {
    tbody.innerHTML = "";

    const texto = buscarUbicacion.value.trim().toLowerCase();

    const ubicacionesFiltradas = ALL_LOCATIONS.filter(location =>
        (location.nombre || "").toLowerCase().includes(texto) ||
        (location.descripcion || "").toLowerCase().includes(texto)
    );

    if (ubicacionesFiltradas.length === 0) {
        const mensaje = ALL_LOCATIONS.length === 0
            ? "No hay ubicaciones disponibles."
            : "No se encontraron ubicaciones.";

        tbody.innerHTML = `<tr><td colspan="3" class="tabla-vacia">${mensaje}</td></tr>`;
        return;
    }

    ubicacionesFiltradas.forEach(location => {
        const row = document.createElement("tr");
        const checkCell = document.createElement("td");
        const checkbox = document.createElement("input");

        checkbox.type = "checkbox";
        checkbox.className = "checkbox-fila";
        checkbox.checked = selectedLocation?.id_ubicacion == location.id_ubicacion;
        checkbox.setAttribute("aria-label", "Seleccionar ubicación " + location.nombre);

        checkbox.addEventListener("change", () => {
            if (checkbox.checked) selectLocation(location);
            else resetForm();
        });

        checkCell.appendChild(checkbox);
        row.appendChild(checkCell);

        [location.nombre, location.descripcion].forEach(value => {
            const cell = document.createElement("td");
            cell.textContent = value ?? "";
            row.appendChild(cell);
        });

        if (checkbox.checked) row.classList.add("fila-seleccionada");

        row.addEventListener("click", event => {
            if (event.target !== checkbox) selectLocation(location);
        });

        tbody.appendChild(row);
    });
}

buscarUbicacion.addEventListener("input", renderLocations);

function selectLocation(location) {
    selectedLocation = location;
    adding = false;
    hideMessage();
    nameField.value = location.nombre || "";
    descField.value = location.descripcion || "";
    titleForm.textContent = "Modificar ubicación";
    btnBorrar.disabled = false;
    showForm();
    renderLocations();
}

function showForm() {
    sectionForm.hidden = false;
    form.hidden = false;
    window.setTimeout(() => sectionForm.scrollIntoView({ behavior: "smooth", block: "start" }), 0);
}

function resetForm() {
    selectedLocation = null;
    adding = false;
    form.reset();
    sectionForm.hidden = true;
    form.hidden = true;
    btnBorrar.disabled = true;
    renderLocations();
}

btnAnadir.addEventListener("click", () => {
    selectedLocation = null;
    adding = true;
    hideMessage();
    form.reset();
    titleForm.textContent = "Añadir ubicación";
    btnBorrar.disabled = true;
    showForm();
    renderLocations();
    nameField.focus();
});

btnCancelar.addEventListener("click", resetForm);

form.addEventListener("submit", async event => {
    event.preventDefault();
    hideMessage();

    const nombre = nameField.value.trim();
    const descripcion = descField.value.trim();
    if (!nombre || !descripcion) return showMessage("Rellena todos los campos.", true);

    const duplicada = ALL_LOCATIONS.some(location =>
        location.nombre.trim().toLowerCase() === nombre.toLowerCase() &&
        location.id_ubicacion != selectedLocation?.id_ubicacion
    );
    if (duplicada) return showMessage("Ya existe una ubicación con ese nombre.", true);

    const wasAdding = adding;
    const info = { nombre, descripcion };

    try {
        const result = wasAdding
            ? await api.ubicaciones.crear(info)
            : await api.ubicaciones.actualizar({ ...info, id_ubicacion: selectedLocation.id_ubicacion });

        if (!result.exito) throw new Error(result.mensaje || "No se pudieron guardar los cambios.");
        resetForm();
        await loadLocations();
        showMessage(result.mensaje || (wasAdding ? "Ubicación añadida correctamente." : "Ubicación modificada correctamente."));
    } catch (error) {
        showMessage("Error al guardar: " + error.message, true);
    }
});

btnBorrar.addEventListener("click", async () => {
    if (!selectedLocation || !(await confirmDelete())) return;

    try {
        const result = await api.ubicaciones.eliminar(selectedLocation.id_ubicacion);
        if (!result.exito) throw new Error(result.mensaje || "No se pudo eliminar la ubicación.");
        resetForm();
        await loadLocations();
        showMessage(result.mensaje || "Ubicación eliminada correctamente.");
    } catch (error) {
        showMessage("Error al eliminar: " + error.message, true);
    }
});

function confirmDelete() {
    modal.hidden = false;
    return new Promise(resolve => { resolveDelete = resolve; });
}

function closeDelete(value) {
    modal.hidden = true;
    if (resolveDelete) resolveDelete(value);
    resolveDelete = null;
}

btnCancelarBorrado.addEventListener("click", () => closeDelete(false));
btnConfirmarBorrado.addEventListener("click", () => closeDelete(true));
modal.addEventListener("click", event => { if (event.target === modal) closeDelete(false); });

function showMessage(text, error = false) {
    message.textContent = text;
    message.classList.toggle("mensaje-error", error);
    message.hidden = false;
}

function hideMessage() {
    message.hidden = true;
    message.textContent = "";
    message.classList.remove("mensaje-error");
}
