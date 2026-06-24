# Instrucciones de Ejecución - BIBLIOTECA UPB

---

### 1. Requisitos Previos
* Tener instalado **XAMPP** (incluye Apache y MySQL).
* Asegurarse de que la extensión `mysqli` esté habilitada en el archivo `php.ini` de XAMPP (por lo general, viene habilitada por defecto).

---

### 2. Configurar la Base de Datos
1. Abre **phpMyAdmin** (`http://localhost/phpmyadmin/`) o tu gestor de bases de datos MySQL preferido.
2. Crea una nueva base de datos llamada **`biblioteca_upb`**:
   ```sql
   CREATE DATABASE biblioteca_upb;
   ```
3. Selecciona la base de datos recién creada e importa el archivo **`biblioteca_upb.sql`** ubicado en la carpeta `sql` del proyecto.
   O ejecuta el script mediante la terminal de MySQL:
   ```bash
   mysql -u root -p biblioteca_upb < sql/biblioteca_upb.sql
   ```
   *(Por defecto en XAMPP, el usuario es `root` sin contraseña).*
   Esto creará las tablas `usuarios`, `categorias`, `editoriales`, `autores`, `materiales`, `material_autor` y `prestamos`, además de insertar los datos iniciales y de prueba.

---

### 3. Ubicación en XAMPP
* Asegúrate de que la carpeta del proyecto esté ubicada en el directorio `htdocs` de tu instalación de XAMPP:
  `C:\xampp\htdocs\Proyecto3`

---

### 4. Acceder a la Aplicación
1. Inicia los módulos **Apache** y **MySQL** desde el Panel de Control de XAMPP.
2. Abre tu navegador web y accede a la siguiente dirección:
   ```
   http://localhost/Proyecto3/
   ```
3. Inicia sesión con las credenciales de prueba del administrador:
   * **Email o Código:** `admin@upb.edu`
   * **Contraseña:** `admin123`

---

### 5. Cadena de Conexión (MySQLi)

La conexión entre PHP y MySQL se realiza en el archivo **`config/conexion.php`** mediante la clase **`mysqli`**. A continuación se desglosa cada parte:

```php
$host     = 'localhost';
$dbname   = 'biblioteca_upb';
$user     = 'root';
$password = '';

$conexion = new mysqli($host, $user, $password, $dbname);
```

#### ¿Qué representan estos parámetros?
La conexión a través del constructor `new mysqli(...)` recibe los siguientes argumentos:

| Parte | Valor | Descripción |

| **host** | `'localhost'` | Dirección del servidor de la base de datos. `'localhost'` significa que está en la misma máquina. |
| **user** | `'root'` | El usuario de MySQL con permisos sobre la base de datos (por defecto es `'root'` en XAMPP). |
| **password** | `''` | Contraseña del usuario (por defecto está vacía en XAMPP). |
| **dbname** | `'biblioteca_upb'` | Nombre de la base de datos a la que nos conectamos. |

#### Manejo de errores
```php
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
```
Esta condición verifica si la conexión falló a través del atributo `connect_error`. Si es así, se detiene la ejecución del script con `die()` mostrando el detalle del error.


### 📁 Estructura Esencial del Proyecto
* **`config/conexion.php`**: Conexión a la base de datos MySQL (con credenciales: usuario `root` y contraseña vacía).
* **`index.php`**: Página de aterrizaje del sistema que redirige al usuario o al administrador si ya tienen sesión iniciada, o les ofrece opciones para iniciar sesión o registrarse.
* **`auth/login.php`**: Formulario de inicio de sesión y autenticación de credenciales de usuario.
* **`auth/register.php`**: Formulario de registro de nuevos usuarios (estudiantes y externos).
* **`auth/logout.php`**: Cierra la sesión activa y destruye los datos de sesión.
* **`admin/`**: Panel de administración. Contiene la gestión de:
  * **Materiales** (`admin/materiales/`): Alta, baja, modificación y listado de libros, papers, tesis y proyectos de grado.
  * **Préstamos** (`admin/prestamos/`): Registro de préstamos y devoluciones de materiales bibliográficos.
  * **Autores y Editoriales** (`admin/autores/`, `admin/editoriales/`): Listado e inserción de nuevos autores y editoriales.
  * **Usuarios** (`admin/usuarios/`): Visualización de los usuarios registrados en el sistema.
* **`usuario/`**: Panel del usuario. Contiene las vistas del catálogo, detalles de materiales, préstamos activos y la solicitud de nuevos préstamos.
* **`includes/`**: Componentes reutilizables como la cabecera (`header.php`), pie de página (`footer.php`) y verificación de sesión (`auth_check.php`).
* **`sql/biblioteca_upb.sql`**: Script SQL para crear las tablas e insertar los datos de prueba.

### Integrantes

Fidel Aguilar
Sebastián Armijo
Boris Calcina
Manizeh Monteville
Santiago Muñoz

### Video

https://youtu.be/jczla4oBpu8