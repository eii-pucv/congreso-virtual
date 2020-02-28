#!/bin/bash

echo -e $CONGRESO_ENV > .env
ls -lah

if [ ! -d "dist" ]; then
	./fast_setup.sh
else 
	./fast_update.sh
fi
