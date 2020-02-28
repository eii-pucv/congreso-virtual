#!/bin/bash

#Check docker
if which docker-compose >/dev/null; then
	docker exec -i mysql mysql -u {{DB_USERNAME}} -p {{DB_PASSWORD}} {{DB_DATABASE}} < ./initialdata.sql
else
	echo -e "Docker-compose is not present and/or installed\nThis is a requirement for configuring and deploying Congreso Virtual."
fi
