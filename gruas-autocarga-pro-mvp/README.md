# Grúas Autocarga Pro — base MVP

## 1. Requisitos locales
- XAMPP con Apache + MySQL
- PHP 8.1+ recomendado
- Navegador
- Git + GitHub

## 2. Instalación local
1. Copia la carpeta del proyecto dentro de `htdocs`.
2. En phpMyAdmin importa `database/schema.sql`.
3. Revisa `src/config.php`.
4. Abre `http://localhost/gruas-autocarga-pro/public/`.
5. Para crear el administrador, ejecuta una sola vez `database/seed_admin.php` desde un entorno local y después elimina el archivo.

## 3. Orden de desarrollo
1. Autenticación y autorización.
2. CRUD de grúas.
3. CRUD de empleados.
4. Registro de servicios.
5. Registro de gastos.
6. Calendario.
7. Cotizador con mapa.
8. Cálculos financieros.
9. Auditoría/actividad.
10. Pruebas y despliegue.

## 4. Render
PHP no tiene runtime nativo en Render; se puede desplegar mediante Docker.
Para un MVP gratuito, Render ofrece Web Services y Postgres gratuitos con limitaciones; el MySQL de este proyecto requiere un servicio Docker con almacenamiento persistente, por lo que no debemos asumir que la parte MySQL será gratuita.
