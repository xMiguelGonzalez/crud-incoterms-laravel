FROM php:8.2-apache

# instalamos las dependencias básicas y LOS COMPILADORES (g++, make)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    curl \
    gnupg2 \
    unixodbc-dev \
    apt-transport-https \
    g++ \
    make

# habilitamos reescritura en apache
RUN a2enmod rewrite

# instalamos los drivers de sql server (VERSIÓN DEBIAN 12)
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl -fsSL https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
    && pecl install sqlsrv-5.11.1 pdo_sqlsrv-5.11.1 \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv
    
# instalamos composer y lo copiamos al contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copiamos el codigo de laravel al contenedor
COPY . /var/www/html

# instalamos las dependencias de laravel con composer 
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install

# damos permisos a las carpetas de almacenamiento y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# configuramos apache para que apunte a la carpeta public de laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80