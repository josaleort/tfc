#!/bin/bash

set -e

echo "->  Creando carpeta personalizada..."
mkdir -p mi-proyecto/nginx

echo ""

echo "->  Creando archivo docker-compose..."
cat > mi-proyecto/docker-compose.yml << 'EOF'

version: "3.8"

services:

  mysql:
    image: mysql:5.7
    container_name: mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: cliente
      MYSQL_DATABASE: wordpress_db
      MYSQL_USER: cliente
      MYSQL_PASSWORD: cliente
    volumes:
      - mysql_data:/var/lib/mysql

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: phpmyadmin
    restart: always
    depends_on:
      - mysql
    environment:
      PMA_HOST: mysql
      MYSQL_ROOT_PASSWORD: cliente
    ports:
      - 8081:80

  wordpress:
    image: wordpress:latest
    container_name: wordpress
    depends_on:
      - mysql
    restart: always
    environment:
      WORDPRESS_DB_HOST: mysql:3306
      WORDPRESS_DB_NAME: wordpress_db
      WORDPRESS_DB_USER: cliente
      WORDPRESS_DB_PASSWORD: cliente
    volumes:
      - wordpress_data:/var/www/html
    ports:
      - 8080:80

  nginx:
    image: nginx:latest
    container_name: nginx
    restart: always
    ports:
      - 80:80
    volumes:
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
      - wordpress_data:/var/www/html:ro
    depends_on:
      - wordpress

  portainer:
    image: portainer/portainer-ce
    container_name: portainer
    restart: always
    ports:
      - 9000:9000
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock
      - portainer_data:/data

volumes:
  mysql_data:
  wordpress_data:
  portainer_data:

EOF
echo ""

echo "->  Creando archivo de configuración para nginx..."
cat > mi-proyecto/nginx/default.conf << 'EOF'

server {
    listen 80;

    server_name localhost;

    location / {
        proxy_pass http://wordpress:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
EOF

echo ""
echo "->  Desplegando contenedores..."
cd mi-proyecto
COMPOSE_PROGRESS=plain docker compose up -d --build

echo ""
echo "-> Comprobando el estado de los contenedores..."
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo ""
echo "->  Proyecto desplegado."


