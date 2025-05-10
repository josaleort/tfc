#!/bin/bash

echo "->  Actualizando sistema..."
sudo apt update && sudo apt upgrade -y

echo "->  Instalando todo lo necesario..."
sudo apt install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release

echo "->  Añadiendo clave GPG de Docker..."
sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | \
  sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

echo "->  Añadiendo repositorio de Docker..."
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
  https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

echo "->  Actualizando lista de paquetes..."
sudo apt update

echo "->  Instalando Docker..."
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

echo "->  Verificando instalación..."
docker --version
docker compose version

echo "->  Añadiendo tu usuario al grupo docker..."
sudo usermod -aG docker $USER

echo "-> Ahora reinicia sesión o ejecuta ->'newgrp docker'<- para aplicar los cambios."

echo "->  Instalación completada. Puedes usar Docker y Docker Compose."
