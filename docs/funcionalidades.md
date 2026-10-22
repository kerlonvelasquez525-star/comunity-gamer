# Nexus Community: documentación funcional

## 1. Propósito del proyecto

Nexus Community es una plataforma social para jugadores. Permite descubrir comunidades, publicar noticias y contenido, conversar en canales o por chat privado, organizar amistades, reportar incidencias y administrar equipos.

La aplicación tiene tres espacios principales:

- **Portada pública**: accesible desde `/`; presenta noticias oficiales, métricas generales y acceso al catálogo público.
- **Catálogo público**: permite explorar comunidades y noticias sin pertenecer a un equipo.
- **Espacio de equipo**: agrupa dashboard, noticias, comunidades, foros, canales, soporte y administración bajo una URL con el formato `/{current_team}/...`.

La aplicación está construida con Laravel, Blade, Livewire, Flux, Fortify, Eloquent y Vite.

## 2. Requisitos técnicos

- PHP `^8.3`.
- Laravel `^13.17`.
- Node.js y npm.
- MySQL, SQLite u otra conexión soportada por Laravel.
- Composer.

Dependencias principales:

- `laravel/fortify`: autenticación, verificación, contraseña, 2FA y passkeys.
- `livewire/livewire`: navegación y componentes interactivos.
- `livewire/flux`: componentes visuales y navegación de la interfaz.
- `tailwindcss` y `vite`: estilos y compilación frontend.
- `larastan`, PHPUnit y Pint: análisis, pruebas y formato.

## 3. Instalación y puesta en marcha

### Instalación inicial

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan storage:link
```

En PowerShell, el equivalente de copiar el entorno puede ser:

```powershell
Copy-Item .env.example .env
```

Configura en `.env` como mínimo:

- `APP_URL`.
- `APP_KEY`.
- `DB_CONNECTION`.
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` si se usa MySQL.
- Configuración de correo si se necesita verificación o recuperación por email.

### Desarrollo

Servidor Laravel:

```bash
php artisan serve
```

Vite en otra terminal:

```bash
npm run dev
```

El script agrupado del proyecto es:

```bash
composer run dev
```

Después abre `http://127.0.0.1:8000` o la URL indicada por Artisan.

### Datos de demostración

Para conservar datos existentes:

```bash
php artisan db:seed
```

Para reconstruir la base completa en desarrollo:

```bash
php artisan migrate:fresh --seed
```

La cuenta de prueba es:

```text
Correo: test@example.com
Contraseña: password
```

La contraseña anterior es únicamente para desarrollo. Debe cambiarse o deshabilitarse en entornos reales.

## 4. Tipos de usuario y contexto de equipo

### Visitante

Puede:

- Ver la portada.
- Leer noticias oficiales.
- Explorar comunidades públicas.
- Ver la página pública de soporte.
- Intentar comentar o solicitar ingreso; el flujo le pedirá autenticarse cuando sea necesario.

### Usuario autenticado y verificado

Además puede:

- Seleccionar y administrar sus equipos según su rol.
- Crear publicaciones, comentarios y contenido permitido.
- Solicitar ingreso a comunidades públicas.
- Usar chat privado, amistades, notificaciones y asistente.
- Crear problemas o reportes desde el equipo.

### Propietario y administrador

Puede realizar acciones administrativas sobre el equipo y el contenido autorizado:

- Editar o eliminar el equipo según la policy.
- Gestionar miembros e invitaciones.
- Crear, modificar o eliminar comunidades.
- Aceptar o rechazar solicitudes.
- Gestionar canales, foros y contenido moderable.
- Responder reportes de soporte y actualizar reportes de moderación.

### Miembro

Puede consultar el contenido del equipo y crear contenido social permitido. No recibe automáticamente permisos para modificar el equipo ni administrar miembros.

## 5. Autenticación y seguridad

Fortify gestiona el ciclo de autenticación:

