#!/bin/bash
set -o errexit

#Check docker
if which docker >/dev/null  &&  which docker-compose >/dev/null ; then
	./stop.sh
	./configure.sh --update
	./compile_frontend.sh
	./run.sh
else
	echo -e "This script requires Docker and Docker compose. See README for more details"
fi
