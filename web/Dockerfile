# use latest php version
FROM php:7.0.0RC6-fpm

WORKDIR /web

ADD requirements.txt /web/requirements.txt

RUN cat requirements.txt | apt-get install

ADD . /web

EXPOSE 80
