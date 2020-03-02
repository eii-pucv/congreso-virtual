#!/bin/bash
set -o errexit

#Check docker
if ! which docker >/dev/null ; then
	echo "Docker not found. "
	echo "This script requires Docker, Docker compose and a .env file in this dir. See README for more details."
	exit 1
fi
	
#Check docker compose
if ! which docker-compose >/dev/null ; then
	echo "Docker-compose not found. "
	echo "This script requires Docker, Docker compose and a .env file in this dir. See README for more details."
	exit 1
fi	

#Check env
if ! test -f ../.env ; then
	echo "env file not found. "
	echo "This script requires Docker, Docker compose and a .env file in this dir. See README for more details."
	exit 1
fi

if [ ! -f ../done.flag ]; then
	echo "Cleaning undone setup"
	./configure.sh --clean --yes
fi

chmod +x compile_frontend.sh configure.sh livelog.sh run.sh stop.sh fast_update.sh installinitialdata.sh
./configure.sh --prepare --UID=$UID --GID=$(id -g $UID)
echo "CONGRESO_USER_UID=${UID}" >> ../.env
echo "CONGRESO_USER_GID=$(id -g $UID)" >> ../.env
mv -f ../.env ../dist/volumefiles/
./configure.sh --applyconfig
./compile_frontend.sh
./run.sh

echo "done" > ../done.flag
