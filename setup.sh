#!/bin/bash

# Actualizar los repositorios y el sistema
sudo apt update && sudo apt upgrade -y

# Instalar Git
echo "Instalando Git..."
sudo apt install git -y

echo "Verificando la instalación de Git..."
git --version

# Clonar el repositorio
REPO_URL="https://github.com/Albertooo18/gamezone.git"
echo "Clonando el repositorio: $REPO_URL"
git clone $REPO_URL

# Instalar dependencias para Docker
echo "Instalando dependencias para Docker..."
sudo apt-get install apt-transport-https ca-certificates curl software-properties-common -y

# Añadir la clave GPG oficial de Docker
echo "Añadiendo la clave GPG de Docker..."
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

# Añadir el repositorio de Docker
echo "Añadiendo el repositorio de Docker..."
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"

# Actualizar los repositorios y el sistema nuevamente
echo "Actualizando los repositorios..."
sudo apt-get update

# Instalar Docker
echo "Instalando Docker..."
sudo apt-get install docker-ce docker-ce-cli containerd.io -y

# Instalar Docker Compose
echo "Instalando Docker Compose..."
sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Añadir el usuario actual al grupo de Docker
echo "Añadiendo el usuario actual al grupo de Docker..."
sudo usermod -aG docker $USER

# Cambiar al nuevo grupo para evitar reiniciar
newgrp docker

echo "Instalación y configuración completadas."