- Registro en `/register`.
- Inicio de sesión en `/login`.
- Cierre de sesión mediante `/logout`.
- Verificación de correo en `/email/verify`.
- Recuperación en `/forgot-password` y `/reset-password/{token}`.
- Confirmación de contraseña en `/user/confirm-password`.
- Configuración de contraseña, 2FA y códigos de recuperación.
- Registro y uso de passkeys/WebAuthn.

Las rutas sensibles usan middleware de autenticación, verificación y, cuando corresponde, confirmación de contraseña. Hay límites de intentos para login, 2FA y passkeys.

En producción, las reglas de contraseña son más estrictas que en desarrollo: longitud mínima, mayúsculas, minúsculas, números, símbolos y comprobación contra contraseñas filtradas.

## 6. Configuración de cuenta

La sección `/settings` redirige a las opciones de cuenta. Las páginas principales son:

- `/settings/profile`: nombre, correo y datos de perfil.
- `/settings/appearance`: apariencia y preferencias visuales.
- `/settings/security`: contraseña, sesiones, 2FA y passkeys.
- `/settings/teams`: equipos del usuario.
- `/settings/teams/{team}`: edición de un equipo autorizado.
- `/settings/communities`: creación de comunidades del equipo actual.
- `/.well-known/passkey-endpoints`: metadatos de passkeys.

La eliminación de cuenta y cambios de seguridad requieren validaciones adicionales para evitar acciones accidentales.

## 7. Portada pública

Ruta:

```text
GET /
Nombre: home
Vista: resources/views/nexus_community.blade.php
Controlador: PublicNewsController@index
```

La portada presenta:

- Marca y navegación pública.
- Acceso a noticias oficiales.
- Acceso al catálogo de comunidades.
- Acceso a soporte público.
- Carrusel de noticias oficiales destacadas.
- Métricas de usuarios activos, publicaciones y telemetría.
- Acciones distintas para visitantes y usuarios autenticados.

Las noticias mostradas en la portada son registros de `noticias` con `team_id = null` y `es_oficial = true`.

## 8. Noticias públicas

Rutas principales:

```text
GET  /noticias-publicas
     public-noticias.index
GET  /noticias-oficiales/refresh
     official-news.refresh
POST /noticias-oficiales/{noticia}/comentarios
     official-news.comments.store
PATCH /noticias-oficiales/{noticia}/comentarios/{comentario}
     official-news.comments.update
DELETE /noticias-oficiales/{noticia}/comentarios/{comentario}
     official-news.comments.destroy
```

Características:

- Listado paginado de noticias oficiales.
- Lectura sin cuenta.
- Comentarios para usuarios autenticados.
- Si un visitante intenta comentar, el contenido puede guardarse temporalmente en sesión para continuar después del login o registro.
- El autor puede editar o eliminar sus comentarios según la policy.
- La actualización rápida devuelve JSON para refrescar noticias sin cargar toda la página.

## 9. Dashboard de equipo

Ruta:

```text
GET /{current_team}/dashboard
Nombre: dashboard
Controlador: HomeController@index
Vista: resources/views/dashboard.blade.php
```

`{current_team}` es el slug del equipo, por ejemplo:

```text
/nexus-demo-community/dashboard
```

El dashboard consulta la base de datos y muestra:

- Noticias recientes del equipo y noticias oficiales públicas.
- Conteo de noticias disponibles.
- Conteo de comunidades.
- Conteo de casos abiertos o en progreso.
- Notificaciones recientes del usuario.
- Enlaces rápidos a Noticias, Comunidades, Soporte y Asistente.
- Vista previa de ranking y contacto.

Los valores de las tarjetas no están escritos de forma fija en la vista: los calcula `HomeController` con consultas Eloquent.

## 10. Equipos

Las rutas de equipo usan esta cadena de middleware:

```text
auth
verified
SetTeamUrlDefaults
EnsureTeamMembership
```

El flujo es:

1. Laravel obtiene el slug de `{current_team}`.
2. `SetTeamUrlDefaults` establece los valores de URL usados por enlaces internos.
3. `EnsureTeamMembership` comprueba que el usuario pertenezca al equipo.
4. Si no pertenece, se redirige a su equipo actual o al selector de equipos.
5. El controlador aplica además la policy concreta de la operación.

