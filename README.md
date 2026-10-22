# Nexus Community

Nexus Community es una plataforma social para jugadores: permite descubrir comunidades, publicar contenido, conversar, crear equipos, participar en foros y reportar problemas.

La aplicación principal está construida con Laravel, Livewire, Fortify, Blade y Vite. El servicio opcional `community-directory/` es un API independiente en TypeScript y Prisma.

La descripción de funcionalidades está en [docs/funcionalidades.md](docs/funcionalidades.md) y el detalle técnico en [docs/informe-proyecto.md](docs/informe-proyecto.md).

## Requisitos

- PHP 8.3 o superior y Composer.
- Node.js y npm.
- MySQL/MariaDB, o SQLite con la extensión PHP correspondiente.

## Instalación local

Desde la raíz del repositorio:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Configura la conexión de base de datos en `.env` antes de migrar. El ejemplo usa MySQL; para SQLite, establece `DB_CONNECTION=sqlite` y `DB_DATABASE` a una ruta de archivo SQLite existente o nueva.

```powershell
php artisan migrate --seed
npm ci
npm run build
php artisan storage:link
```

El seeder carga datos de demostración; úsalo únicamente en un entorno de desarrollo o prueba. Para ejecutar la aplicación:

```powershell
php artisan serve
```

En otra terminal, `npm run dev` inicia Vite con recarga de recursos frontend.

## Verificación

```powershell
composer test
npm run build
```

`composer test` ejecuta Pint, PHPStan y la suite PHPUnit. La compilación frontend se valida por separado con Vite.

## Directorio de comunidades opcional

El API independiente tiene su propia instalación, configuración de base de datos y comandos. Consulta [community-directory/README.md](community-directory/README.md); no es necesario para ejecutar Laravel.
