# https://docs.docker.com/compose/compose-file/#target
# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=8.1.7

FROM php:${PHP_VERSION}-fpm-alpine AS symfony_php

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
	;

ARG APCU_VERSION=5.1.21
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		zip \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

# Add xdebug
ARG INSTALL_XDEBUG
RUN if [ ${INSTALL_XDEBUG} = true ]; \
    then \
        apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
        apk add --update linux-headers; \
        pecl install xdebug-3.1.5; \
        docker-php-ext-enable xdebug; \
        apk del -f .build-deps; \
    fi;

COPY ./docker/images/symfony_php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Set SHELL flags for RUN commands to allow -e and pipefail
# Rationale: https://github.com/hadolint/hadolint/wiki/DL4006
SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

RUN apk update && \
    apk add --no-cache ssmtp \
		acl fcgi file gettext gnu-libiconv make tini
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

ADD https://github.com/gordalina/cachetool/releases/latest/download/cachetool.phar /usr/local/bin/
RUN chmod +x /usr/local/bin/cachetool.phar

## ------------------------------------------------- Healthcheck -------------------------------------------------------
#
COPY docker/images/symfony_php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

# ------------------------------------------------- Configure PHP -----------------------------------------------------

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/images/symfony_php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY docker/images/symfony_php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/images/symfony_php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

#COPY docker/images/symfony_php/ssmtp.conf /etc/ssmtp/ssmtp.conf
ARG TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

VOLUME /var/run/php

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /var/www/site/

# Allow to choose skeleton
ARG SKELETON="symfony/skeleton"
ENV SKELETON ${SKELETON}

# Allow to use development versions of Symfony
ARG STABILITY="stable"
ENV STABILITY ${STABILITY}

# Allow to select skeleton version
ARG SYMFONY_VERSION=""
ENV SYMFONY_VERSION ${SYMFONY_VERSION}

# Download the Symfony skeleton and leverage Docker cache layers
RUN composer create-project "${SKELETON} ${SYMFONY_VERSION}" . --stability=$STABILITY --prefer-dist --no-dev --no-progress --no-interaction; \
	composer clear-cache

####> recipes ###
###> doctrine/doctrine-bundle ###
RUN apk add --no-cache --virtual .pgsql-deps postgresql-dev; \
	docker-php-ext-install -j$(nproc) pdo_pgsql; \
	apk add --no-cache --virtual .pgsql-rundeps so:libpq.so.5; \
	apk del .pgsql-deps
###< doctrine/doctrine-bundle ###
####> xhprof/graphviz ###

ARG INSTALL_XHPROF
RUN if [ ${INSTALL_XHPROF} = true ]; \
    then \
        chmod +x /usr/local/bin/install-php-extensions && \
            install-php-extensions xhprof; \
        apk add --no-cache --update --virtual buildDeps autoconf graphviz; \
        apk del buildDeps; \
        mkdir -m 777 /var/www/xhprof; \
    fi;

COPY docker/images/symfony_php/conf.d/xhprof.ini $PHP_INI_DIR/conf.d/xhprof.ini
#COPY docker/images/symfony_php/crontab /var/spool/cron/crontabs/root

####< xhprof/graphviz ###
####< recipes ###

COPY src/backend/ .
RUN set -eux; \
	mkdir -p var/cache var/log; \
    composer config --no-plugins allow-plugins.symfony/runtime true; \
	composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer symfony:dump-env prod; \
	#composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /var/www/site/var/

# -------------------------------------------------- ENTRYPOINT --------------------------------------------------------
ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
