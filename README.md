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





