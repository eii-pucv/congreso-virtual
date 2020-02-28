#!/bin/bash

#Check docker
if which docker-compose >/dev/null; then
	cd ./dist/volumefiles/
	./installinitialdata.sh
	cd ../..
else
	echo -e "Docker-compose is not present and/or installed\nThis is a requirement for configuring and deploying Congreso Virtual."
fi
