FROM ubuntu:18.04
RUN apt update && apt -y upgrade
RUN apt install -y software-properties-common
RUN apt update
RUN ln -fs /usr/share/zoneinfo/America/New_York /etc/localtime
RUN export DEBIAN_FRONTEND=noninteractive
RUN apt install -y tzdata
RUN dpkg-reconfigure --frontend noninteractive tzdata
RUN apt install -y \
    apache2 \
    php \
    php-mbstring \
    php-mysql \
    # php-memcache \
    php-gd \
    php-xml \
    phpmd \
    # composer \
    mysql-server \
    unzip \
    curl

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer

COPY ./public_html/media /var/www/medieval-europe/public_html/media
COPY ./public_html/application/libraries     /var/www/medieval-europe/public_html/application/libraries
COPY ./public_html/application/models        /var/www/medieval-europe/public_html/application/models
RUN cd /var/www/medieval-europe/public_html/application/libraries/vendors/PHPMailer && \
    php /usr/local/bin/composer require league/oauth2-google

RUN cd /var/www/medieval-europe/public_html/application/models && \
    php /usr/local/bin/composer require league/oauth2-google

#RUN php /usr/local/bin/composer require league/oauth2-google
#COPY . /var/www/medieval-europe
COPY ./scripts /var/www/medieval-europe/scripts
COPY ./sql /var/www/medieval-europe/sql
COPY ./config /var/www/medieval-europe/config
COPY ./public_html/public /var/www/medieval-europe/public_html/public
#COPY ./public_html/koseven-devel /var/www/medieval-europe/public_html/koseven-devel
#COPY ./public_html/application/libraries /var/www/medieval-europe/public_html/application/libraries
#RUN chmod a+w /var/www/medieval-europe/public_html/koseven-devel/application/cache
#RUN chmod a+w /var/www/medieval-europe/public_html/koseven-devel/application/logs

COPY ./public_html/application/bootstrap.php /var/www/medieval-europe/public_html/application/bootstrap.php
COPY ./public_html/application/config /var/www/medieval-europe/public_html/application/config 
COPY ./public_html/application/classes /var/www/medieval-europe/public_html/application/classes
# COPY ./public_html/application/controllers   /var/www/medieval-europe/public_html/application/controllers
COPY ./public_html/application/helpers       /var/www/medieval-europe/public_html/application/helpers
COPY ./public_html/application/hooks         /var/www/medieval-europe/public_html/application/hooks         
COPY ./public_html/application/i18n          /var/www/medieval-europe/public_html/application/i18n          
COPY ./public_html/application/tests         /var/www/medieval-europe/public_html/application/tests         
COPY ./public_html/application/views /var/www/medieval-europe/public_html/application/views 
# COPY ./public_html/application /var/www/medieval-europe/public_html/application
COPY ./public_html/index.php /var/www/medieval-europe/public_html/index.php
COPY public_html/.htaccess.asdf /var/www/medieval-europe/public_html/.htaccess


COPY ./public_html/scripts /var/www/medieval-europe/public_html/scripts
COPY ./public_html/system /var/www/medieval-europe/public_html/system
COPY ./public_html/modules /var/www/medieval-europe/public_html/modules
COPY ./public_html/database /var/www/medieval-europe/public_html/database
COPY ./public_html/upload /var/www/medieval-europe/public_html/upload

COPY config/php7.ini /etc/php/7.2/apache2/
COPY config/medieval-europe.conf /etc/apache2/sites-available/

RUN a2dissite 000-default
RUN a2ensite medieval-europe.conf
RUN chmod a+w /var/www/medieval-europe/public_html/upload
RUN chmod a+w /var/www/medieval-europe/public_html/media/images/characters
RUN mkdir /var/www/medieval-europe/public_html/application/logs
RUN chmod a+w /var/www/medieval-europe/public_html/application/logs
RUN mkdir /var/www/medieval-europe/public_html/application/cache
RUN chmod a+w /var/www/medieval-europe/public_html/application/cache

CMD /var/www/medieval-europe/scripts/init_and_run.sh
