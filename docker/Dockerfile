#NOTE: I change to 7.4 to use typed properties
FROM php:7.4-fpm

ARG PSR_VERSION=1.0.0
ARG PHALCON_VERSION=4.0.5
ARG PHALCON_EXT_PATH=php7/64bits

WORKDIR /var/www/task

#NOTE: To reduce the layers of docker image
RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++ libzip-dev libpng-dev lsb-base unzip zip librabbitmq-dev && \
    pecl install redis mongodb && \
    pecl install xdebug && \
    pecl install amqp && \
    #PSR
    curl -LO https://github.com/jbboehr/php-psr/archive/v${PSR_VERSION}.tar.gz && \
    tar xzf ${PWD}/v${PSR_VERSION}.tar.gz && \
    # Phalcon 4
    curl -LO https://github.com/phalcon/cphalcon/archive/v${PHALCON_VERSION}.tar.gz && \
    tar xzf ${PWD}/v${PHALCON_VERSION}.tar.gz && \
    docker-php-ext-install -j $(getconf _NPROCESSORS_ONLN) \
       ${PWD}/php-psr-${PSR_VERSION} \
       ${PWD}/cphalcon-${PHALCON_VERSION}/build/${PHALCON_EXT_PATH} \
    && \
    # Remove all temp files
    rm -r \
       ${PWD}/v${PSR_VERSION}.tar.gz \
       ${PWD}/php-psr-${PSR_VERSION} \
       ${PWD}/v${PHALCON_VERSION}.tar.gz \
       ${PWD}/cphalcon-${PHALCON_VERSION} && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl && \
    docker-php-ext-enable redis mongodb && \
    docker-php-ext-install bcmath pdo_mysql && \
    docker-php-ext-install zip && \
    docker-php-ext-install gd && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-enable amqp && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer  --version=1.10.13



