// Cargar los equipamientos al abrir la página
document.addEventListener('DOMContentLoaded', cargarEquipamientos);

async function cargarEquipamientos() {
  const tabla = document.getElementById('tablaEquipamientos');

  try {
    const equipamientos = await api.equipamientos.obtenerTodos();

    tabla.innerHTML = '';

    if (!Array.isArray(equipamientos) || equipamientos.length === 0) {
        mostrarMensaje(tabla, 'No hay equipamientos registrados');
        return;
    }

    equipamientos.forEach(equipamiento => {
      const fila = document.createElement('tr');

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

      tabla.appendChild(fila);
    });

  } catch (error) {
      console.error('Error al cargar los equipamientos:', error);
      mostrarMensaje(tabla, 'Error al cargar los equipamientos');
  }
}

function mostrarMensaje(tabla, mensaje) {
  tabla.innerHTML = '';

  const fila = document.createElement('tr');
  const celda = document.createElement('td');

  celda.colSpan = 6;
  celda.textContent = mensaje;
  celda.classList.add('tabla-vacia');

  fila.appendChild(celda);
  tabla.appendChild(fila);
}

const buscador = document.getElementById('search');
const columnaFiltro = document.getElementById('columnaFiltro');
const btnBuscar = document.getElementById('btnBuscar');

// Cambiar el texto de ayuda al elegir una columna
columnaFiltro.addEventListener('change', () => {
    const columna = columnaFiltro.options[columnaFiltro.selectedIndex].text;

    buscador.placeholder = `Buscar por ${columna.toLowerCase()}...`;

    buscador.value = '';
});

// Buscar al pulsar el botón
btnBuscar.addEventListener('click', buscarEquipamientos);

// También buscar al pulsar Enter
buscador.addEventListener('keydown', (evento) => {
    if (evento.key === 'Enter') {
        buscarEquipamientos();
    }
});

function buscarEquipamientos() {
    const texto = buscador.value.trim().toLowerCase();
    const columna = columnaFiltro.value;
    const filas = document.querySelectorAll('#tablaEquipamientos tr');

    filas.forEach(fila => {
        // Ignorar filas que muestran mensajes en vez de equipamientos
        if (fila.cells.length !== 6) {
            return;
        }

        const indice = {
            nombre: 0,
            descripcion: 1,
            marca: 2,
            modelo: 3,
            categoria: 4,
            nombreUbicacion: 5
        };

        const valor = fila.cells[indice[columna]].textContent.toLowerCase();

        fila.style.display = valor.includes(texto) ? '' : 'none';
    });
}