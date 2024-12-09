# Usa una versión compatible de PHP para Laravel 11
FROM php:8.2-apache

# Instala extensiones requeridas para Laravel
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql mbstring tokenizer xml ctype bcmath zip \
    && a2enmod rewrite

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia el código fuente al contenedor
COPY . /var/www/html

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Ajusta permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Instala las dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev

# Expone el puerto 80
EXPOSE 80

# Comando por defecto para iniciar Apache
CMD ["apache2-foreground"]
