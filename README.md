# Ruta Larga

Sistema de gestión logística y de flotas para la empresa "Ruta Larga Furgones Unidos".

## Descripción

Aplicación web en PHP que permite administrar:

- Clientes
- Choferes
- Vehículos
- Inventario
- Fletes
- Gastos de fletes
- Reportes en PDF y Excel
- Estadísticas operativas
- Registro, inicio de sesión, recuperación de contraseña y cambio de clave

## Tecnologías

- PHP
- MySQL/MariaDB
- HTML/CSS
- JavaScript
- FPDF (generación de PDFs)
- PHPMailer (envío de correos para recuperación de contraseña)

## Requisitos

- Servidor local WAMP / XAMPP / similar
- PHP 7.x o superior
- MySQL / MariaDB
- Navegador moderno

## Instalación

1. Copia la carpeta del proyecto en el directorio `www` o `htdocs` de tu servidor local.
2. Importa la base de datos desde el archivo SQL disponible en `database/proyecto (2).sql`, `database/proyecto (3).sql` o `database/proyecto (4).sql` según corresponda.
3. Verifica la configuración de la base de datos en `app/config/claseconexion.php`.
4. Abre el proyecto en el navegador usando la URL de tu servidor local.

## Configuración

- `app/config/claseconexion.php`: datos de conexión a la base de datos.
- `app/controller/recuperacionController.php`: configuración del correo SMTP para recuperación de contraseña.

## Estructura del proyecto

- `app/controller/`: controladores del sistema
- `app/model/`: modelos y lógica de acceso a datos
- `app/view/`: vistas HTML/PHP
- `assets/`: estilos, imágenes y recursos estáticos
- `database/`: archivos SQL y CSV de datos
- `FPDF/`: librería para generar PDF
- `PHPMAILER/`: librería PHPMailer para correo
- `Public/`: sitio web público de presentación

## Uso

- Accede al sistema desde la vista de login: `app/view/loginView.php`
- Regístrate desde `nuevoregistroView.php` o usa cuentas existentes si ya están en la base de datos
- Navega por el menú principal para gestionar clientes, choferes, vehículos, inventario y fletes
- Genera reportes y documentos en PDF/Excel desde la sección de reportes

## Notas importantes

- Asegúrate de configurar correctamente el servidor SMTP si vas a utilizar la recuperación de contraseña por correo.
- Los datos iniciales de ejemplo pueden cargarse desde los archivos CSV en `database/` si así lo deseas.

## Licencia

Este proyecto contiene una licencia en el archivo `LICENSE`.
