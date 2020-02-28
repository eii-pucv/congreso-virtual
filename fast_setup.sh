#!/bin/bash

#Check docker
if  which docker >/dev/null  &&  which docker-compose >/dev/null &&  test -f ./.env ; then
	chmod 777 compile_frontend.sh configure.sh livelog.sh run.sh stop.sh fast_update.sh
	./configure.sh --prepare=$UID
	echo "CONGRESO_USER_UID=${UID}" >> ./.env
	mv -f .env ./dist/volumefiles/
	./configure.sh --applyconfig
	./compile_frontend.sh
	./run.sh
else
	echo -e "This script requires Docker, Docker compose and a .env file in this dir. See README for more details"
fi
