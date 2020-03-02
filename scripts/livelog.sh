#!/bin/bash
set -o errexit

#Check docker
if which docker-compose >/dev/null; then
	cd ../dist
	docker-compose logs -f
	cd ../scripts
else
	echo -e "Docker-compose is not present and/or installed\nThis is a requirement for configuring and deploying Congreso Virtual."
fi

