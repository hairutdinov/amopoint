FROM php:8.3.3-fpm

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync

RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update \
    && apt-get install -qq -y \
      curl \
      git \
      zip unzip \
      librabbitmq-dev libldb-dev libldap2-dev libmemcached-dev libc-client-dev libpq-dev libkrb5-dev libkrb5-dev \
      libzmq3-dev zlib1g-dev \
    && install-php-extensions \
      pdo_pgsql \
      pgsql \
      mysqli \
      pdo \
      pdo_mysql

# Timezone
RUN ln -sf /usr/share/zoneinfo/Europe/Moscow  /etc/localtime \
    && echo "Europe/Moscow" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add custom php.ini
# ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

RUN addgroup --gid 3000 --system app
RUN adduser --uid 3000 --system --disabled-login --disabled-password --gid 3000 app

WORKDIR /var/www/html
CMD ["php-fpm"]