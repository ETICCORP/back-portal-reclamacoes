FROM php:8.2-fpm

WORKDIR /var/www

# Remover html antigo
RUN rm -rf /var/www/html

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
        build-essential \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libwebp-dev \
        libxpm-dev \
        libzip-dev \
        libonig-dev \
        supervisor \
        procps \
        curl \
        git \
        unzip \
        vim \
        zip \
        jpegoptim optipng pngquant gifsicle \
        libssl-dev pkg-config ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Configura e instala extensões PHP
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm \
    && docker-php-ext-install gd zip pdo_mysql mbstring exif bcmath pcntl \
    && pecl install redis-5.3.7 \
    && docker-php-ext-enable redis

# Instala Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Copia aplicação
COPY . /var/www

# Cria pastas e permissões
RUN mkdir -p /var/run/supervisor /var/log/supervisord /var/www/storage/logs \
    && chmod -R 777 /var/run/supervisor /var/log/supervisord /var/www/storage

# Supervisor
COPY docker/php/supervisord.conf /etc/supervisor/supervisord.conf

# PHP ini custom
COPY ./docker/php/99-local.ini /usr/local/etc/php/conf.d/99-local.ini

EXPOSE 9000

CMD ["supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
