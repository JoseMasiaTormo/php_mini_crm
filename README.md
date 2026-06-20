# PHP Mini CRM

Un mini CRM (Customer Relationship Manager) construido con PHP puro para aprender y profundizar en las bases del lenguaje sin frameworks ni librerías externas.

## ¿Qué es un CRM?

Un CRM es una herramienta para gestionar las relaciones con clientes. Este proyecto implementa una versión simplificada que permite gestionar tareas asociadas a clientes o proyectos.

## Funcionalidades planeadas

- Autenticación de usuarios (login / logout)
- Listado de tareas
- Creación de nuevas tareas
- Base de datos con MySQL/SQLite

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
- Se conecta a bases de datos con PDO o MySQLi

---

## Estructura de archivos

```
php_mini_crm/
│
├── setup.php           # Script de inicialización de la base de datos
│
└── tasks/
    ├── db.php          # Conexión a la base de datos (incluido en los demás archivos)
    ├── index.php       # Página principal: listado de tareas
    ├── login.php       # Formulario de autenticación
    ├── logout.php      # Cierra la sesión y redirige al login
    └── new_task.php    # Formulario para crear una nueva tarea
```

### Descripción de cada archivo

| Archivo | Rol |
|---|---|
| `setup.php` | Crea las tablas en la base de datos. Se ejecuta una sola vez. |
| `tasks/db.php` | Establece la conexión PDO. El resto de archivos lo incluyen con `require`. |
| `tasks/index.php` | Lista todas las tareas. Requiere sesión iniciada. |
| `tasks/login.php` | Gestiona el formulario GET (mostrar) y POST (validar credenciales). |
| `tasks/logout.php` | Destruye la sesión con `session_destroy()` y redirige. |
| `tasks/new_task.php` | Muestra el formulario de nueva tarea y procesa el POST para insertarla. |

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
