# 🐝 Sistema de Control y Trazabilidad Apícola - FUNDACOVI

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![PWA](https://img.shields.io/badge/PWA_Ready-Offline_First-5A0FC8?style=for-the-badge)

Aplicación Web Progresiva (PWA) desarrollada para el control de calidad, seguimiento de la producción y promoción de productos apícolas asociados a la **Fundación Colombia de Vida (FUNDACOVI)** en Norte de Santander.

Este proyecto fue desarrollado bajo una arquitectura **Offline-First**, permitiendo a los productores agrícolas registrar sus datos en zonas rurales sin conexión a internet, garantizando la sincronización posterior mediante Service Workers e IndexedDB.

## 🚀 Características Principales

- **Arquitectura Híbrida Offline-First:** Captura de datos en campo sin internet mediante IndexedDB (Dexie.js) con sincronización encolada inteligente.
- **Control de Acceso Basado en Roles (RBAC):** Vistas y permisos aislados para Administradores y Apicultores (Multi-tenencia de datos).
- **Control de Calidad Ciego (Antifraude):** Evaluación matemática automática de lotes de miel basada en normativas del INVIMA.
- **Analítica Avanzada (BI):** Dashboards dinámicos generados con Chart.js y exportación directa a PDF.
- **Catálogo Web Público:** Landing Page promocional para los productos con integración de correo SMTP.

## 🛠️ Stack Tecnológico

- **Backend:** Laravel 13, Livewire 3, MySQL / PostgreSQL
- **Frontend:** Tailwind CSS v4, Alpine.js, Blade
- **Almacenamiento Local (PWA):** Dexie.js, Vite PWA Plugin
- **Control de Calidad (Pruebas):** PHPUnit (Caja Blanca), OWASP ZAP (Seguridad).

## ⚙️ Requisitos de Instalación

- PHP >= 8.3
- Composer >= 2.7
- Node.js >= 20.x y NPM
- MySQL >= 8.0

## 💻 Guía de Despliegue Local

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/ArthurZambrano19131015/webfundacovi.git
   cd webfundacovi
2. **Instalar dependencias:**
   composer install
   npm install --legacy-peer-deps
3. **Configuración de Variables de Entorno:**
   Duplica el archivo .env.example, renómbralo a .env y configura tu conexión a la base de datos MySQL.
   cp .env.example .env
   php artisan key:generate
4. **Base de Datos y Datos Semilla (Seeders):**
   php artisan migrate:fresh --seed
Nota: El seeder creará automáticamente los roles del sistema y la cuenta del Administrador.
5. **Compilar PWA para pruebas (Crítico para modo Offline):**
   npm run build
6. **Servir la aplicación:**
   Puedes usar Laravel Herd o el servidor integrado:
   php artisan serve
(Para probar el modo Offline en un teléfono celular real, se recomienda exponer el entorno local mediante túneles seguros como herd share o ngrok, dado que los Service Workers exigen contextos HTTPS).

👨‍💻 Autores
Trabajo de grado - Ingeniería de Sistemas (Universidad de Santander UDES, Campus Cúcuta):
Arthur Edilsson Zambrano Niño
Víctor Alfonso Plazas Rodríguez