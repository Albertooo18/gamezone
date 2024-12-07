FROM mariadb:latest

# Establece el directorio de trabajo para inicialización
WORKDIR /docker-entrypoint-initdb.d

# Copia el script de inicialización SQL al contenedor
COPY estructura_gamezone.sql /docker-entrypoint-initdb.d/

# Expone el puerto 3306 para MariaDB
EXPOSE 3306
