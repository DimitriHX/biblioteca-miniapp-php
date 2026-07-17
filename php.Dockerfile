# Usamos la imagen oficial de PHP 8.4 FPM como base.
FROM php:8.4-fpm

# Instalamos la extensión pdo_mysql para permitir la conexión a bases de datos MySQL/MariaDB.
RUN docker-php-ext-install pdo_mysql