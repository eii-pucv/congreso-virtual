#!/bin/bash

echo -e $CONGRESO_ENV
echo -e $CONGRESO_ENV > .env
ls -lah
cat .env

exit 0;

if [ ! -d "dist" ]; then
	./fast_setup.sh
else 
	./fast_update.sh
fi
