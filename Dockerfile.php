FROM php:5.4-apache
MAINTAINER Christopher Stoll <stollcri at gmail dot com>

RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get upgrade -yq
RUN DEBIAN_FRONTEND=noninteractive apt-get install -yq \
		libcurl4-openssl-dev \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
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
		sendmail \
	&& docker-php-ext-install iconv mcrypt \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install gd \
	&& docker-php-ext-install curl gd mysql mysqli
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN pecl install xdebug

#
# Copy Apache2 configuration files
#

# COPY config/apache2/apache2.conf /etc/apache2/apache2.conf
#
# COPY config/apache2/sites-enabled/10-myapp.com.conf					/etc/apache2/sites-enabled/10-myapp.com.conf
# COPY config/apache2/sites-enabled/10-myapp.com_ssl.conf				/etc/apache2/sites-enabled/10-dev.myapp.com_ssl.conf

#
# Copy PHP5 configuration files
#

COPY config/php5/php.ini /etc/php5/php.ini
# 
# COPY config/php5/conf.d/browscap.ini 		/usr/local/etc/php/conf.d/browscap.ini
# COPY config/php5/conf.d/curl.ini 			/usr/local/etc/php/conf.d/curl.ini
# COPY config/php5/conf.d/gd.ini 			/usr/local/etc/php/conf.d/gd.ini
# COPY config/php5/conf.d/imagick.ini 		/usr/local/etc/php/conf.d/imagick.ini
# COPY config/php5/conf.d/memcached.ini 	/usr/local/etc/php/conf.d/memcached.ini
# COPY config/php5/conf.d/mysql.ini 		/usr/local/etc/php/conf.d/mysql.ini
# COPY config/php5/conf.d/mysqli.ini 		/usr/local/etc/php/conf.d/mysqli.ini
# COPY config/php5/conf.d/odbc.ini 			/usr/local/etc/php/conf.d/odbc.ini
# COPY config/php5/conf.d/pdo.ini 			/usr/local/etc/php/conf.d/pdo.ini
# COPY config/php5/conf.d/pdo_mysql.ini 	/usr/local/etc/php/conf.d/pdo_mysql.ini
# COPY config/php5/conf.d/pdo_odbc.ini 		/usr/local/etc/php/conf.d/pdo_odbc.ini
# COPY config/php5/conf.d/pdo_sqlite.ini 	/usr/local/etc/php/conf.d/pdo_sqlite.ini
# COPY config/php5/conf.d/snmp.ini 			/usr/local/etc/php/conf.d/snmp.ini
# COPY config/php5/conf.d/sqlite3.ini 		/usr/local/etc/php/conf.d/sqlite3.ini
# COPY config/php5/conf.d/xmlrpc.ini 		/usr/local/etc/php/conf.d/xmlrpc.ini

#
# Enable Apache2 modules
#

