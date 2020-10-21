FROM framenetbrasil/apache-php:v3.5.0

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && apt-get install -y --no-install-recommends apt-utils > /dev/null
RUN apt-get install -y tesseract-ocr
RUN apt-get install -y ffmpeg
