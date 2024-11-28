FROM php:apache

# Actualiza el sistema e instala herramientas adicionales y librerías necesarias
RUN apt-get update && apt-get install -y nano vim git unzip wget curl libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd mysqli pdo pdo_mysql

# Habilita módulos de Apache útiles
RUN a2enmod rewrite headers

# Configura permisos y establece el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos del proyecto al contenedor
COPY ./html /var/www/html

# Asigna permisos correctos a los archivos del proyecto
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Establece variables de entorno recomendadas
ENV APACHE_LOG_DIR /var/log/apache2
