# =============================================================================
# code-cookie — image PHP + Apache pour le DÉVELOPPEMENT uniquement.
#
# Images publiques uniquement : le dépôt doit pouvoir être construit par
# n'importe qui, sans registre privé.
#
# Le code n'est PAS copié dans l'image : docker-compose bind-monte le projet.
#
# PHP_VERSION est un argument de build : la valeur par défaut est celle qui
# fait tourner le composer.lock ACTUEL (WordPress 6.7.1). Elle sera à monter en
# 8.4 une fois le cœur et les extensions mis à jour, et après avoir vérifié le
# PHP réellement disponible sur le serveur cible.
# =============================================================================
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libicu-dev \
        zlib1g-dev \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libfreetype-dev \
        default-mysql-client \
        git \
        curl \
        unzip \
        less \
        msmtp \
    && rm -rf /var/lib/apt/lists/*

# gd avec webp et freetype : les sous-tailles d'images du blog en dépendent.
# ⚠️ L'extension imagick n'est PAS installée : l'extension imagemagick-engine du
# site se rabat alors sur GD, ce qui ne change que la qualité des miniatures
# REGÉNÉRÉES en local — les médias déjà produits en production sont intacts.
RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        gd \
        exif \
        opcache

# Tout mail sortant part vers Mailpit, jamais vers Internet. Un dump de
# production contient de vraies adresses d'abonnés : sans cette ligne, PHP
# tenterait un MTA local (absent, donc échec silencieux) et, sur une machine qui
# en a un, leur écrirait pour de bon.
RUN printf 'sendmail_path = "/usr/bin/msmtp -t --read-envelope-from --host=mailpit --port=1025 --auth=off --tls=off"\n' \
    > /usr/local/etc/php/conf.d/mail.ini

RUN { \
        echo 'upload_max_filesize = 64M'; \
        echo 'post_max_size = 64M'; \
        echo 'memory_limit = 512M'; \
        echo 'max_execution_time = 300'; \
        echo 'max_input_vars = 3000'; \
    } > /usr/local/etc/php/conf.d/uploads.ini

# revalidate_freq=0 : le code bind-monté est relu à chaque requête, sinon une
# modification sur l'hôte ne serait pas visible avant expiration du cache.
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# WP-CLI : le wp-cli.phar versionné à la racine du dépôt date de 2023 et n'est
# pas repris ici — l'image prend la version courante.
RUN curl -fsSL -o /usr/local/bin/wp \
        https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp

# MySQL 8.4 présente un certificat auto-signé, et le client mariadb vérifie la
# chaîne par défaut : sans cette ligne, `wp db import` et `wp db export` meurent
# sur « self-signed certificate in certificate chain » — donc l'import du dump
# du blog aussi. Constaté le 26/09/2026 en tentant l'import.
# Écrit dans ~/.my.cnf : dernier fichier lu par le client, donc celui qui prime.
RUN printf '[client]\nssl-verify-server-cert=0\n' > /root/.my.cnf \
    && chmod 600 /root/.my.cnf

# rewrite : permaliens. headers/expires/deflate : exigés par website/.htaccess.
RUN a2enmod rewrite headers expires deflate
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html
EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
