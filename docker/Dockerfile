FROM php:8.1-apache

RUN apt-get update \
    &&  apt-get install -y --no-install-recommends \
    locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip libpq-dev nodejs npm wget \
    apt-transport-https lsb-release ca-certificates

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen  \
    &&  echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
    &&  locale-gen


RUN curl -sS https://getcomposer.org/installer | php -- \
    &&  mv composer.phar /usr/local/bin/composer


RUN curl -sS https://get.symfony.com/cli/installer | bash \
    &&  mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN docker-php-ext-configure \
    intl \
    &&  docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql opcache intl zip calendar dom mbstring gd xsl

RUN pecl install apcu && docker-php-ext-enable apcu
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN npm install --global yarn

RUN cd /var/www && \
    composer install

COPY . /var/www/
RUN npm run build


WORKDIR /var/www/

ENTRYPOINT ["bash", "./docker/docker.sh"]
EXPOSE 80