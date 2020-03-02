#!/bin/bash
set -o errexit
cd ..

#Check docker
if which docker >/dev/null; then
	docker run --rm -v $(pwd):/app php php /app/volumefiles/configure.php "$@"
else
	echo -e "Docker is not present and/or installed\nThis is a requirement for configuring and deploying Congreso Virtual."
fi
cd scripts