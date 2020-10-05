FROM framenetbrasil/apache-php:v3.5.0

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN echo "memory_limit=1024M" >> "$PHP_INI_DIR/php.ini" \
    && echo "post_max_size=100M" >> "$PHP_INI_DIR/php.ini" \
    && echo "upload_max_filesize=1024M" >> "$PHP_INI_DIR/php.ini"

RUN apt-get update && apt-get install -y --no-install-recommends apt-utils > /dev/null
RUN apt-get install -y software-properties-common
RUN apt-get update
RUN apt-get install -y tesseract-ocr
RUN apt-get install -y ffmpeg
