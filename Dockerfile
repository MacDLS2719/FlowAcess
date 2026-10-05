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

# Forzar a PHP-FPM a escuchar en el puerto TCP 9000 para que Nginx pueda comunicarse
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/g' /usr/local/etc/php-fpm.d/www.conf || \
    sed -i 's/listen = .*/listen = 9000/g' /usr/local/etc/php-fpm.d/www.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Composer ignorando alertas problemáticas de seguridad
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Instalar dependencias de Node y compilar assets (Vite/Filament)
RUN npm install && npm run build

# Configurar permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar Nginx y Supervisor
RUN mkdir -p /run/nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Crear automáticamente el script de inicio para soportar el puerto dinámico de Railway
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'PORT_TO_USE="${PORT:-8080}"' >> /start.sh && \
    echo 'sed -i "s/listen 80;/listen ${PORT_TO_USE};/g" /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'sed -i "s/listen \\[::\\]:80;/listen [::]:${PORT_TO_USE};/g" /etc/nginx/nginx.conf' >> /start.sh && \
    echo 'exec /usr/bin/supervisord -c /etc/supervisord.conf' >> /start.sh && \
    chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]