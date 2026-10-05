#!/bin/sh
# Reemplaza el puerto 80 en la configuración de Nginx por el puerto dinámico de Railway ($PORT)
# Si no existe $PORT, usa el puerto 8080 por defecto
PORT_TO_USE="${PORT:-8080}"
sed -i "s/listen 80;/listen ${PORT_TO_USE};/g" /etc/nginx/nginx.conf
sed -i "s/listen \[::\]:80;/listen [::]:${PORT_TO_USE};/g" /etc/nginx/nginx.conf

# Inicia Supervisor (que a su vez arranca Nginx y PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisord.conf