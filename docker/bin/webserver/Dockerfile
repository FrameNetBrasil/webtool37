FROM framenetbrasil/apache-php:v3.5.0

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get --allow-releaseinfo-change update
RUN apt install -y graphviz