Permisos de equipo disponibles:

- `team:update`.
- `team:delete`.
- `member:add`.
- `member:update`.
- `member:remove`.
- `invitation:create`.
- `invitation:cancel`.

Los roles principales son `owner`, `admin` y `member`.

## 11. Comunidades

### Catálogo público

```text
GET /comunidad-publica
Nombre: public-comunidad.index
Controlador: PublicCommunityController@index
Vista: resources/views/public-community.blade.php
```

Permite explorar comunidades con filtros por:

- Nombre.
- Juego.
- Plataforma.
- Región.
- Idioma.
- Modalidad.
- Horario.
- Tipo.
- Nivel.
- Rango.
- Estado.

El resultado se pagina y ordena por cantidad de miembros.

Una persona autenticada puede solicitar ingreso mediante:

```text
POST /comunidad-publica/{comunidad}/solicitar
Nombre: public-comunidad.apply
```

La solicitud se rechaza si la comunidad está cerrada, llena o si el usuario ya pertenece a ella. Los estados habituales son `pendiente`, `aceptada` y `rechazada`.

### Catálogo dentro de un equipo

```text
GET /{current_team}/comunidad
Nombre: comunidad.index
Controlador: ComunidadController@index
```

La URL interna mantiene autenticación, verificación y pertenencia al equipo, pero comparte la presentación del catálogo público. Puede mostrar comunidades del equipo y comunidades públicas abiertas.

### Administración

El resource de comunidades proporciona:

```text
GET    /{current_team}/comunidad
GET    /{current_team}/comunidad/create
POST   /{current_team}/comunidad
GET    /{current_team}/comunidad/{comunidad}
GET    /{current_team}/comunidad/{comunidad}/edit
PUT    /{current_team}/comunidad/{comunidad}
PATCH  /{current_team}/comunidad/{comunidad}
DELETE /{current_team}/comunidad/{comunidad}
```

Acciones adicionales:

```text
PATCH /{current_team}/comunidad/{comunidad}/miembros/{user}/rol
POST  /{current_team}/comunidad/{comunidad}/miembros/invitar
PATCH /{current_team}/comunidad/{comunidad}/transferir
PATCH /{current_team}/comunidad/{comunidad}/solicitudes/{solicitud}/aceptar
PATCH /{current_team}/comunidad/{comunidad}/solicitudes/{solicitud}/rechazar
```

El creador o un administrador puede modificar la comunidad. La aceptación de una solicitud incorpora al usuario como miembro de la comunidad y, según el flujo, al equipo asociado.

## 12. Noticias del equipo

Resource:

```text
/{current_team}/noticias
```

Operaciones:

```text
GET    .../noticias
GET    .../noticias/create
POST   .../noticias
GET    .../noticias/{noticia}
GET    .../noticias/{noticia}/edit
PUT    .../noticias/{noticia}
PATCH  .../noticias/{noticia}
DELETE .../noticias/{noticia}
```

Nombres de ruta: `noticias.index`, `noticias.create`, `noticias.store`, `noticias.show`, `noticias.edit`, `noticias.update` y `noticias.destroy`.

Los comentarios de una noticia usan:

```text
POST   /{current_team}/noticias/{noticia}/comentarios
PATCH  /{current_team}/noticias/{noticia}/comentarios/{comentario}
DELETE /{current_team}/noticias/{noticia}/comentarios/{comentario}
```

La policy comprueba pertenencia al equipo y autoría. Los administradores tienen permisos adicionales para moderar contenido.

## 13. Publicaciones, comentarios y reacciones

Publicaciones:

```text
/{current_team}/publicaciones
```

Usan las acciones REST estándar: `index`, `create`, `store`, `show`, `edit`, `update` y `destroy`.

Reacciones:

```text
POST /{current_team}/publicaciones/{publicacion}/reacciones
Nombre: publicaciones.reacciones.toggle
```

La reacción funciona como interruptor: si el usuario no ha reaccionado, se crea; si ya existe, se elimina o cambia según la implementación del controlador.

Comentarios generales:

```text
/{current_team}/comentarios
```

