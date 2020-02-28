#!/bin/bash

#Check docker
if which docker-compose >/dev/null; then
	echo "Starting Congreso Virtual."
	echo "If this is the first time running this may take a while, as docker needs to build the images. Please be patient"
	echo "=============================================="
	cd dist
	docker-compose up -d
	cd ..
	echo "=============================================="
	echo "Done!"
	echo "The site must be operative in the next 5 minutes (if it's the first time it can last up to 15-20 minutes as it needs to warm up installing dependencies and databases)"
	echo "For a live log of Congreso Virtual you can execute livelog.sh"
else
	echo -e "Docker-compose is not present and/or installed\nThis is a requirement for configuring and deploying Congreso Virtual."
fi



