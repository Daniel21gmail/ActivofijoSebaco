# Sistema de Activos Fijos - Alcaldía de Sébaco

Sistema integral para la gestión y control de activos fijos de la Alcaldía Municipal de Sébaco, Nicaragua.

## 📋 Descripción

Sistema web desarrollado con Laravel y Vue.js (Inertia) que permite la administración completa de activos fijos municipales, incluyendo vehículos, terrenos, equipos y otros bienes. El sistema incluye funcionalidades de registro, seguimiento, depreciación, mantenimiento y generación de reportes.

## ✨ Características Principales

- **Gestión de Activos Fijos**: Registro completo con información general, técnica, financiera y de ubicación
- **Módulos Especializados**: 
  - Vehículos (con datos específicos como placa, año, número de circulación)
  - Terrenos (con información catastral y ubicación geográfica)
- **Sistema de Catálogos**: Marcas, modelos, colores, categorías, proveedores, ubicaciones, etc.
- **Cálculo Automático de Depreciación**: Método de línea recta con vida útil configurable
- **Gestión de Movimientos**: Transferencias entre departamentos y ubicaciones
- **Control de Mantenimientos**: Registro y seguimiento de mantenimientos preventivos y correctivos
- **Bajas de Activos**: Proceso de desincorporación de activos
- **Sistema de Inventarios**: Generación y gestión de inventarios físicos
- **Dashboard Interactivo**: Gráficos y estadísticas en tiempo real
- **Reportes**: 
  - Por categoría
  - Depreciación
  - Historial de movimientos
  - Inventarios
- **Gestión de Usuarios y Roles**: Control de acceso basado en roles
- **Impresión de Etiquetas**: Generación de códigos de barras para activos

## 🛠️ Tecnologías

- **Backend**: Laravel 11.x
- **Frontend**: Vue.js 3 + Inertia.js
- **Estilos**: Tailwind CSS
- **Base de Datos**: MySQL/MariaDB
- **Gráficos**: Chart.js
- **Bundler**: Vite

## 📦 Requisitos del Sistema

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- NPM o Yarn
- MySQL >= 8.0 o MariaDB >= 10.3
- Extensiones PHP requeridas:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Daniel21gmail/ActivofijoSebaco.git
cd ActivofijoSebaco
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de Node.js

```bash
npm install
```

### 4. Configurar variables de entorno

```bash
cp .env.example .env
```

Editar el archivo `.env` y configurar:

```env
APP_NAME="Sistema de Activos Fijos"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=activos_fijos_sebaco
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generar clave de aplicación

```bash
php artisan key:generate
```

### 6. Crear la base de datos

Crear una base de datos MySQL llamada `activos_fijos_sebaco` (o el nombre que configuraste en `.env`)

### 7. Ejecutar migraciones y seeders

```bash
php artisan migrate:fresh --seed
```

Esto creará todas las tablas y poblará la base de datos con datos de ejemplo.

### 8. Crear enlace simbólico para storage

```bash
php artisan storage:link
```

### 9. Compilar assets

**Para desarrollo:**
```bash
npm run dev
```

**Para producción:**
```bash
npm run build
```

### 10. Iniciar el servidor

```bash
php artisan serve
```

El sistema estará disponible en: `http://localhost:8000`

## 👤 Credenciales de Acceso

Después de ejecutar los seeders, puedes acceder con las siguientes credenciales:

**Administrador:**
- Email: `admin@alcaldia.gob.ni`
- Password: `password`

**Usuario de Consulta:**
- Email: `consulta@alcaldia.gob.ni`
- Password: `password`

## 📁 Estructura del Proyecto

```
├── app/
│   ├── Http/Controllers/     # Controladores
│   ├── Models/               # Modelos Eloquent
│   └── ...
├── database/
│   ├── migrations/           # Migraciones de base de datos
│   └── seeders/              # Seeders con datos de ejemplo
├── resources/
│   ├── js/
│   │   ├── Components/       # Componentes Vue reutilizables
│   │   ├── Layouts/          # Layouts de la aplicación
│   │   └── Pages/            # Páginas Vue (Inertia)
│   ├── css/                  # Estilos CSS
│   └── views/                # Vistas Blade (reportes PDF)
├── routes/
│   └── web.php               # Rutas de la aplicación
└── public/                   # Archivos públicos
```

## 🎨 Módulos del Sistema

### Catálogos
- Categorías
- Marcas
- Modelos
- Colores
- Proveedores
- Ubicaciones
- Departamentos
- Estados de Activos
- Fuentes de Financiamiento
- Personal Responsable
- Cargos
- Técnicos

### Activos Fijos
- Registro general de activos
- Vehículos (módulo especializado)
- Terrenos (módulo especializado)
- Visualización de detalles
- Edición y actualización
- Impresión de etiquetas
- Historial de movimientos

### Gestión
- Movimientos/Transferencias
- Mantenimientos
- Bajas de activos
- Inventarios físicos

### Reportes
- Reporte por categoría
- Reporte de depreciación
- Historial de movimientos
- Inventarios

### Administración
- Usuarios
- Roles y permisos
- Perfil de usuario

## 🔧 Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Refrescar base de datos
php artisan migrate:fresh --seed

# Ejecutar tests
php artisan test

# Generar IDE helpers (opcional)
php artisan ide-helper:generate
php artisan ide-helper:models
```

## 📝 Notas de Desarrollo

- El sistema utiliza **Inertia.js** para la comunicación entre Laravel y Vue.js
- Los activos se deprecian automáticamente usando el método de **línea recta**
- La vida útil por defecto es de **5 años**
- El sistema soporta **múltiples tipos de activos** con campos personalizados
- Los reportes se generan en formato **PDF** usando vistas Blade

## 🤝 Contribución

Este es un proyecto interno de la Alcaldía Municipal de Sébaco. Para contribuir:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto es propiedad de la Alcaldía Municipal de Sébaco, Nicaragua.

## 📧 Contacto

Alcaldía Municipal de Sébaco
- Sitio Web: [Pendiente]
- Email: [Pendiente]

---

**Desarrollado para la Alcaldía Municipal de Sébaco** 🏛️
