FROM php:7.3-apache

RUN apt-get autoclean

RUN apt-get update --fix-missing

RUN apt-get install -y \
    locales \
    wget \
    git \
    zip \
    webp \
    unzip \
    libcurl4 \
    libcurl4-gnutls-dev \
    mariadb-client \
    libpng-dev \
    libwebp-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libsodium-dev \
    zlib1g-dev \
    libzip-dev

RUN echo "Europe/Paris" >/etc/timezone && \
    ln -sf /usr/share/zoneinfo/Europe/Paris /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    sed -i -e 's/# fr_FR.UTF-8 UTF-8/fr_FR.UTF-8 UTF-8/' /etc/locale.gen && \
    echo 'LANG="fr_FR.UTF-8"'>/etc/default/locale && \
    dpkg-reconfigure -f noninteractive locales && \
    update-locale LANG=fr_FR.UTF-8

# Imagemagick
RUN apt-get update && apt-get install -y libmagickwand-6.q16-dev --no-install-recommends \
    && ln -s /usr/lib/x86_64-linux-gnu/ImageMagick-6.8.9/bin-Q16/MagickWand-config /usr/bin \
    && pecl install imagick \
    && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini

# config gd
RUN docker-php-ext-configure gd --with-jpeg-dir=/usr/include/ --with-freetype-dir=/usr/include/ --with-webp-dir=/usr/include/ --with-png-dir=/usr/include/

# PHP extensions
RUN docker-php-ext-install \
    iconv \
    intl \
    pdo \
    pdo_mysql \
    mysqli \
    curl \
    tokenizer \
    json \
    mbstring \
    gettext \
    exif \
    pcntl \
    zip \
    sodium \
    intl \
    gd

# if SUPERDOCK_REDIS=false
# Setup redis-cli
RUN apt-get update --fix-missing && apt-get install -y redis-tools
RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

# ARG SUPERDOCK_V8JS=true

# ENV V8_VERSION=7.4.288.21
# RUN if [ ${SUPERDOCK_V8JS} = true ]; then \
#     apt-get update -y --fix-missing && apt-get upgrade -y; \
#     apt-get install -y --no-install-recommends \
#     libtinfo5 libtinfo-dev \
#     build-essential \
#     curl \
#     git \
#     libglib2.0-dev \
#     libxml2 \
#     python \
#     patchelf \
#     && cd /tmp \
#     \
#     && git clone https://chromium.googlesource.com/chromium/tools/depot_tools.git --progress --verbose \
#     && export PATH="$PATH:/tmp/depot_tools" \
#     \
#     && fetch v8 \
#     && cd v8 \
#     && git checkout $V8_VERSION \
#     && gclient sync \
#     \
#     && tools/dev/v8gen.py -vv x64.release -- is_component_build=true use_custom_libcxx=false \
#     ;fi
# RUN if [ ${SUPERDOCK_V8JS} = true ]; then \
#     export PATH="$PATH:/tmp/depot_tools" \
#     && cd /tmp/v8 \
#     && ninja -C out.gn/x64.release/ \
#     && mkdir -p /opt/v8/lib && mkdir -p /opt/v8/include \
#     && cp out.gn/x64.release/lib*.so out.gn/x64.release/*_blob.bin out.gn/x64.release/icudtl.dat /opt/v8/lib/ \
#     && cp -R include/* /opt/v8/include/ \
#     && apt-get install patchelf \
#     && for A in /opt/v8/lib/*.so; do patchelf --set-rpath '$ORIGIN' $A;done \
#     ;fi
# RUN if [ ${SUPERDOCK_V8JS} = true ]; then \
#     cd /tmp \
#     && git clone https://github.com/phpv8/v8js.git \
#     && cd v8js \
#     && git checkout 2.1.1 \
#     && phpize \
#     && ./configure --with-v8js=/opt/v8 LDFLAGS="-lstdc++" \
#     && make install \
#     ;fi
# RUN if [ ${SUPERDOCK_V8JS} = true ]; then \
#     docker-php-ext-enable v8js \
#     ;fi

# if SUPERDOCK_NODE=true
#Install node
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash
RUN apt-get install -y nodejs
RUN curl -sL https://npmjs.org/install.sh | sh

# if SUPERDOCK_COMPOSER=true
# Get composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer
    
# if SUPERDOCK_XDEBUG=true
# Xdebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

#add php calendar
RUN docker-php-ext-install calendar

# Apache settings
RUN a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod headers \
    && a2enmod proxy_http \
    && a2enmod ssl

#install certbot, sendmail and ssmtp for ssl and mails
RUN apt-get install software-properties-common -y && \
    apt-get update --fix-missing && \
    apt-add-repository -r ppa:certbot/certbot -y && \
    apt-get update --fix-missing && \
    apt-get install python-certbot-apache -y

# PHP sessions
RUN mkdir -p /var/lib/php/sessions && chown -R www-data /var/lib/php && chmod -R 775 /var/lib/php/sessions

# Copy required configuration files
COPY conf/app.conf /etc/apache2/sites-available/000-default.conf
COPY php/app.ini /usr/local/etc/php/conf.d/app.ini
COPY php/app.ini /usr/local/etc/php/php.ini

#curl cert
COPY curl/cacert.pem /usr/local/etc/curl/cacert.pem

# Locale
ENV LC_ALL="fr_FR.UTF-8"
ENV LANG="fr_FR.UTF-8"
ENV LANGUAGE="fr_FR.UTF-8"

# tips: get volumes access
RUN usermod -u 1000 www-data
RUN usermod -G staff www-data

CMD ["apache2-foreground"]

# COPY docker/entrypoint.sh /
# RUN chmod +x /entrypoint.sh
# ENTRYPOINT ["/entrypoint.sh", ${SUPERDOCK_LOCAL_DOMAIN} ]