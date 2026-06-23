# 📘 Guía de Explicación y Presentación del Proyecto: Biblioteca UPB

Esta guía contiene la documentación técnica y funcional detallada del sistema **Biblioteca UPB**. Está diseñada para comprender y exponer la lógica del código, el acceso a datos y las reglas del negocio.

---

## 1. Código PHP y Sintaxis Fundamental

### Sintaxis Básica de PHP en el Proyecto
* **Etiquetas PHP:** Todo el código del servidor se escribe dentro de los bloques `<?php ... ?>`.
* **Variables:** Declaradas con el símbolo `$`, por ejemplo: `$host = 'localhost';`.
* **Estructuras de Control:** 
  * Condicionales (`if`, `else`, `elseif`) para validar formularios, verificar sesiones y controlar permisos de roles.
  * Bucles (`while`) para iterar los conjuntos de datos devueltos por la base de datos:
    ```php
    while ($m = $materiales->fetch_assoc()) { ... }
    ```
* **Manejo de Sesiones (`$_SESSION`):** Usamos `session_start()` al inicio de las páginas para mantener el estado del usuario logueado (`usuario_id`, `nombre`, `email`, `rol`).
* **Modularización (`require_once`):** Se utiliza para incluir plantillas comunes (como [header.php](file:///c:/xampp/htdocs/Proyecto3/includes/header.php) y [footer.php](file:///c:/xampp/htdocs/Proyecto3/includes/footer.php)) y configuraciones globales, deteniendo la ejecución del script si el archivo no existe y evitando la duplicidad de importación.

### Sentencias Preparadas (Prepared Statements)
Para evitar vulnerabilidades de inyección SQL, las consultas con parámetros dinámicos se procesan de forma segura:
1. **Preparación (`prepare`):** Se envía la plantilla SQL con marcadores de posición `?`.
2. **Asociación de Parámetros (`bind_param`):** Se definen los tipos de datos (`s` para strings, `i` para enteros) y se asocian las variables correspondientes.
3. **Ejecución (`execute`):** El motor ejecuta la consulta habiendo sanitizado previamente las entradas.
4. **Obtención de Resultados (`get_result`):** Extrae el conjunto de registros devueltos.

---

### 🔍 Énfasis en la Cadena de Conexión

El archivo [conexion.php](file:///c:/xampp/htdocs/Proyecto3/config/conexion.php) establece el canal de comunicación con MySQL mediante la API orientada a objetos de **MySQLi**:

```php
$host = 'localhost';
$dbname = 'biblioteca_upb';
$user = 'root';
$password = '';

// Constructor de la clase mysqli
$conexion = new mysqli($host, $user, $password, $dbname);

// Validación de la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Codificación de caracteres
$conexion->set_charset("utf8mb4");
```

#### Parámetros del Constructor `new mysqli()`
* **`$host` (`localhost`):** El dominio o IP del servidor de base de datos.
* **`$user` (`root`):** El nombre del usuario administrador de MySQL en el servidor local.
* **`$password` (`""`):** La contraseña del usuario. En la instalación por defecto de XAMPP, el usuario `root` no tiene contraseña.
* **`$dbname` (`biblioteca_upb`):** La base de datos específica seleccionada para operar.

#### Control e Interrupción de Errores
La propiedad `$conexion->connect_error` almacena la descripción del fallo en caso de que la conexión no se logre establecer. Si existe algún error:
1. Se llama a la función nativa `die()`.
2. Se interrumpe la ejecución del código PHP de manera inmediata.
3. Se muestra un mensaje descriptivo para evitar pantallas en blanco difíciles de depurar.

---

## 2. Funciones de la Página y Pseudocódigo de Consultas

A continuación, se detalla la lógica de cada página de la aplicación y cómo interactúan con la base de datos.

### 🔑 Módulo de Autenticación

#### A. Inicio de Sesión ([login.php](file:///c:/xampp/htdocs/Proyecto3/auth/login.php))
* **Función:** Permite a administradores, estudiantes y externos ingresar al sistema mediante su correo o su código único.
* **Pseudocódigo de Consulta:**
  ```sql
  OBTENER id, nombre, email, password, rol
  DESDE usuarios
  DONDE email = ? O codigo = ?
  ```
* **Lógica del PHP:** Si encuentra el registro, compara la contraseña ingresada con la encriptada en la base de datos usando `password_verify()`. Si coincide, inicializa la sesión redirigiendo según el rol (`admin` o `usuario`).

#### B. Registro de Usuarios ([register.php](file:///c:/xampp/htdocs/Proyecto3/auth/register.php))
* **Función:** Permite registrar usuarios asignándoles los roles de `'estudiante'` o `'externo'`.
* **Pseudocódigo de Consultas:**
  1. *Verificar disponibilidad del email:*
     ```sql
     OBTENER id DESDE usuarios DONDE email = ?
     ```
  2. *Insertar nuevo usuario:*
     ```sql
     INSERTAR EN usuarios (nombre, email, password, rol, codigo)
     VALORES (?, ?, ?, ?, ?)
     ```
* **Lógica del PHP:** Encripta la contraseña usando `password_hash($password, PASSWORD_DEFAULT)` antes de insertarla en la base de datos.

---

### 🛠️ Módulo de Administración (Admin)

#### A. Panel de Control / Dashboard ([index.php](file:///c:/xampp/htdocs/Proyecto3/admin/index.php))
* **Función:** Muestra estadísticas rápidas del sistema al administrador.
* **Pseudocódigo de Consultas:**
  ```sql
  -- Cantidad total de materiales
  SELECCIONAR CONTAR(*) DESDE materiales;
  
  -- Cantidad de usuarios (excluyendo admins)
  SELECCIONAR CONTAR(*) DESDE usuarios DONDE rol != 'admin';
  
  -- Préstamos activos
  SELECCIONAR CONTAR(*) DESDE prestamos DONDE estado = 'activo';
  
  -- Préstamos vencidos
  SELECCIONAR CONTAR(*) DESDE prestamos 
  DONDE estado = 'activo' Y fecha_devolucion_esperada < AHORA();
  ```

#### B. Gestión de Inventario/Materiales ([listar.php](file:///c:/xampp/htdocs/Proyecto3/admin/materiales/listar.php))
* **Función:** Lista los recursos bibliográficos del sistema con soporte para búsquedas y filtros por categoría.
* **Pseudocódigo de Consulta (Listado completo con Autores concatenados):**
  ```sql
  SELECCIONAR m.id, m.titulo, c.nombre COMO categoria, e.nombre COMO editorial,
             m.anio_publicacion, m.cantidad_total, m.cantidad_disponible,
             AGRUPAR_CONCATENANDO(nombre_completo_autor) COMO autores
  DESDE materiales m
  UNIR categorias c EN m.categoria_id = c.id
  UNIR_IZQ editoriales e EN m.editorial_id = e.id
  UNIR_IZQ material_autor ma EN m.id = ma.material_id
  UNIR_IZQ autores a EN ma.autor_id = a.id
  AGRUPAR POR m.id
  ORDENAR POR m.titulo ASC
  ```

#### C. Registro de Materiales ([crear.php](file:///c:/xampp/htdocs/Proyecto3/admin/materiales/crear.php))
* **Función:** Permite agregar un material y asociarlo a múltiples autores.
* **Pseudocódigo de Consulta:**
  1. *Registrar el material:*
     ```sql
     INSERTAR EN materiales (titulo, categoria_id, editorial_id, isbn, anio_publicacion, cantidad_total, cantidad_disponible, descripcion)
     VALORES (?, ?, ?, ?, ?, ?, ?, ?)
     ```
  2. *Insertar autores (Relación N:M):*
     ```sql
     -- Por cada autor seleccionado (obteniendo el id insertado en el paso anterior)
     INSERTAR EN material_autor (material_id, autor_id) VALORES (id_generado, id_autor)
     ```

#### D. Registro de Préstamos ([registrar.php](file:///c:/xampp/htdocs/Proyecto3/admin/prestamos/registrar.php))
* **Función:** Registra la entrega física de un material a un usuario específico.
* **Pseudocódigo de Consultas y Lógica:**
  1. *Verificar disponibilidad:*
     ```sql
     OBTENER cantidad_disponible DESDE materiales DONDE id = ?
     ```
  2. *Verificar límite del usuario (Máx 3 activos):*
     ```sql
     OBTENER CONTAR(*) DESDE prestamos DONDE usuario_id = ? Y estado = 'activo'
     ```
  3. *Registrar préstamo:*
     ```sql
     INSERTAR EN prestamos (usuario_id, material_id, fecha_devolucion_esperada)
     VALORES (?, ?, ?)
     ```
  4. *Reducir inventario:*
     ```sql
     ACTUALIZAR materiales SET cantidad_disponible = cantidad_disponible - 1 WHERE id = ?
     ```

#### E. Devolución de Préstamo ([devolver.php](file:///c:/xampp/htdocs/Proyecto3/admin/prestamos/devolver.php))
* **Función:** Procesa la entrega del material de vuelta a la biblioteca.
* **Pseudocódigo de Consultas:**
  1. *Marcar como devuelto:*
     ```sql
     ACTUALIZAR prestamos SET estado = 'devuelto', fecha_devolucion_real = AHORA() WHERE id = ?
     ```
  2. *Incrementar disponibilidad:*
     ```sql
     ACTUALIZAR materiales SET cantidad_disponible = cantidad_disponible + 1 WHERE id = ?
     ```

---

### 👨‍🎓 Módulo de Usuario

#### A. Catálogo ([catalogo.php](file:///c:/xampp/htdocs/Proyecto3/usuario/catalogo.php))
* **Función:** Permite al usuario final buscar materiales utilizando filtros de texto y categorías.
* **Pseudocódigo de Consulta:**
  ```sql
  SELECCIONAR m.id, m.titulo, c.nombre COMO categoria, m.cantidad_disponible,
             AGRUPAR_CONCATENANDO(nombre_completo_autor) COMO autores
  DESDE materiales m
  UNIR categorias c EN m.categoria_id = c.id
  UNIR_IZQ material_autor ma EN m.id = ma.material_id
  UNIR_IZQ autores a EN ma.autor_id = a.id
  DONDE m.titulo COMO ? O a.nombre COMO ? O a.apellido COMO ?
  AGRUPAR POR m.id
  ORDENAR POR m.titulo ASC
  ```

#### B. Solicitar Préstamo Autogestionado ([solicitar_prestamo.php](file:///c:/xampp/htdocs/Proyecto3/usuario/solicitar_prestamo.php))
* **Función:** El estudiante o externo puede reservar directamente un material disponible si no supera su cupo de préstamos.
* **Lógica y Pseudocódigo:**
  Realiza las mismas validaciones que el módulo de administración (`cantidad_disponible > 0` y `prestamos_activos < 3`), efectúa la inserción en la tabla `prestamos` con una fecha de devolución estimada de 14 días en el futuro y actualiza la cantidad de material disponible decrementándolo en 1.

---

## 3. Aspectos Extra (Seguridad e Integridad)

### Seguridad de Credenciales
El sistema no almacena las contraseñas en texto plano. Se implementa el estándar industrial de hashing mediante **Bcrypt** a través de las funciones nativas:
* `password_hash($contraseña, PASSWORD_DEFAULT)` para el registro.
* `password_verify($contraseña, $hash)` para el inicio de sesión.

### Integridad Referencial de Datos (Relaciones)
El esquema de datos maneja relaciones que previenen la pérdida de consistencia lógica:
* **Relación N:M (Materiales <-> Autores):** Se modela a través de la tabla intermedia `material_autor` que asocia claves foráneas (`material_id` y `autor_id`) con la instrucción `ON DELETE CASCADE`. Esto asegura que al eliminar un libro, se limpien de forma automática sus referencias sin dejar huellas huérfanas en la base de datos.
* **Control de Stock:** Las cantidades disponibles varían en tiempo real mediante consultas combinadas (operaciones de inserción de préstamo y actualización del material se ejecutan consecutivamente).
