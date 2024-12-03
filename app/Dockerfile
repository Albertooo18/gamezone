# Usamos una imagen base de Ubuntu
FROM ubuntu:20.04

# Actualizamos el sistema e instalamos dependencias
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    nano \
    vim \
    apache2 \
    certbot \
    python3-certbot-apache \
    openjdk-20-jdk \
    && apt-get clean

# Instalar Tomcat 10
RUN curl -O https://dlcdn.apache.org/tomcat/tomcat-10/v10.1.33/bin/apache-tomcat-10.1.33.tar.gz \
    && mkdir -p /opt/tomcat \
    && tar -xvzf apache-tomcat-10.1.33.tar.gz -C /opt/tomcat --strip-components=1 \
    && chmod +x /opt/tomcat/bin/*.sh

# Configurar variables de entorno para Java y Tomcat
ENV JAVA_HOME=/usr/lib/jvm/java-20-openjdk-amd64
ENV CATALINA_HOME=/opt/tomcat

# Crear directorio para el dominio certificadocripto.xyz
RUN mkdir -p /var/www/certificadocripto.xyz \
    && chown -R www-data:www-data /var/www/certificadocripto.xyz \
    && chmod -R 755 /var/www/certificadocripto.xyz

# Crear archivo de configuración de Apache para el dominio certificadocripto.xyz
COPY ./certificadocripto.xyz.conf /etc/apache2/sites-available/certificadocripto.xyz.conf

# Habilitar el sitio y deshabilitar el sitio por defecto
RUN a2ensite certificadocripto.xyz.conf && a2dissite 000-default.conf \
    && apache2ctl configtest \
    && systemctl restart apache2

# Generar certificado SSL con Certbot para el dominio certificadocripto.xyz
RUN certbot --apache --non-interactive --agree-tos -m cristiannmendoza18@gmail.com -d certificadocripto.xyz

# Exponer puertos
EXPOSE 80 443

# Comando por defecto
CMD ["apache2ctl", "-D", "FOREGROUND"]
