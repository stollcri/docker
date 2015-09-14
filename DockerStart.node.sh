#!/bin/bash
#
# Script is ran when the Docker container starts up
# 

# link cached node module directory to app directory
if [ -d /myapp/node_modules ]; then
	rm -r /myapp/node_modules
fi
if [ -d /tmp/node_modules ]; then
	ln -s /tmp/node_modules /myapp/node_modules
fi

# set node web port (must match EXPOSE in Dockerfile)
export PORT=80

# check if we are using Amazon Web Service Elastic Beanstalk
# (this environment variable must be manually added in the EB
#  instance configuration screen under "Software Configuration")
if [ -v AWSEB ]; then
	export NODE_ENV=aws_dev
	# start the app directly
	node bin/www
else
	# start the app using nodemon
	npm start
fi
