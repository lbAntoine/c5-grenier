# Utilisez l'image PHP officielle en tant que base
FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    git \
    nano \
    libmcrypt-dev \
    zlib1g-dev \
    libicu-dev \
    g++ \
    zip \
    unzip \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    default-mysql-client

RUN docker-php-ext-install pdo_mysql mbstring intl zip

# Copiez le code de votre projet dans le conteneur
COPY . /var/www/html/

# Installez Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Exécutez Composer pour installer les dépendances de votre projet
COPY composer.json /var/www/html/
# COPY composer.lock /var/www/html/
WORKDIR /var/www/html/
RUN composer install

# Configurez Apache pour utiliser le dossier "public" comme document root
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
    sed -ri -e 's/\/var\/www\/html/\/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf

COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf

# Activez le module Apache "rewrite"
RUN a2enmod rewrite
RUN service apache2 restart

# Exécutez Composer pour générer l'autoloader
RUN composer dump-autoload --no-scripts --no-dev --optimize
