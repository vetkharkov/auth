FROM php:7.4-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update \
  && apt-get install -y \
     apt-utils \
     curl \
     mariadb-client \
     git \
     nano \
     libpng-dev \
     libonig-dev \
     libxml2-dev \
     zip \
     unzip \
     vim \
     man \
     pkg-config \
     icu-devtools \
     libicu-dev \
     libcurl4 \
     libcurl4-gnutls-dev \
     libfreetype6-dev \
     libjpeg62-turbo-dev \
     libpng-dev \
     libbz2-dev \
     libssl-dev \
     libgmp-dev \
     libtidy-dev \
     libxml2-dev \
     libxslt1-dev \
     libzip-dev \
     libonig-dev \
  &&  ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h

RUN apt-get install -y cron && \
	apt-get install -y supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip \
    && docker-php-ext-install bz2 \
    && docker-php-ext-install calendar \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install exif \
    && docker-php-ext-install gettext \
    && docker-php-ext-install gmp \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install shmop \
    && docker-php-ext-install sockets \
    && docker-php-ext-install sysvmsg \
    && docker-php-ext-install sysvsem \
    && docker-php-ext-install sysvshm \
    && docker-php-ext-install tidy \
    && docker-php-ext-install xsl \
    && docker-php-source delete

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

RUN (crontab -l ; echo "* * * * * /usr/bin/php /var/www/artisan schedule:run >>/dev/null 2>&1") | crontab

# Copy existing application directory permissions
#COPY --chown=www:www . /var/www
COPY . /var/www
RUN chown -R www:www /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
#CMD ["php-fpm"]
CMD  service supervisor start; php-fpm