RUN if [ ! -f /etc/apache2/mods-enabled/alias.conf 				];then if [ -f /etc/apache2/mods-available/alias.conf				]; then ln -s /etc/apache2/mods-available/alias.conf			/etc/apache2/mods-enabled/alias.conf			; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/alias.load 				];then if [ -f /etc/apache2/mods-available/alias.load				]; then ln -s /etc/apache2/mods-available/alias.load			/etc/apache2/mods-enabled/alias.load			; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/auth_basic.load 		];then if [ -f /etc/apache2/mods-available/auth_basic.load			]; then ln -s /etc/apache2/mods-available/auth_basic.load		/etc/apache2/mods-enabled/auth_basic.load		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/authn_file.load 		];then if [ -f /etc/apache2/mods-available/authn_file.load			]; then ln -s /etc/apache2/mods-available/authn_file.load		/etc/apache2/mods-enabled/authn_file.load		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/authz_groupfile.load 	];then if [ -f /etc/apache2/mods-available/authz_groupfile.load		]; then ln -s /etc/apache2/mods-available/authz_groupfile.load	/etc/apache2/mods-enabled/authz_groupfile.load	; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/authz_host.load 		];then if [ -f /etc/apache2/mods-available/authz_host.load			]; then ln -s /etc/apache2/mods-available/authz_host.load		/etc/apache2/mods-enabled/authz_host.load		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/authz_user.load 		];then if [ -f /etc/apache2/mods-available/authz_user.load			]; then ln -s /etc/apache2/mods-available/authz_user.load		/etc/apache2/mods-enabled/authz_user.load		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/autoindex.conf 			];then if [ -f /etc/apache2/mods-available/autoindex.conf			]; then ln -s /etc/apache2/mods-available/autoindex.conf		/etc/apache2/mods-enabled/autoindex.conf		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/autoindex.load 			];then if [ -f /etc/apache2/mods-available/autoindex.load			]; then ln -s /etc/apache2/mods-available/autoindex.load		/etc/apache2/mods-enabled/autoindex.load		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/cgi.load 				];then if [ -f /etc/apache2/mods-available/cgi.load					]; then ln -s /etc/apache2/mods-available/cgi.load				/etc/apache2/mods-enabled/cgi.load				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/deflate.conf 			];then if [ -f /etc/apache2/mods-available/deflate.conf				]; then ln -s /etc/apache2/mods-available/deflate.conf			/etc/apache2/mods-enabled/deflate.conf			; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/deflate.load 			];then if [ -f /etc/apache2/mods-available/deflate.load				]; then ln -s /etc/apache2/mods-available/deflate.load			/etc/apache2/mods-enabled/deflate.load			; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/dir.conf 				];then if [ -f /etc/apache2/mods-available/dir.conf					]; then ln -s /etc/apache2/mods-available/dir.conf				/etc/apache2/mods-enabled/dir.conf				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/dir.load 				];then if [ -f /etc/apache2/mods-available/dir.load					]; then ln -s /etc/apache2/mods-available/dir.load				/etc/apache2/mods-enabled/dir.load				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/env.load 				];then if [ -f /etc/apache2/mods-available/env.load					]; then ln -s /etc/apache2/mods-available/env.load				/etc/apache2/mods-enabled/env.load				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/mime.conf 				];then if [ -f /etc/apache2/mods-available/mime.conf				]; then ln -s /etc/apache2/mods-available/mime.conf				/etc/apache2/mods-enabled/mime.conf				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/mime.load 				];then if [ -f /etc/apache2/mods-available/mime.load				]; then ln -s /etc/apache2/mods-available/mime.load				/etc/apache2/mods-enabled/mime.load				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/negotiation.conf 		];then if [ -f /etc/apache2/mods-available/negotiation.conf			]; then ln -s /etc/apache2/mods-available/negotiation.conf		/etc/apache2/mods-enabled/negotiation.conf		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/negotiation.load 		];then if [ -f /etc/apache2/mods-available/negotiation.load			]; then ln -s /etc/apache2/mods-available/negotiation.load		/etc/apache2/mods-enabled/negotiation.load		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/php5.conf 				];then if [ -f /etc/apache2/mods-available/php5.conf				]; then ln -s /etc/apache2/mods-available/php5.conf				/etc/apache2/mods-enabled/php5.conf				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/php5.load 				];then if [ -f /etc/apache2/mods-available/php5.load				]; then ln -s /etc/apache2/mods-available/php5.load				/etc/apache2/mods-enabled/php5.load				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/reqtimeout.conf 		];then if [ -f /etc/apache2/mods-available/reqtimeout.conf			]; then ln -s /etc/apache2/mods-available/reqtimeout.conf		/etc/apache2/mods-enabled/reqtimeout.conf		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/reqtimeout.load 		];then if [ -f /etc/apache2/mods-available/reqtimeout.load			]; then ln -s /etc/apache2/mods-available/reqtimeout.load		/etc/apache2/mods-enabled/reqtimeout.load		; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/rewrite.load 			];then if [ -f /etc/apache2/mods-available/rewrite.load				]; then ln -s /etc/apache2/mods-available/rewrite.load			/etc/apache2/mods-enabled/rewrite.load			; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/setenvif.conf 			];then if [ -f /etc/apache2/mods-available/setenvif.conf			]; then ln -s /etc/apache2/mods-available/setenvif.conf			/etc/apache2/mods-enabled/setenvif.conf			; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/setenvif.load 			];then if [ -f /etc/apache2/mods-available/setenvif.load			]; then ln -s /etc/apache2/mods-available/setenvif.load			/etc/apache2/mods-enabled/setenvif.load			; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/socache_memcache.load 	];then if [ -f /etc/apache2/mods-available/socache_memcache.load	]; then ln -s /etc/apache2/mods-available/socache_memcache.load	/etc/apache2/mods-enabled/socache_memcache.load	; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/socache_shmcb.load 		];then if [ -f /etc/apache2/mods-available/socache_shmcb.load		]; then ln -s /etc/apache2/mods-available/socache_shmcb.load	/etc/apache2/mods-enabled/socache_shmcb.load	; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/ssl.conf 				];then if [ -f /etc/apache2/mods-available/ssl.conf					]; then ln -s /etc/apache2/mods-available/ssl.conf				/etc/apache2/mods-enabled/ssl.conf				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/ssl.load 				];then if [ -f /etc/apache2/mods-available/ssl.load					]; then ln -s /etc/apache2/mods-available/ssl.load				/etc/apache2/mods-enabled/ssl.load				; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/status.conf 			];then if [ -f /etc/apache2/mods-available/status.conf				]; then ln -s /etc/apache2/mods-available/status.conf			/etc/apache2/mods-enabled/status.conf			; fi; fi
RUN if [ ! -f /etc/apache2/mods-enabled/status.load 			];then if [ -f /etc/apache2/mods-available/status.load				]; then ln -s /etc/apache2/mods-available/status.load			/etc/apache2/mods-enabled/status.load			; fi; fi

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
