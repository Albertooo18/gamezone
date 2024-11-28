FROM mariadb:latest

# Establece el directorio de trabajo
WORKDIR /docker-entrypoint-initdb.d

# Copia el script de inicialización al contenedor
COPY estructura_gamezone.sql /docker-entrypoint-initdb.d/

# Expone el puerto predeterminado de MariaDB
EXPOSE 3306
