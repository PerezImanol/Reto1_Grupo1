# CentroGestor — Gestión de equipamientos (Reto 1 · Grupo 1)

Aplicación web para gestionar el equipamiento informático de un centro (portátiles, sobremesas, periféricos, material audiovisual…), controlar **dónde está cada equipo en cada momento** y gestionar las **notificaciones/incidencias** que abre el personal.

Usa una arquitectura **Cliente / Servidor**:

- **Cliente** (`Client/`): HTML + CSS + JavaScript (jQuery). Se comunica con el servidor mediante `fetch` y JSON.
- **Servidor** (`Server/`): API REST en PHP (patrón Controller → DAO → Modelo) sobre MySQL/MariaDB. La sesión se mantiene con sesiones de PHP (cookie).

---

## Funcionalidades

La app tiene dos roles, y cada uno entra en una interfaz distinta tras iniciar sesión.

### Administrador (`ADMIN`) — interfaz de escritorio (`Client/pages/`)

| Pantalla | Qué permite hacer |
|---|---|
| Equipamientos | Alta, edición y borrado de equipos; búsqueda por columna, filtro por categoría y ordenación; acceso al historial de cada equipo |
| Ubicaciones | Alta, edición y borrado de ubicaciones (aulas, despachos, laboratorio…) |
| Historial de ubicaciones | Consulta de los traslados de cada equipo (ubicación, fecha de inicio y de fin) |
| Usuarios | Alta, edición y borrado de usuarios, con asignación de rol |
| Notificaciones | Consulta de las notificaciones de cada usuario y cambio de su estado |
| Perfil | Datos del usuario y cambio de contraseña |

### Conserje (`USER`) — interfaz móvil (`Client/pagesUser/`)

| Pantalla | Qué permite hacer |
|---|---|
| Equipamientos | Consultar equipos y **cambiar su ubicación** (es lo único que puede modificar) |
| Ubicaciones | Consultar las ubicaciones |
| Notificaciones | Crear notificaciones sobre un equipo y consultar **solo las suyas** |
| Mi perfil | Datos del usuario y cambio de contraseña |

### Reglas de negocio (implementadas en la base de datos con triggers)

- Todo equipo nuevo **empieza siempre en la ubicación «Almacén»**, y se abre automáticamente su primera fila de historial.
- Al cambiar la ubicación de un equipo se **cierra el registro anterior** del historial y se abre uno nuevo.
- Solo puede haber **una ubicación abierta** (sin `fecha_fin`) por equipo.
- La ubicación **«Almacén» no se puede borrar**.
- Solo los usuarios con rol `USER` pueden crear notificaciones, y siempre nacen en estado `PENDIENTE`.
- Una notificación en estado `REALIZADA` **no se puede eliminar**.
- Estados de notificación: `PENDIENTE`, `EN_PROCESO`, `REALIZADA`.
- Categorías de equipo: `PORTATIL`, `SOBREMESA`, `PERIFERICO`, `AUDIOVISUAL`, `OTROS`.

---

## Estructura del proyecto

```
Reto1_Grupo1/
├── Client/
│   ├── index.html            # Redirige a pages/login.html
│   ├── pages/                # Pantallas de administrador
│   ├── pagesUser/            # Pantallas de conserje (versión móvil)
│   ├── js/                   # Lógica de escritorio + api.js y auth.js (comunes)
│   ├── jsMovil/              # Lógica de la versión móvil
│   ├── css/
│   └── img/
└── Server/
    ├── public/               # Endpoints de la API (*API.php) y Autorizacion.php
    ├── controller/           # Controladores
    ├── dao/                  # Acceso a datos (ConexionDB.php = credenciales de la BD)
    ├── model/                # Entidades y enums (Rol, Estado, EnumCategoria…)
    ├── view/                 # Vistas auxiliares de las entidades
    └── sql/
        ├── Reto2DAW.sql              # Esquema: base de datos, tablas y triggers
        └── Valores de las tablas.sql # Datos de prueba
```

