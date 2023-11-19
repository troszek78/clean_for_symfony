FROM php:8.1-fpm

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN apt-get update && apt-get install -y \
        libpng-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        zip unzip \
        sudo;
RUN docker-php-ext-install gd;

ARG USERNAME
ARG USERID
ARG GROUPID

#skopiuj klucze uzytkownika do kontenera
COPY ./docker/.ssh/* /.ssh/

RUN mkdir /app

#dodaj usera, przekopiuj gdzie trzeba klucze
RUN groupadd -g ${GROUPID} ${USERNAME} && useradd -g ${USERNAME} -u ${USERID} ${USERNAME} ;
RUN chown -R ${USERNAME} /.ssh ; \
    chown -R ${USERNAME} /app ; \
    chmod 600 /.ssh/id_rsa ; \
    mv /.ssh `getent passwd ${USERNAME} | cut -d: -f6`/

#zmień kontekst na usera, nie rób nic jako root
USER ${USERNAME}

WORKDIR /app
