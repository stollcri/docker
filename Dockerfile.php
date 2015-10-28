FROM php:5.5-apache
MAINTAINER Christopher Stoll <stollcri at gmail dot com>

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get upgrade -yq && \
    DEBIAN_FRONTEND=noninteractive apt-get install -yq \
		exim4 \
		git \
		libcurl4-openssl-dev \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmagickwand-6.q16-dev \
		libmcrypt-dev \
		libpng12-dev \
		mysql-client \
		php-pear \
		php5-cli \
		php5-curl \
		php5-dev \
		php5-gd \
		php5-imagick \
		php5-memcached \
		php5-mysql \
		php5-odbc \
		php5-xmlrpc \
		zlib1g-dev \
	&& docker-php-ext-install curl iconv mcrypt mysql mysqli opcache zip \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install gd \
	&& ln -s /usr/lib/x86_64-linux-gnu/ImageMagick-6.8.9/bin-Q16/MagickWand-config /usr/bin \
	&& pecl install imagick \
	&& echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN pecl install uri_template-alpha

#
# Copy Apache2 configuration files
#

COPY config/apache2/apache2.conf /etc/apache2/apache2.conf
COPY config/apache2/sites-enabled/10-www.maintenanceprogram.com.conf /etc/apache2/sites-enabled/10-www.maintenanceprogram.com.conf
COPY config/apache2/sites-enabled/10-www.maintenanceprogram.com_ssl.conf /etc/apache2/sites-enabled/10-www.maintenanceprogram.com_ssl.conf
RUN rm /etc/apache2/conf-enabled/javascript-common.conf

#
# Copy EXIM4 configuration files
#

COPY config/exim4/* /etc/exim4/
COPY config/exim4/email-addresses /etc/email-addresses
RUN chown root:Debian-exim /etc/exim4/passwd.client && \
	chmod 640 /etc/exim4/passwd.client && \
	update-exim4.conf

#
# Copy PHP5 configuration files
#

COPY config/php5/php.ini /usr/local/etc/php/php.ini

#
# Enable Apache2 and PHP modules
#

RUN a2enmod alias \
			auth_basic \
			authn_file \
			authz_groupfile \
			authz_host \
			authz_user \
			autoindex \
			cgi \
			deflate \
			dir \
			env \
			mime \
			negotiation \
			php5 \
			reqtimeout \
			rewrite \
			setenvif \
			socache_memcache \
			socache_shmcb \
			ssl \
			status \
	&& php5enmod opcache

#
# System Settings
#

RUN rm -fr /var/www/html && ln -s /myapp /var/www/html
RUN usermod -u 1000 www-data

COPY DockerStart.sh /DockerStart.sh
RUN chmod 755 /DockerStart.sh
CMD ["/DockerStart.sh"]

WORKDIR /var/www/html
EXPOSE 80
