FROM composer:1.10 as builder-composer

FROM alpine:3.15 as builder

COPY bin/install-wp-tests.sh /install-wp-tests.sh
COPY bin/generate-plugin-zip.sh /generate-plugin-zip.sh

COPY --from=builder-composer /usr/bin/composer /usr/bin/composer

ENV DOCKER_TESTS=true

RUN set -ex; \
    chmod +x \
        /install-wp-tests.sh \
        /usr/bin/composer \
    ; \
    apk add --update \
        bash \
        curl \
        subversion \
    ; \
    apk add --no-cache \
        php7 \
        php7-exif \
        php7-fileinfo \
        php7-gd \
        php7-ldap \
        php7-json \
        php7-phar \
        php7-iconv \
        php7-intl \
        php7-openssl \
        php7-curl \
        php7-ctype \
        php7-dom \
        php7-mbstring \
        php7-mysqli \
        php7-pdo_mysql \
        php7-simplexml \
        php7-soap \
        php7-tokenizer \
        php7-xml \
        php7-xmlreader \
        php7-xmlwriter \
        php7-zip \
    ; \
    apk add --no-cache \
        nodejs \
        npm \
    ; \
    npm install grunt; \
    npm install -g grunt-cli;

COPY docker-test.sh /docker-test.sh

RUN set -ex; \
    chmod +x \
        /docker-test.sh \
        /install-wp-tests.sh \
        /generate-plugin-zip.sh \
        /usr/bin/composer \
    ;

CMD ["/docker-test.sh"]