Los autores pueden modificar su contenido. Los administradores pueden moderar según las policies.

## 14. Foros, hilos y aportes

Resources disponibles:

```text
/{current_team}/foros
/{current_team}/hilos
/{current_team}/aportes
```

Flujo habitual:

1. Un equipo contiene comunidades.
2. Una comunidad puede tener foros.
3. Un foro contiene hilos.
4. Un hilo contiene aportes o respuestas.
5. Cada controlador valida que el recurso pertenezca al equipo actual.
6. La policy comprueba autoría y rol antes de editar o eliminar.

Las tres entidades exponen las acciones REST estándar: listar, crear, guardar, ver, editar, actualizar y eliminar.

## 15. Canales y mensajes de comunidad

Canales:

```text
/{current_team}/canales
```

Mensajes:

```text
GET    /{current_team}/canales/{canal}/mensajes
POST   /{current_team}/canales/{canal}/mensajes
DELETE /{current_team}/mensajes/{mensaje}
```

Los canales pertenecen a una comunidad. El controlador valida que la comunidad y el canal correspondan al equipo actual.

Los miembros pueden leer y publicar mensajes. El autor puede eliminar su mensaje y los administradores pueden moderar según la policy.

## 16. Chat privado y asistente

Chat entre usuarios:

```text
GET  /chat/{recipient}
     chat.show
POST /chat/{recipient}
     chat.store
```

El chat:

- Carga únicamente mensajes entre el usuario actual y el destinatario.
- Ordena por fecha de envío.
- Marca como leídos los mensajes recibidos.
- Impide enviar mensajes a la propia cuenta.
- Guarda el contenido cifrado mediante el cast del modelo `MensajePrivado`.

Asistente local:

```text
GET  /chatbot
     chatbot.index
POST /chat/bot
     chat.bot.answer
```

El asistente responde con reglas locales sobre privacidad, seguridad, equipos, noticias, chat y reportes. No guarda las preguntas. El endpoint tiene límite de 30 solicitudes por minuto.

## 17. Amistades

```text
GET    /amistades
POST   /amistades
PATCH  /amistades/{amistad}/accept
PATCH  /amistades/{amistad}/reject
DELETE /amistades/{amistad}
```

El flujo permite enviar una solicitud, aceptarla, rechazarla o eliminar una amistad existente. Las operaciones están restringidas a los participantes de la relación.

## 18. Soporte e incidencias

La interfaz visible usa el término **Soporte**. Las rutas y modelos conservan nombres técnicos históricos relacionados con `problemas`.

Resource de incidencias del equipo:

```text
/{current_team}/problemas
```

Acciones estándar:

```text
GET    .../problemas
GET    .../problemas/create
POST   .../problemas
GET    .../problemas/{problema}
GET    .../problemas/{problema}/edit
PUT    .../problemas/{problema}
PATCH  .../problemas/{problema}
DELETE .../problemas/{problema}
```

Estados habituales:

- `abierto`.
- `en_progreso`.
- `resuelto`.
- `cerrado`.

Prioridades habituales:

- `baja`.
- `media`.
- `alta`.
- `critica`.

Soporte público:

```text
GET /soporte-publico
Nombre: public-problemas.index
```

También existe un flujo de reportes de soporte para casos más detallados:

```text
/{current_team}/reportes-soporte
```

Incluye respuestas administrativas y votación de reportes. El creador puede consultar su reporte y los administradores pueden responder.

## 19. Moderación

Los reportes de moderación se gestionan mediante:

```text
GET   /{current_team}/reportes-moderacion
POST  /{current_team}/reportes-moderacion
GET   /{current_team}/reportes-moderacion/{reporteModeracion}
PATCH /{current_team}/reportes-moderacion/{reporteModeracion}/estado
```

Crear reportes requiere pertenecer al equipo. Consultar detalles y cambiar estados requiere permisos administrativos.

## 20. Juegos, plataformas, idiomas y etiquetas

Cada módulo tiene un resource bajo el equipo actual:

```text
/{current_team}/juegos
/{current_team}/plataformas
/{current_team}/idiomas
/{current_team}/etiquetas
```