> **Importante:** `Client/` y `Server/` deben estar en la **misma carpeta padre** del servidor web. El cliente localiza la API con la ruta relativa `../../Server/public/` desde `Client/js/`.

---

## Requisitos

- **PHP 8.1 o superior** (se usan `enum`s nativos) con la extensión **mysqli**.
- **MySQL 8.x o MariaDB**.
- Un servidor web con PHP, por ejemplo **Apache** (XAMPP, WAMP, MAMP o LAMP).
- Conexión a internet en el navegador: las páginas cargan **jQuery desde su CDN**.

---

## Instalación y configuración

### 1. Descargar el proyecto

```bash
git clone https://github.com/PerezImanol/Reto1_Grupo1.git
cd Reto1_Grupo1
git checkout clienteJagoba
```

Copia (o clona) la carpeta del proyecto dentro del directorio público del servidor web, por ejemplo:

- XAMPP: `C:\xampp\htdocs\Reto1_Grupo1\`
- Linux (Apache): `/var/www/html/Reto1_Grupo1/`

### 2. Crear la base de datos

El script crea la base de datos `reto_2daw`, las tablas, los triggers y la ubicación obligatoria «Almacén».

```bash
mysql -u root -p < Server/sql/Reto2DAW.sql
```

> Si prefieres usar phpMyAdmin, importa el archivo desde la pestaña *Importar*.
>
> ⚠️ La primera sentencia del archivo es `select * from usuarios;`, que falla en una instalación limpia porque la tabla aún no existe. **Bórrala (o coméntala) antes de ejecutar el script.**

Después carga los datos de prueba (usuarios, ubicaciones, equipamientos y notificaciones):

```bash
mysql -u root -p < "Server/sql/Valores de las tablas.sql"
```

### 3. Configurar las credenciales de la base de datos

Edita `Server/dao/ConexionDB.php` y ajusta los parámetros de `new mysqli(...)` a tu entorno:

```php
$conexion = new mysqli(
    "localhost",   // host del servidor MySQL
    "root",        // usuario
    "",            // contraseña
    "reto_2daw"    // nombre de la base de datos
);
```

Por defecto está configurado para un XAMPP estándar (usuario `root`, sin contraseña). Si tu MySQL tiene contraseña o está en otro host, cámbialo aquí.

### 4. Crear un usuario con el que entrar

Las contraseñas se guardan con `password_hash` y se comprueban con `password_verify`. Los usuarios del script de datos de prueba llevan **hashes de ejemplo que no corresponden a ninguna contraseña real**, así que con ellos no se puede iniciar sesión.

Genera un hash para la contraseña que quieras:

```bash
php -r "echo password_hash('TuContraseña', PASSWORD_DEFAULT), PHP_EOL;"
```

y asígnalo a un usuario existente:

```sql
USE reto_2daw;
UPDATE usuarios
   SET password_hash = '<hash generado>'
 WHERE nombre_usuario = 'agarcia';   -- Administrador de los datos de prueba
```

Usuarios de prueba incluidos: `agarcia` (ADMIN) e `imartinez`, `mrodriguez`, `jsanchez`, `lfernandez` (USER).

Alternativa: inserta directamente un usuario nuevo con su hash:

```sql
INSERT INTO usuarios (nombre, apellidos, nombre_usuario, password_hash, rol)
VALUES ('Admin', 'Prueba', 'admin', '<hash generado>', 'ADMIN');
```

### 5. Arrancar la aplicación

1. Arranca **Apache** y **MySQL** (en XAMPP, desde su panel de control).
2. Abre en el navegador:

   ```
   http://localhost/Reto1_Grupo1/Client/
   ```

   (Ajusta el nombre de la carpeta si la has llamado de otra forma.) `index.html` te redirige a la pantalla de inicio de sesión.

---

## Uso

1. Inicia sesión con tu usuario y contraseña.
2. Según tu rol, la app te lleva a la interfaz correspondiente:
   - `ADMIN` → `Client/pages/equipamientos.html`
   - `USER` → `Client/pagesUser/equipamientosMovil.html`
3. Navega con el menú superior. Para salir, usa la opción de cerrar sesión.

---

## API

Todas las respuestas son JSON con los campos `exito` (booleano) y `status`. Los datos se devuelven en `result` o `usuario`; los errores, en `mensaje` o `error`. Base: `http://localhost/Reto1_Grupo1/Server/public/`.

