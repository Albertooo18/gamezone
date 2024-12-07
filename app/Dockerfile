FROM php:apache

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y nano libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Configura el directorio de trabajo
WORKDIR /var/www/html
