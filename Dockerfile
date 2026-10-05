FROM php:8.2-apache-alpine

# Instalar dependencias del sistema y extensiones requeridas por Filament
RUN apk add --no-cache \
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

# Configurar el DocumentRoot de Apache para apuntar a la carpeta public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/httpd.conf
RUN sed -i 's!AllowOverride None!AllowOverride All!g' /etc/apache2/httpd.conf

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

# Permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Script de inicio para adaptar Apache al puerto dinámico de Railway ($PORT)
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'PORT_TO_USE="${PORT:-8080}"' >> /start.sh && \
    echo 'sed -i "s/Listen 80/Listen ${PORT_TO_USE}/g" /etc/apache2/httpd.conf' >> /start.sh && \
    echo 'sed -i "s/:80/:${PORT_TO_USE}/g" /etc/apache2/conf.d/*.conf 2>/dev/null || true' >> /start.sh && \
    echo 'php artisan config:clear' >> /start.sh && \
    echo 'php artisan cache:clear' >> /start.sh && \
    echo 'php artisan route:clear' >> /start.sh && \
    echo 'php artisan view:clear' >> /start.sh && \
    echo 'exec httpd -D FOREGROUND' >> /start.sh && \
    chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]