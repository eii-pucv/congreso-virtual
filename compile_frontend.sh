#!/bin/bash

cd ./dist/congresovirtual-frontend
echo "Preparing environment for compiling frontend."
docker run --rm -v $(pwd):/app d1g1/vuejs npm install --unsafe-perm --loglevel=info --prefix /app/
echo "Compiling frontend. This may take a while ..."
docker run --rm --env NODE_OPTIONS="--max_old_space_size=4096" -v $(pwd):/app d1g1/vuejs npm run build --expose-gc --max_old_space_size=4096 --prefix /app/ 
cd ..
echo "Setting proper permissions for the result package"
docker run --rm -v $(pwd):/app d1g1/vuejs chown -R 33:$UID /app/congresovirtual-frontend
docker run --rm -v $(pwd):/app d1g1/vuejs chmod -R 776 /app/congresovirtual-frontend
echo "Copying htaccess file"
cp ./volumefiles/.htaccess_frontend ./congresovirtual-frontend/dist/.htaccess
echo -e "\nDone!\nNow you can run Congreso Virtual by typing ./run.sh\n"
cd ..