Cada uno ofrece `index`, `create`, `store`, `show`, `edit`, `update` y `destroy`.

Los juegos se relacionan con plataformas e idiomas mediante tablas intermedias. La lectura requiere pertenecer al equipo y la modificación normalmente exige rol `admin`.

## 21. Notificaciones

```text
GET    /notificaciones
PATCH  /notificaciones/{notificacion}/read
PATCH  /notificaciones/read-all
DELETE /notificaciones/{notificacion}
```

Cada notificación pertenece a un usuario. El controlador verifica la propiedad antes de marcarla, eliminarla o marcar todas como leídas.

El dashboard muestra las notificaciones recientes y diferencia visualmente las pendientes de las ya leídas.

## 22. Modelo de datos principal

Entidades principales:

- `User`: cuenta, autenticación y propietario de contenido.
- `Team`: equipo, slug, roles y pertenencia.
- `Membership`: relación usuario-equipo y rol.
- `Comunidad`: grupo público o interno de jugadores.
- `ComunidadSolicitud`: solicitud de ingreso.
- `noticias`: noticias oficiales o del equipo.
- `NoticiasComentario`: comentarios de noticias oficiales.
- `Publicacion`: publicaciones sociales.
- `Comentarios`: comentarios de publicaciones.
- `Reaccion`: reacción de un usuario a una publicación.
- `Foro`, `Hilo` y `Aporte`: conversación estructurada.
- `Canal` y `Mensaje`: chat de una comunidad.
- `MensajePrivado`: chat directo cifrado.
- `Amistad`: solicitudes y relaciones entre usuarios.
- `Problemas`: incidencias del equipo.
- `ReporteSoporte`: reportes dirigidos al soporte.
- `ReporteModeracion`: reportes para moderación.
- `Notificacion`: actividad dirigida a un usuario.
- `Juego`, `Plataforma`, `Idioma` y `Etiqueta`: catálogos relacionados con contenido.

Las foreign keys y las tablas intermedias se definen en `database/migrations`. Los modelos usan relaciones Eloquent para controlar pertenencia, autores, miembros y contenido dependiente.

## 23. Datos demo del DatabaseSeeder

`database/seeders/DatabaseSeeder.php` prepara un escenario completo:

- Cuenta `test@example.com` verificada.
- Usuarios adicionales.
- Equipo compartido `nexus-demo-community`.
- Usuarios asociados al equipo.
- Tres conversaciones privadas de ejemplo para la cuenta de prueba.
- Noticias oficiales con textos y comentarios distintos.
- Publicaciones, comentarios y reacciones.
- Diez comunidades demo.
- Al menos diez miembros por comunidad.
- Canal `general` y mensajes de canal.
- Foros, hilos y aportes.
- Idiomas, plataformas y juegos.
- Problemas abiertos o en progreso.
- Notificaciones.
- Amistades aceptadas.

El seeder utiliza operaciones como `firstOrCreate`, `updateOrCreate` y comprobaciones de existencia para evitar duplicar parte de los datos cuando se ejecuta varias veces. Aun así, los datos demo deben considerarse sintéticos y no deben cargarse en producción.

## 24. Autorización y policies

Las policies están en `app/Policies`. Se registran desde los proveedores de la aplicación y se invocan mediante `Gate::authorize()` y directivas Blade como `@can`.

La autorización tiene dos niveles:

1. **Contexto**: middleware comprueba autenticación, verificación y pertenencia al equipo.
2. **Operación**: la policy comprueba si el usuario puede ver, crear, actualizar, eliminar, administrar o moderar el recurso.

No debe sustituirse una comprobación de policy por ocultar un botón en Blade. La vista solo mejora la experiencia; el controlador debe seguir protegiendo la operación.

## 25. Frontend y navegación

Vite compila estas entradas principales:

- `resources/css/app.css` y `resources/js/app.js`.
- `resources/css/style_th.css` para la portada y vistas públicas.
- `resources/css/dashboard.css` para el dashboard.
- `resources/css/chat.css` para chat.
- `resources/css/noticias.css` para noticias.
- `resources/css/encabezado.css` para el header.