| Endpoint | Métodos | Notas |
|---|---|---|
| `LoginAPI.php` | `POST` (login), `GET` (sesión actual), `DELETE` (logout) | `POST` espera `{"username", "password"}` |
| `EquipamientoAPI.php` | `GET`, `POST`, `PUT`, `DELETE` | `GET` con sesión. `POST` y `DELETE` solo `ADMIN`. `PUT`: `ADMIN` edita todo; `USER` solo la ubicación |
| `NotificacionAPI.php` | `GET`, `POST`, `PUT`, `DELETE` | Requiere sesión. `USER` solo ve y gestiona las suyas; `ADMIN` consulta por `id` o `id_user` |
| `UbicacionAPI.php` | `GET`, `POST`, `PUT`, `DELETE` | Sin control de sesión en el servidor por ahora |
| `UsuarioAPI.php` | `GET`, `POST`, `PUT`, `DELETE` | Sin control de sesión en el servidor por ahora |
| `HistoricoUbicacionAPI.php` | `GET`, `POST`, `PUT`, `DELETE` | Sin control de sesión en el servidor por ahora |

Parámetros habituales: `?id=<n>` para un registro concreto, `?id_equipamiento=<n>` en el histórico y `?id_user=<n>` en notificaciones. Cada archivo `*API.php` incluye al principio, en un comentario, ejemplos del JSON esperado en `POST` y `PUT`.

Ejemplo — crear un equipamiento (como `ADMIN`):

```json
POST /Server/public/EquipamientoAPI.php
{
  "nombre": "Portátil Aula 101",
  "descripcion": "Portátil del profesor",
  "marca": "Lenovo",
  "modelo": "ThinkPad E15",
  "categoria": "PORTATIL",
  "id_ubicacion_actual": 1
}
```

> Aunque se indique una ubicación, el trigger de la base de datos coloca siempre el equipo nuevo en «Almacén».

---

## Modelo de datos

```
usuarios ──< notificaciones
ubicaciones ──< equipamientos (ubicación actual)
equipamientos >──< ubicaciones   (N:M, resuelta con historico_ubicaciones)
```

| Tabla | Contenido |
|---|---|
| `usuarios` | Nombre, apellidos, nombre de usuario único, hash de contraseña y rol (`ADMIN`/`USER`) |
| `ubicaciones` | Nombre único y descripción |
| `equipamientos` | Nombre, descripción, marca, modelo, categoría y ubicación actual |
| `historico_ubicaciones` | Equipo, ubicación, `fecha_inicio` y `fecha_fin` (`NULL` = ubicación actual) |
| `notificaciones` | Título, descripción, fecha de creación, estado y usuario que la creó |

---

## Problemas frecuentes

- **«Internal Server Error: Could not reach the database»**: revisa las credenciales de `Server/dao/ConexionDB.php` y que MySQL esté arrancado.
- **No puedo iniciar sesión con los usuarios de prueba**: sus hashes son de ejemplo. Genera uno real (paso 4).
- **La pantalla se queda en blanco o las llamadas a la API fallan**: comprueba que `Client/` y `Server/` están en la misma carpeta y que accedes por `http://localhost/...`, no abriendo los HTML con doble clic (`file://`), porque la sesión necesita un servidor.
- **Error 404 tras iniciar sesión como `USER` en Linux**: `Client/js/login.js` redirige a `../pagesUSER/...`, pero la carpeta se llama `pagesUser`. En Windows funciona porque no distingue mayúsculas; en Linux hay que corregir la ruta en `login.js` a `../pagesUser/equipamientosMovil.html`.
- **Error al importar `Reto2DAW.sql`**: elimina la primera línea (`select * from usuarios;`) y, si la base de datos `reto_2daw` ya existe, bórrala antes de volver a importar.
