FROM php:7-apache

# configs
COPY ./php/php.ini /usr/local/etc/php/php.ini
COPY ./apache/apache2.conf /etc/apache2/apache2.conf
COPY ./apache/ifmodule.conf /etc/apache2/ifmodule.conf
COPY ./apache/remoteip.conf /etc/apache2/conf-available/remoteip.conf
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
COPY ./php/msmtprc /usr/local/etc/msmtprc

# add ons
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install exif

# Install memcache extension
RUN set -x \
    && apt-get update && apt-get install -y --no-install-recommends unzip libssl-dev libpcre3 libpcre3-dev \
    && cd /tmp \
    && curl -sSL -o php7.zip https://github.com/websupport-sk/pecl-memcache/archive/php7.zip \
    && unzip php7 \
    && cd pecl-memcache-php7 \
    && /usr/local/bin/phpize \
    && ./configure --with-php-config=/usr/local/bin/php-config \
    && make \
    && make install \
    && echo "extension=memcache.so" > /usr/local/etc/php/conf.d/ext-memcache.ini \
    && rm -rf /tmp/pecl-memcache-php7 php7.zip

# Install msmtp
RUN set -x \
    && cd /tmp \
    && curl -sSL -o msmtp.tar https://sourceforge.net/projects/msmtp/files/latest/download?source=files \
    && tar -xvf msmtp.tar \
    && cd msmtp-1.6.6 \
    && ./configure \
    && make \
    && make install \
    && rm -rf /tmp/msmtp-1.6.6 msmtp.tar \
	&& touch /var/log/msmtp.log \
	&& chmod 0644 /usr/local/etc/msmtprc \	
	&& chmod 777 /var/log/msmtp.log
	
# APACHE
# modrewrite, remoteip 
# remote ip https://trick77.com/apache2-2-4-logging-remote-ip-address-using-mod_remoteip/
RUN a2enmod rewrite \
	&& a2enmod remoteip \
	&& a2enconf remoteip


# Start up script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh
WORKDIR /var/www
CMD ["/usr/local/bin/start.sh"]
	