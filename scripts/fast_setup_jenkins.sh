#!/bin/bash

#Crear carpeta de produccion si no existe y copiar todos los archivos (reemplazar si fuese necesario) alli
mkdir -p ../../../congresovirtual
cd ..
cp -rp . ../../congresovirtual
cd scripts

echo -e $CONGRESO_ENV > ../../../congresovirtual/.env_ex
sed -e ':a' -e 'N' -e '$!ba' -e 's/\n /\n/g' ../../../congresovirtual/.env_ex > ../../../congresovirtual/.env
#ls -lah ..
#cat ../.env

#continuar en la carpeta de produccion
cd ../../../congresovirtual/scripts

#determinar instalacion o actualizacion
if [ ! -d "../dist" ]; then
	./fast_setup.sh
else
	if [ ! -f ../done.flag ]; then
		./fast_setup.sh
	else
		./fast_update.sh
	fi
fi
