# Lost Nexus

Sistema web para el registro y seguimiento de objetos perdidos.

## Tecnologías Utilizadas

### Backend
- PHP 7.4+
- Composer (Gestor de dependencias PHP)
- vlucas/phpdotenv (Manejo de variables de entorno)
- MySQL/MariaDB (Base de datos)

### Frontend
- HTML5, CSS3, JavaScript
- Tailwind CSS 4.1.8 (Framework CSS)
- Chart.js 4.5.0 (Gráficos y estadísticas)

### Herramientas de Desarrollo
- Bun/Node.js (Entorno de ejecución)
- NPM (Gestor de paquetes)

## Características Principales

- Registro de objetos perdidos
- Sistema de devolución de objetos
- Gestión de categorías de objetos
- Sistema de puntos de recepción
- Panel de estadísticas con gráficos interactivos
- Sistema de usuarios y autenticación
- Interfaz responsive y moderna

## Estructura del Proyecto

```
lost_nexus/
├── src/                    # Código fuente principal
│   ├── Controllers/        # Controladores de la aplicación
│   ├── Models/             # Modelos de datos
│   ├── Views/              # Vistas y plantillas
│   └── Helpers/            # Clases auxiliares
├── public/                 # Archivos públicos
│   ├── css/                # Hojas de estilo
│   ├── js/                 # Scripts del cliente
│   └── img/                # Imágenes y recursos
├── includes/               # Archivos de configuración
└── dbmodel/               # Modelos de base de datos
```

## Requisitos

- PHP 7.4 o superior
- Composer
- MySQL/MariaDB
- Node.js 14+ o Bun
- NPM o Yarn

## Instalación

1. Clonar el repositorio
2. Instalar dependencias PHP:
```bash
composer install
composer dump-autoload -o
```
3. Copiar el archivo .env.example a .env y configurar las variables de entorno:
```bash
cp .env.example .env
```
4. Configurar la conexión a la base de datos en el archivo .env
5. Instalar dependencias de frontend:
```bash
npm install
# o si usas bun
bun install
```
6. Compilar los assets para desarrollo:
```bash
# Para desarrollo (observa cambios en tiempo real)
npm run dev

# O para producción (versión optimizada)
npm run build
```
7. Iniciar el servidor de desarrollo de Tailwind CSS (en otra terminal):
```bash
npm run tailwind:dev
```

## Comandos de Desarrollo

- `npm run dev` - Compila los assets y observa cambios
- `npm run build` - Compila los assets para producción
- `npm run tailwind:dev` - Inicia el watcher de Tailwind CSS
- `npm run tailwind:build` - Compila y minimiza los estilos para producción

## Configuración del Entorno

1. Asegúrate de que el archivo `.env` contenga las siguientes variables:
   ```
   DB_HOST=localhost
   DB_NAME=nombre_base_datos
   DB_USER=usuario
   DB_PASS=contraseña
   ```

2. Asegúrate de que el servidor web esté configurado para apuntar al directorio `public/`

## Estructura de la Base de Datos

El proyecto utiliza un modelo de base de datos que incluye tablas para:
- Usuarios
- Objetos perdidos
- Categorías
- Puntos de recepción
- Reclamaciones
