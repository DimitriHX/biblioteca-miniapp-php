FROM mysql:8.0

# Configuramos las variables para emular XAMPP
ENV MYSQL_ALLOW_EMPTY_PASSWORD=yes
ENV MYSQL_DATABASE=biblioteca

# Copiamos el script SQL a la carpeta de inicialización nativa de MySQL.
# Docker ejecutará este archivo automáticamente al levantar el contenedor por primera vez.
COPY biblioteca.sql /docker-entrypoint-initdb.d/