# PHP Mini CRM

Un mini CRM (Customer Relationship Manager) construido con PHP puro para aprender y profundizar en las bases del lenguaje sin frameworks ni librerías externas.

## ¿Qué es un CRM?

Un CRM es una herramienta para gestionar las relaciones con clientes. Este proyecto implementa una versión simplificada que permite gestionar tareas asociadas a un usuario autenticado.

## Funcionalidades implementadas

- Registro de usuarios (con inicio de sesión automático al crear la cuenta)
- Autenticación de usuarios (login / logout)
- Listado de tareas propias del usuario
- Creación de nuevas tareas
- Eliminación de tareas (con protección: solo puedes borrar las tuyas)
- Base de datos SQLite mediante PDO
- Diseño consistente en todas las vistas (Inter, degradado morado, cards)

---

## ¿Cómo funciona PHP?

PHP es un lenguaje de programación del lado del servidor. Cuando el navegador solicita una página `.php`, el servidor ejecuta el código y devuelve HTML al navegador. El usuario nunca ve el código PHP, solo el resultado.

```
Navegador  →  petición HTTP  →  Servidor (Apache/Nginx)
                                      ↓
                               Interpreta el .php
                                      ↓
Navegador  ←  respuesta HTML  ←  Devuelve el resultado
```

### Ciclo de una petición típica

1. El usuario accede a `/tasks/index.php`
2. El servidor ejecuta el archivo PHP
3. PHP consulta la base de datos si es necesario
4. PHP genera el HTML dinámico con los datos
5. El navegador recibe y renderiza el HTML

### Características clave de PHP

- Las variables empiezan con `$`: `$nombre = "Juan";`
- Se mezcla con HTML usando `<?php ... ?>`
- Accede a datos del formulario con `$_POST` y `$_GET`
- Gestiona sesiones con `$_SESSION`
- Se conecta a bases de datos con PDO

---

## Estructura de archivos

```
php_mini_crm/
│
├── setup.php           # Script de inicialización de la base de datos
├── index.php           # Redirección al panel de tareas
├── tasks.db            # Base de datos SQLite (generada por setup.php)
│
└── tasks/
    ├── db.php          # Conexión PDO (incluido en los demás archivos)
    ├── index.php       # Panel principal: listado y creación de tareas
    ├── login.php       # Formulario de autenticación
    ├── register.php    # Formulario de registro (inicia sesión al crear cuenta)
    ├── logout.php      # Cierra la sesión y redirige al login
    ├── new_task.php    # Procesa el POST de nueva tarea e inserta en BD
    └── remove_task.php # Elimina una tarea por GET (?id=X), protegido por user_id
```

### Descripción de cada archivo

| Archivo | Rol |
|---|---|
| `setup.php` | Crea las tablas `users` y `tasks` en la base de datos. Se ejecuta una sola vez. |
| `tasks/db.php` | Establece la conexión PDO con SQLite. El resto de archivos lo incluyen con `require_once`. |
| `tasks/index.php` | Lista las tareas del usuario activo e incluye el formulario de nueva tarea. Requiere sesión. |
| `tasks/login.php` | Gestiona el formulario GET (mostrar) y POST (validar credenciales). Muestra errores si algún campo está vacío o las credenciales son incorrectas. |
| `tasks/register.php` | Registra un nuevo usuario, hashea la contraseña con `password_hash` e inicia sesión automáticamente redirigiendo a `index.php`. |
| `tasks/logout.php` | Destruye la sesión con `session_destroy()` y redirige al login. |
| `tasks/new_task.php` | Recibe el POST del formulario de tarea, inserta en BD con el `user_id` de la sesión y redirige a `index.php`. |
| `tasks/remove_task.php` | Elimina la tarea cuyo `id` llega por GET, comprobando que pertenece al usuario activo antes de borrar. |

---

## Cómo ejecutarlo

Necesitas un servidor PHP local. La forma más sencilla:

```bash
# Desde la raíz del proyecto
php -S localhost:8000

# Inicializar la base de datos (solo la primera vez)
php setup.php
```

Luego abre el navegador en `http://localhost:8000/tasks/login.php`.
