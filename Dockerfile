FROM php:7.4.30-apache-buster
#update system core
RUN apt update -y && apt upgrade -y
# Instala el conector a mysql
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN a2enmod rewrite
RUN service apache2 restart
