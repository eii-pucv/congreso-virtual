#!/bin/bash

#Check docker
if which docker >/dev/null  &&  which docker-compose >/dev/null ; then
	./stop.sh
	./configure.sh --update
	./compile_frontend.sh
	./run.sh
else
	echo -e "This script requires Docker, Docker compose and a .env file in this dir. See README for more details"
fi
