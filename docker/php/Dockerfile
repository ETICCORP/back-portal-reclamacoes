FROM php:8.2-fpm

WORKDIR /var/www

RUN rm -rf /var/www/html

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    apt-utils \
    libonig-dev \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Extensões PHP básicas
RUN docker-php-ext-install zip pdo_mysql mbstring exif bcmath

# GD com suporte a JPEG e FreeType
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd

# Instalar Redis
RUN mkdir -p /usr/src/php/ext/redis \
    && curl -fsSL https://pecl.php.net/get/redis --ipv4 | tar xvz -C "/usr/src/php/ext/redis" --strip 1 \
    && docker-php-ext-install redis

# Instalar PCNTL (necessário para Laravel Horizon/Reverb)
RUN docker-php-ext-install pcntl

# Instalar Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Copiar código do app
COPY . /var/www

EXPOSE 9000

CMD ["php-fpm"]
