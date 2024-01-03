FROM php:8.1-fpm

# Install php entension: ldap
RUN apt-get update && \
    apt-get install libldap2-dev -y && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
    docker-php-ext-install ldap

# Install php entension: curl
RUN apt-get update && \
    apt-get install pkg-config libcurl4-openssl-dev -y && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install curl

# Install php entension: exif
RUN docker-php-ext-install exif

# Install php entension: fileinfo
RUN docker-php-ext-install fileinfo

# Install php entension: gd
RUN apt-get update && \
    apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install gd && \
    rm -rf /var/lib/apt/lists/*

# Install php entension: imagick
RUN apt-get update && \
    apt-get install -y libmagickwand-dev && \
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    rm -rf /var/lib/apt/lists/*

# Install php entension: intl
RUN docker-php-ext-install intl

# Install php entension: mysqli
RUN docker-php-ext-install mysqli

# Install php entension: zip
RUN apt-get update && \
    apt-get install -y libzip-dev && \
    docker-php-ext-install zip && \
    rm -rf /var/lib/apt/lists/*

# Install Composer (PHP package manager)
RUN curl -s https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

# Copy just the files we need to be able to run "composer install"
# This is good practice, so that a change to another file doesn't mean we have to re-run "composer install"
COPY ./composer.* ./

ARG ACF_PRO_KEY
ENV ACF_PRO_KEY $ACF_PRO_KEY

# Use Composer to install PHP dependencies
RUN composer install

# Copy the remaining files to Docker image
COPY . .

# Expose port 8080 (so we can connect to the server from outside the Docker container)
EXPOSE 8080

# Run the web server
# Note: we need to specify the IP address 0.0.0.0 to allow PHP to receive connections from other machines (i.e. your laptop)
CMD php -S 0.0.0.0:8080 -t /var/www/html/web

#CMD sleep 1h