Las páginas internas usan el layout de aplicación. La portada y las páginas públicas usan el layout público. Los enlaces que cruzan del layout interno al público hacen navegación completa para evitar mezclar temporalmente los estilos de ambos layouts. La navegación Livewire se mantiene entre páginas del mismo contexto.

## 26. Pruebas y calidad

Ejecutar todas las pruebas:

```bash
php artisan test
```

Ejecutar una prueba concreta:

```bash
php artisan test --filter=DashboardTest
```

Formato y revisión de estilo:

```bash
composer lint
composer lint:check
```

Análisis estático:

```bash
composer types:check
```

Comprobaciones útiles:

```bash
php artisan route:list
php artisan migrate:status
php artisan optimize:clear
php -l app/Http/Controllers/HomeController.php
```

## 27. Limpieza y diagnóstico

Cuando una vista no refleja cambios:

```bash
php artisan optimize:clear
php artisan view:clear
npm run build
```

Cuando una ruta no responde:

```bash
php artisan route:list --path=dashboard
php artisan route:list --name=comunidad
```

Cuando una relación parece vacía:

1. Comprueba el `team_id` del registro.
2. Comprueba el `current_team_id` del usuario.
3. Comprueba que exista la fila en `team_members` o la tabla intermedia correspondiente.
4. Revisa el middleware `EnsureTeamMembership`.
5. Revisa la policy aplicada por el controlador.

Cuando el dashboard muestra ceros:

- Comprueba que la base haya sido sembrada.
- Comprueba que el usuario esté entrando al equipo esperado.
- Revisa las consultas de `HomeController`.
- Ejecuta `php artisan tinker` para contar registros directamente.

## 28. Estructura de carpetas relevante

```text
app/
  Actions/              Acciones de Fortify, equipos y flujos específicos.
  Concerns/             Traits compartidos, incluidos equipos y slugs.
  Enums/                Roles y permisos.
  Http/Controllers/     Controladores web.
  Http/Middleware/      Contexto y pertenencia a equipos.
  Livewire/             Componentes interactivos.
  Models/               Modelos Eloquent.
  Notifications/        Notificaciones de Laravel.
  Policies/             Autorización por modelo.
  Providers/            Registro de servicios, Fortify y policies.
  Rules/                Reglas de validación.
  Services/             Servicios de dominio, incluido el asistente.

database/
  factories/            Datos para pruebas.
  migrations/           Esquema de base de datos.
  seeders/              Datos demo.

resources/
  css/                  Estilos de aplicación, portada y módulos.
  js/                   Interactividad del frontend.
  views/                Vistas Blade y layouts.

routes/
  web.php               Rutas públicas y de equipo.
  settings.php          Rutas de configuración.

storage/
  app/public/           Archivos públicos subidos.
  framework/            Cachés y vistas compiladas.
  logs/                 Logs de Laravel.
```

## 29. Recomendaciones de operación

- No usar `migrate:fresh --seed` sobre una base con información real.
- No conservar la contraseña demo en producción.
- Mantener `APP_DEBUG=false` en producción.
- Ejecutar `npm run build` antes de publicar cambios frontend.
- Ejecutar pruebas después de modificar rutas, policies o seeders.
- Usar `php artisan storage:link` cuando se suban imágenes o archivos.
- Revisar permisos de los directorios `storage` y `bootstrap/cache` en servidores Linux.
- No confiar en la interfaz para seguridad: la autorización debe permanecer en middleware, policies y controladores.

## 30. Archivos de referencia

- `routes/web.php`: rutas públicas, autenticadas y de equipo.
- `routes/settings.php`: configuración de cuenta y equipos.
- `app/Http/Controllers`: comportamiento de cada módulo.
- `app/Models`: relaciones y persistencia.
- `app/Policies`: autorización.
- `database/migrations`: esquema.
- `database/seeders/DatabaseSeeder.php`: escenario demo.
- `tests/Feature`: flujos funcionales.
- `comandosLaravel.md`: referencia de comandos Artisan.
