# FROM node:20-alpine3.16 AS scss-processor

# RUN apk add git
# RUN npm install -g sass
# RUN git clone https://github.com/lbantoine/c5-grenier
# RUN sass ./c5-grenier/style:~/style

FROM php:7.4-apache

RUN apt update && apt upgrade -y && apt install -y \
  git libmcrypt-dev \
  zlib1g-dev \
  libicu-dev \
  g++ \
  zip \
  unzip \
  libonig-dev \
  libzip-dev \
  libpq-dev \
  default-mysql-client \
  jq

RUN docker-php-ext-install pdo_mysql mbstring intl zip

# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Set the COMPOSER_ALLOW_SUPERUSER env variable
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/

COPY . /var/www/html
RUN ls -la /var/www/html && sleep 10

# RUN rm -rf public/style
# COPY --from=scss-processor ~/style public/

RUN composer install -d ./html

# RUN ls -la /var/www/html

RUN chown -R www-data:www-data /var/www/html/public

# Configurez Apache pour utiliser le dossier "public" comme document root
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
    sed -ri -e 's/\/var\/www\/html/\/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf


# RUN mv /var/www/html/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN mv ./html/000-default.conf /etc/apache2/sites-available/000-default.conf

# Activez le module Apache "rewrite"
RUN a2enmod rewrite
RUN service apache2 restart

# Exécutez Composer pour générer l'autoloader
# RUN composer dump-autoload --no-scripts --no-dev --optimize