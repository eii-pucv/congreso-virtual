#!/bin/bash
set -o errexit

#Check docker
if which docker-compose >/dev/null; then
	echo "Executing SQL"
	cd ../dist/volumefiles/
	./installinitialdata.sh
	cd ..
	echo "Refreshing ElasticSearch"
	docker exec -w /var/www/congresovirtual-backend -i php php artisan search:reindex
	echo "Done"
	cd ../scripts
else
	echo -e "Docker-compose is not present and/or installed\nThis is a requirement for configuring and deploying Congreso Virtual."
fi
