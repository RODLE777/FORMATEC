# FORMATEC — Sistema Web de Gestion Academica

Sistema de gestion academica y administrativa para FORMATEC (Cuscatlan
Sur), construido en Laravel 11 siguiendo el prompt maestro y el
esquema de base de datos entregado.

## Requisitos previos (en tu servidor, no en este sandbox)

- PHP >= 8.2 con extensiones: pdo_mysql, mbstring, xml, curl, zip, gd
- Composer 2.x
- Node.js >= 18 y npm
- MySQL >= 8.0
- `mysqldump` disponible en el PATH del servidor (para el modulo de Backups)

## Instalacion paso a paso

```bash
# 1. Instalar dependencias PHP
composer install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Editar .env con tus datos reales de conexion a MySQL
#    DB_DATABASE=formatec
#    DB_USERNAME=formatec_app
#    DB_PASSWORD=tu_password
#    APP_URL=http://192.168.1.100:8000   <- IP de tu servidor en la red local

# 4. Crear la base de datos vacia en MySQL
mysql -u root -p -e "CREATE DATABASE formatec CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Importar la base de datos completa (estructura + triggers + vistas
#    + catalogo geografico + cuenta ROOT, todo en un solo archivo).
#    Opcion A - linea de comandos:
mysql -u root -p formatec < database/formatec_full.sql
#    Opcion B - phpMyAdmin: abre la base "formatec" vacia -> pestaña
#    "Importar" -> selecciona database/formatec_full.sql -> Continuar.
#
#    NO ejecutes `php artisan migrate` si usaste este archivo: ya
#    incluye todo (y deja la tabla `migrations` marcada para que
#    artisan no intente recrear nada si mas adelante corres una
#    migracion nueva).

# 6. Instalar dependencias de frontend y compilar assets
npm install
npm run build

# 7. Levantar el servidor (desarrollo)
php artisan serve --host=0.0.0.0 --port=8000
```

Para produccion, usa un servidor web real (nginx/Apache + PHP-FPM)
apuntando a `public/`, no `artisan serve`.

### Alternativa: instalar con artisan en vez del SQL directo

Si prefieres el flujo estandar de Laravel en vez de importar
`database/formatec_full.sql`, tambien funciona:

```bash
php artisan migrate
php artisan db:seed
```

Ambos caminos dejan exactamente la misma base de datos y la misma
cuenta ROOT inicial — usa el que te resulte mas comodo, no los dos a
la vez.

## Primer acceso

El seeder crea una cuenta ROOT inicial:

- **Correo:** `root@formatec.local`
- **Contrasena:** `CambiarInmediatamente123!`

**Cambia esta contrasena de inmediato** desde `/password` una vez
dentro. Desde ahi, ROOT crea el resto de cuentas (ADMINISTRADOR y
PROFESOR) en el modulo **Usuarios y roles** — no hay registro publico
por diseño.

## Estructura del flujo de trabajo

El sistema esta organizado alrededor del **Grupo** como unidad de
trabajo (ver `NOTA_ARQUITECTURA.md` para el detalle de esta decision).
En resumen:

- **Cursos**, **Profesores**, **Estudiantes**: catalogos/directorios independientes.
- **Grupos**: workspace principal, con pestañas Informacion / Estudiantes
  / Sesiones / Asistencia / Reportes. Todo lo academico de un grupo
  especifico (inscribir, pasar lista, registrar notas, generar
  constancias) ocurre desde ahi.
- **TOTALON**, **Importacion masiva** y **Backups**: viven en
  Administracion porque no pertenecen a un grupo especifico (cruzan
  varios grupos o son operaciones de sistema).

## Roles

| Rol | Alcance |
|---|---|
| ROOT | Todo lo anterior + Usuarios, catalogo geografico, importacion masiva, backups |
| ADMINISTRADOR | Cursos, Grupos, Estudiantes, Profesores, TOTALON |
| PROFESOR | Solo sus propios grupos (filtrado por `profesor_id` a nivel de query y Policy, no solo de menu) |

## Notas tecnicas importantes

- `codigo_formatec` (estudiantes) y `nota_final` (inscripciones) **nunca**
  se escriben desde Eloquent: los calculan los triggers de MySQL
  definidos en `database/migrations/2024_01_01_000011_create_triggers.php`.
  Si migras a otro motor de base de datos en el futuro, esta logica
  debe reimplementarse en la aplicacion.
- El reporte TOTALON se apoya en las vistas SQL `vista_totalon_mensual`
  y `vista_totalon_anual` (migracion `..._000012_create_vistas.php`).
- Los PDFs (`barryvdh/laravel-dompdf`) usan plantillas con CSS plano en
  `resources/views/reportes/`, no Tailwind, porque dompdf no soporta
  utility classes.

## Correcciones aplicadas (ultima revision)

Se detectaron y corrigieron 3 errores reales antes de esta entrega:

1. **`$this->authorize()` no existia** — la clase base `Controller` no
   traia el trait `AuthorizesRequests` de Laravel. Afectaba a Grupos,
   Estudiantes, Inscripciones, Sesiones, Asistencia, Evaluaciones y
   Reportes (cualquier pantalla que validara permisos por rol).
2. **Las Policies nunca se registraban** — `AuthServiceProvider`
   sobrescribia `boot()` sin registrar `GrupoPolicy` / `EstudiantePolicy`.
3. **Backups con ruta inconsistente** — el disco de almacenamiento
   apuntaba a una carpeta distinta de donde el backup realmente se
   guardaba, lo que habria roto la descarga.

Tambien se cambiaron `CACHE_STORE` y `QUEUE_CONNECTION` de `database` a
`file`/`sync` porque esas tablas nunca se migraron (evita otro error
similar al que reportaste).

**Tip para Windows:** evita rutas con espacios como `Nueva carpeta`;
usa algo como `C:\laragon\www\formatec` o `C:\xampp\htdocs\formatec`.

## Este proyecto fue generado con ayuda de Claude

Todo el codigo fue escrito directamente (no es un scaffold generico):
migraciones que replican 1:1 el esquema SQL entregado, Policies que
aplican las reglas de rol de la seccion 5 del prompt maestro, y vistas
que siguen el flujo de trabajo real de FORMATEC en lugar de un CRUD
por tabla. Como el entorno de generacion no tiene PHP/Composer, nunca
se ejecuto `php artisan serve` durante la construccion — pruébalo
localmente siguiendo los pasos de arriba y reporta cualquier ajuste
necesario.
