FROM framenetbrasil/apache-php:v3.5.0

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN sed -i 's|deb.debian.org|archive.debian.org|g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' /etc/apt/sources.list && \
    sed -i '/buster-updates/d' /etc/apt/sources.list && \
    apt-get update && \
    apt-get install -y graphviz

