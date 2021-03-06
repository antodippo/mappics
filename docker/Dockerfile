FROM php:7.4-apache

COPY . /var/www/mappics
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/docker-custom-php.ini /usr/local/etc/php/conf.d

RUN pecl channel-update pecl.php.net
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN docker-php-ext-install exif

RUN apt-get update
RUN apt-get install -y libzip-dev zip libonig-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& docker-php-ext-install -j$(nproc) iconv \
	&& docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd

RUN chown -R www-data:www-data /var/www/mappics
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN service apache2 restart

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_CACHE_DIR=/root/.composer/cache
