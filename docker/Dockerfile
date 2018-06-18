FROM debian:6.0.10
MAINTAINER Camdram Developers camdram-users@googlegroups.com
ENV DEBIAN_FRONTEND noninteractive

#Add dotdeb repos
RUN apt-get update && apt-get install -y wget curl ca-certificates \
    && echo "deb http://packages.dotdeb.org squeeze-php54 all\ndeb-src http://packages.dotdeb.org squeeze-php54 all" > /etc/apt/sources.list.d/dotdeb.list \
    && wget http://www.dotdeb.org/dotdeb.gpg -qO - | apt-key add -

#Set up PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    php5-cli \
    php5-curl \
    php5-intl \
    php5-sqlite \
    php5-apc \
    php5-gd \
    php5-json \
    supervisor

RUN apt-get install -y libpq5 unixodbc libmysqlclient16 \
    && wget -qO sphinxsearch.deb http://sphinxsearch.com/files/sphinxsearch_2.2.9-release-1_amd64.deb \
    && dpkg -i sphinxsearch.deb

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www
EXPOSE 8000
VOLUME /var/lib/mysql
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php5/apache2/php.ini
COPY php.ini /etc/php5/cli/php.ini
CMD /usr/bin/supervisord
