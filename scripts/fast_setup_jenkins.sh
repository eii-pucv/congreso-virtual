#!/bin/bash

echo -e $CONGRESO_ENV > ../.env_ex
sed -e ':a' -e 'N' -e '$!ba' -e 's/\n /\n/g' ../.env_ex > ../.env
ls -lah ..
cat ../.env

if [ ! -d "../dist" ]; then
	./fast_setup.sh
else
	if [ ! -f ../done.flag ]; then
		./fast_setup.sh
	else
		./fast_update.sh
	fi
fi
