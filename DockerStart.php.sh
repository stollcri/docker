#!/bin/bash
#
# Script is ran when the Docker container starts up
# 

if [ -z "$ENVDEVDB" ]; then
	ENVDEVDB=dev
fi

SOURCE_DIR="/myapp/site-config/"
SOURCE_FILE="dev0.server.local.php"
DESTINATION="/myapp/htdocs/include/server.local.php"

if [ ! -f $DESTINATION ]; then
	case $ENVDEVDB in
		"dev1" )
			SOURCE_FILE="dev1.server.local.php"
			;;
		"dev2" )
			SOURCE_FILE="dev2.server.local.php"
			;;
		"dev3" )
			SOURCE_FILE="dev3.server.local.php"
			;;
		* )
			SOURCE_FILE="dev0.server.local.php"
			;;
	esac

	if [ -f $SOURCE_DIR$SOURCE_FILE ]; then
		ln -s $SOURCE_DIR$SOURCE_FILE $DESTINATION
	fi
fi

source /etc/apache2/envvars
tail -F /var/log/apache2/* &
exec apache2 -D FOREGROUND
