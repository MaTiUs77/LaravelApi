FROM decyt/nginx-php-fpm:latest

# Configure nginx
COPY nginx-php-fpm/nginx/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY nginx-php-fpm/php/fpm-pool.conf /etc/php7/php-fpm.d/zzz_custom.conf
COPY nginx-php-fpm/php/php.ini /etc/php7/conf.d/zzz_custom.ini

# Configure supervisord
COPY nginx-php-fpm/supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf


RUN adduser -u 1000 -D -S -G www-data www-data
RUN for user in $(awk -F: '{print $1}' /etc/passwd); do groups $user; done

# Crea la carpeta en www
RUN mkdir -p /var/www/html
WORKDIR /var/www/html
COPY . /var/www/html

RUN ls -al /var/www/html/storage
RUN chmod 777 /var/www/html/storage -R
RUN composer install --ignore-platform-reqs

RUN wget https://api.github.com/repos/MaTiUs77/LaravelApi/commits/master && mv master /var/www/html/public/master.json
RUN wget https://api.github.com/repos/MaTiUs77/LaravelApi/commits/developer && mv developer /var/www/html/public/developer.json

# Modifica todo los permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80 443
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]