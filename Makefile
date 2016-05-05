#
# Makefile to simplify managing Docker states
# 
#  Christopher Stoll, 2015-08-06
#  

#
# Setup
# 

# which docker machine
DOCKER_MACHINE := default
# which image to build
DOCKER_IMAGE := myname/myapp
# which image to run
DOCKER_INSTANCE := myname_myapp
# localhost url name so we don't have to type the IP
# (add /etc/hosts entry for this which points to localhost)
DOCKER_LOCALHOST := localhost.myapp.com
# path of the app, localy
APP_DIR := /app
# path of the app, inside the docker container
APP_PATH := /myapp
# the website source code directory
WEBSITE_URI := dev.myapp.com

# for absolute paths
WORKDIR := $(shell pwd)
USRNAME := $(shell pwd)

# for OS specific cheks
ifeq ($(OS),Windows_NT)
	OSFLAG := Windows
else
	UNAME_S := $(shell uname -s)
	
	ifeq ($(UNAME_S),Darwin)
		OSFLAG := Darwin
	endif

	ifeq ($(UNAME_S),Linux)
		OSFLAG := Linux
	endif
endif

# which development database
ENV_DEV_DB := @echo "Using database '$(ENVDEVDB)'"
ifeq ($(strip $(ENVDEVDB)),)
	ENVDEVDB := dev
	ENV_DEV_DB := @echo "Using database '$(ENVDEVDB)'"
endif

#
# Pre-checks
# 

# make sure docker is installed
DOCKER_EXISTS := @echo "Found docker"
DOCKER_WHICH := $(shell which docker)
ifeq ($(strip $(DOCKER_WHICH)),)
	DOCKER_EXISTS := @echo "\nERROR:\n docker not found.\n See: https://docs.docker.com/\n" && exit 1
endif

# make sure docker-machien is available, for Macs (and Windows)
DOCKER_MACHINE_EXISTS := @echo "Found docker-machine"
DOCKER_MACHINE_WHICH := $(shell which docker-machine)
ifneq ($(OSFLAG),Darwin)
	ifeq ($(strip $(DOCKER_MACHINE_WHICH)),)
		DOCKER_MACHINE_EXISTS := @echo "\nERROR:\n docker-machine not found.\n See: https://docs.docker.com/machine/\n" && exit 1
	endif
endif

# make sure docker machine is running
DOCKER_MACHINE_RUNS := @echo "Docker-machine is running"
DOCKER_MACHINE_RUNNING := $(shell docker-machine env default 2>&1 | grep -o 'not running')
ifneq ($(strip $(DOCKER_MACHINE_RUNNING)),)
	DOCKER_MACHINE_RUNS := docker-machine start $(DOCKER_MACHINE)
endif

# make sure the proper environemnt variables are set
DOCKER_MACHINE_ENV := @echo "Docker environment set"
DOCKER_MACHINE_EV1 := $(DOCKER_TLS_VERIFY)
DOCKER_MACHINE_EV2 := $(DOCKER_HOST)
DOCKER_MACHINE_EV3 := $(DOCKER_CERT_PATH)
DOCKER_MACHINE_EV4 := $(DOCKER_MACHINE_NAME)
ifeq ($(strip $(DOCKER_MACHINE_EV1)),)
	DOCKER_MACHINE_ENV = @echo "\n Docker environment missing, run: eval \"\$$(docker-machine env default)\"\n" && exit 1
endif
ifeq ($(strip $(DOCKER_MACHINE_EV2)),)
	DOCKER_MACHINE_ENV = @echo "\n Docker environment missing, run: eval \"\$$(docker-machine env default)\"\n" && exit 1
endif
ifeq ($(strip $(DOCKER_MACHINE_EV3)),)
	DOCKER_MACHINE_ENV = @echo "\n Docker environment missing, run: eval \"\$$(docker-machine env default)\"\n" && exit 1
endif
ifeq ($(strip $(DOCKER_MACHINE_EV4)),)
	DOCKER_MACHINE_ENV = @echo "\n Docker environment missing, run: eval \"\$$(docker-machine env default)\"\n" && exit 1
endif

# make sure the docker machine is running
DOCKER_MACHINE_START := @echo "Docker machine '$(DOCKER_MACHINE)' already running"
DOCKER_MACHINE_STATUS := $(shell docker-machine status $(DOCKER_MACHINE))
ifneq ($(DOCKER_MACHINE_STATUS),Running)
	DOCKER_MACHINE_START := docker-machine start $(DOCKER_MACHINE)
endif

DOCKER_MACHINE_IP := @docker-machine ip $(DOCKER_MACHINE)
DOCKER_MACHINE_IP_NOW := $(shell docker-machine ip $(DOCKER_MACHINE))

LOCALHOST_NOTE := @echo "Container accessible via $(DOCKER_LOCALHOST)"
LOCALHOST_ENTRY := $(shell ping -c1 -t1 $(DOCKER_LOCALHOST) | grep icmp_seq | grep -o $(DOCKER_MACHINE_IP_NOW))
ifneq ($(LOCALHOST_ENTRY),$(DOCKER_MACHINE_IP_NOW))
	LOCALHOST_NOTE := @echo "Add '$(DOCKER_MACHINE_IP_NOW) $(DOCKER_LOCALHOST)' to /etc/hosts"
endif

#
# Targets
# 

default: up

.PHONY: up down start stop restart rebug precheck build dostart dostop shutdown debug shell bashell bashshell clean

up: precheck build dostart
down: precheck shutdown
start: precheck dostart
stop: precheck dostop
restart: dostop dostart
rebug: dostop debug

precheck:
	$(ENV_DEV_DB)
	$(DOCKER_EXISTS)
	$(DOCKER_MACHINE_EXISTS)
	$(DOCKER_MACHINE_RUNS)

build:
	$(DOCKER_MACHINE_ENV)
	$(DOCKER_MACHINE_START)
	docker build -t $(DOCKER_IMAGE) .
	@echo ""
	@echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	@echo "!!!!! REQUIRED MANUAL ACTIONS !!!!!"
	@echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	@echo ""
	@echo " REQUIRED:"
	@echo "  NONE"
	@echo ""

dostart:
	# docker run -it --rm --name $(DOCKER_INSTANCE) -v $(WORKDIR)$(APP_DIR):$(APP_PATH) -d --net=host -p 80:80 -e ENVDEVDB=$(ENVDEVDB) $(DOCKER_IMAGE)
	docker run --rm --name $(DOCKER_INSTANCE) -v $(WORKDIR)$(APP_DIR):$(APP_PATH) -d --net=host -e ENVDEVDB=$(ENVDEVDB) $(DOCKER_IMAGE)
	@echo "\nDocker-machine running at IP address:"
	$(DOCKER_MACHINE_IP)
	$(LOCALHOST_NOTE)
	@echo

dostop:
	docker stop $(DOCKER_INSTANCE)
	docker rm $(DOCKER_INSTANCE)

shutdown:
	docker stop $$(docker ps -a -q)
	docker rm $$(docker ps -a -q)
	docker-machine stop

debug:
	docker run -it --rm --name $(DOCKER_INSTANCE) -v $(WORKDIR)$(APP_DIR):$(APP_PATH) --net=host -e ENVDEVDB=$(ENVDEVDB) $(DOCKER_IMAGE)

status:
	docker-machine ls
	@echo
	docker ps -a
	@echo
	docker-machine ip $(DOCKER_MACHINE)
	@echo

shell: bashshell
bashell: bashshell
bashshell:
	docker exec -it $(DOCKER_INSTANCE) /bin/bash

clean:
	docker rm $$(docker ps -a -q)
