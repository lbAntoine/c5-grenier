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

# RUN docker-php-ext-install pdo_mysql mbstring intl zip
RUN docker-php-ext-install pdo_mysql

RUN git clone https://github.com/lbantoine/c5-grenier && cd ./c5-grenier && git checkout feature/ci-integration && git pull && cd

RUN mv ./c5-grenier/* /var/www/html/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
# RUN rm -rf public/style
# COPY --from=scss-processor ~/style public/

RUN composer install

RUN ls -la /var/www/html

# Configurez Apache pour utiliser le dossier "public" comme document root


RUN mv /var/www/html/000-default.conf /etc/apache2/sites-available/000-default.conf

# Activez le module Apache "rewrite"
RUN a2enmod rewrite
RUN service apache2 restart

# Exécutez Composer pour générer l'autoloader
RUN composer dump-autoload --no-scripts --no-dev --optimize