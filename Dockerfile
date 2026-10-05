FROM php:8.2-fpm-alpine

# Instalar dependencias del sistema y extensiones de PHP requeridas por Filament
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    git \
    nodejs \
    npm \
    bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip intl pdo pdo_mysql bcmath

# Forzar a PHP-FPM a escuchar en el puerto TCP 9000
RUN echo "listen = 9000" >> /etc/php82/php-fpm.d/zz-docker.conf 2>/dev/null || \
    echo "listen = 9000" >> /etc/php8/php-fpm.d/zz-docker.conf 2>/dev/null || \
    echo "listen = 9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf 2>/dev/null || true

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Instalar dependencias de Node y compilar assets
RUN npm install && npm run build

# Configurar permisos iniciales para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configuración de Supervisor
RUN mkdir -p /run/nginx
COPY docker/supervisord.conf /etc/supervisord.conf

# Script de inicio inteligente: Escribe Nginx directamente usando el $PORT de Railway
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'PORT_TO_USE="${PORT:-8080}"' >> /start.sh && \
    echo 'echo "worker_processes auto;" > /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "pid /run/nginx.pid;" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "events { worker_connections 1024; }" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "http {" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "    include /etc/nginx/mime.types;" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "    default_type application/octet-stream;" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "    sendfile on;" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "    keepalive_timeout 65;" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "    server {" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo \"        listen \${PORT_TO_USE};\" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo \"        listen [::]:\${PORT_TO_USE};\" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "        server_name _;" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "        root /var/www/html/public;" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "        index index.php index.html;" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "        charset utf-8;" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "        location / { try_files \$uri \$uri/ /index.php?\$query_string; }" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "        location ~ \\.php\$ { fastcgi_pass 127.0.0.1:9000; fastcgi_index index.php; include fastcgi_params; fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name; }" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "    }" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'echo "}" >> /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'php artisan config:clear' >> /start.sh && \
    echo 'php artisan cache:clear' >> /start.sh && \
    echo 'php artisan route:clear' >> /start.sh && \
    echo 'php artisan view:clear' >> /start.sh && \
    echo 'exec /usr/bin/supervisord -c /etc/supervisord.conf' >> /start.sh && \
    chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]