# Community Directory API

Servicio REST opcional e independiente de la aplicación Laravel. Está implementado con Express, TypeScript y Prisma; utiliza SQLite por defecto.

## Requisitos

- Node.js 20 o superior y npm.

## Instalación y ejecución

Desde `community-directory/`:

```powershell
Copy-Item .env.example .env
npm ci
npm run db:push
npm run seed
npm run dev
```

El servidor escucha en `http://localhost:3000` por defecto. `DATABASE_URL` y `PORT` se pueden cambiar en `.env`.

Para una compilación de producción, ejecuta `npm run build` y luego `npm start`. `npm run db:push` crea o actualiza el esquema de la base SQLite a partir de `prisma/schema.prisma`.

## API

La base de las rutas es `/api/communities`:

| Método | Ruta | Acción |
|---|---|---|
| `GET` | `/api/communities` | Listar comunidades |
| `GET` | `/api/communities/:id` | Consultar una comunidad |
| `POST` | `/api/communities` | Crear una comunidad |
| `PUT` | `/api/communities/:id` | Actualizar una comunidad |
| `DELETE` | `/api/communities/:id` | Eliminar una comunidad |

Para crear o actualizar, envía JSON con `name` y `description`, ambos como texto no vacío. Los identificadores deben ser enteros positivos.
