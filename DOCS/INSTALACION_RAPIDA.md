## Instalación rápida con uso de consola bash.
Congreso Virtual ofrece la opción de Instalación rápida, el cual luego de configurar algunos parámetros permite construir y correr Congreso Virtual en su servidor mediante contenedores Docker. Es una alternativa mas expresa que montar la plataforma de forma tradicional, según describe la documentación.

**NOTA:** Si bien, estos scripts son hechos para bash, y por ende, pensados para sistemas basados en UNIX (Linux, OSX, otros), pueden también correrse en Windows con ayuda de componentes adicionales, tales como MSYS2, Git for Windows o WSL.

Para efectuar la instalación rápida de Congreso Virtual se necesitará tener instalado en su sistema operativo los siguientes componentes:

 - [Docker CE](https://docs.docker.com/get-started/)
 - [Docker-compose](https://docs.docker.com/compose/install/)

Además, asegúrese que el servidor posea los puertos 80 y 443 desocupados. 

**NOTA:** Es posible ejecutar la instalación rápida desde su servidor de Integración Continua (Jenkins) vía este repositorio. Ver mas abajo el capítulo [Instalación con Jenkins](#instalación-con-jenkins).

Luego de clonar el repositorio de Congreso Virtual, por favor copie el archivo `.env` localizado en `/volumefiles/` a la raiz del repositorio, y configure los parámetros dentro a su gusto (Viene ya preconfigurado, aunque deberá especificar algunos parametros tales como las URL de funcionamiento). 

**NOTA:** El script de instalación CREARÁ una base de datos en un contenedor Docker, no es necesario que usted prepare una. Los datos que aparece en la configuración son usados al momento de crear las tablas.

**NOTA:** Si desea habilitar HTTPS, por favor, ponga su certificado en `/volumefiles/certs/cert.crt` y `/volumefiles/certs/cert.key`

Una vez listo ejecute el siguiente comando para iniciar la instalación.

    ./scripts/fast_setup.sh

Se iniciará automáticamente el proceso de instalación y puesta en punto; el cual dependiendo del rendimiento de su servidor tardará dentro de 30 a 40 minutos. 

Una vez finalizado el proceso, deje unos 15 minutos adicionales para que Congreso Virtual termine de inicializarse en segundo plano (librerías, dependencias y bases de datos). 

Una vez funcionando Congreso Virtual, deseará poblar éste con información inicial. Para aquel fin, se recomienda instalar los datos iniciales en la base de datos ejecutando este comando.

    ./scripts/installinitialdata.sh
Se creará con ello un usuario `admin@congresovirtual.cl` con contraseña `abc123456`

En cualquier momento usted puede ver los logs de Congreso Virtual (y sus componentes) ejecutando

    ./scripts/livelog.sh

Puede parar la ejecución de congreso virtual en cualquier momento ejecutando el comando 

    ./scripts/stop.sh

De la misma forma puede volver a iniciar el servidor ejecutando

    ./scripts/run.sh

## Actualización rápida con uso de consola bash.
De la misma forma que la instalación, Congreso Virtual permite una forma rápida de actualizar la plataforma a su versión mas reciente.

Para ello, simplemente actualice su repositorio (git pull), y luego ejecute el siguiente comando para iniciar la actualización.

    ./scripts/fast_update.sh

Deberá esperar aproximadamente 30 minutos dependiendo del rendimiento del servidor (durante este periodo Congreso Virtual estará fuera de servicio). Una vez finalizado y esperado los 5 minutos post instalación, Congreso Virtual se encontrará actualizado y nuevamente listo para operar.

## Uso manual de los scripts de mantenimiento.
Si desea ir paso por paso por los procesos de instalación y actualización (ya sea por personalizar pasos, o bien ), es posible operar los scripts de forma manual.

El primer script es ./scripts/configure.sh cuya tarea es ayudar en las tareas de configurar una instancia de Congreso Virtual en un entorno especial para ello, creando una carpeta dist, donde en volumefiles contiene las configuraciones de cada componente. ./scripts/configure.sh se encarga de tomar las configuraciones en el archivo .env y las reparte en los distintos componentes. 

Para inicializar una carpeta dist se deberá ejecutar el comando

    ./scripts/configure.sh --prepare=$UID
Donde UID es el ID de usuario actual. Una vez inicializado, el script invitará a configurar el archivo /dist/volumefiles/.env con la información correspondiente. 

**NOTA:** Si desea habilitar HTTPS, por favor, ponga su certificado en `/dist/volumefiles/certs/cert.crt` y `/dist/volumefiles/certs/cert.key`

Para aplicar la configuración del .env ejecutar

    ./scripts/configure.sh --applyconfig
Una vez finalizada la aplicación de la configuración, podrá compilar el frontend de la carpeta dist (basado en vue), ejecutando el comando

    ./scripts/compile_frontend.sh

Inicializará una instancia temporal de vue y compilará el frontend (esto puede tardar mucho tiempo, y puede consumir mucha RAM). Luego añadirá archivos especiales y asignará permisos para apache.  

Con el frontend compilado, ya se puede inicializar el proyecto con el comando 

    ./scripts/run.sh

Si se corre por primera vez, se inicializarán los contenedores, y se crearán los archivos de bases de datos segun lo descrito en el archivo de configuración en `/dist/volumefiles/mysql` y `/dist/volumefiles/elasticsearch`. Si no es la primera vez que se corren, entonces se usarán los archivos ya existentes.

Una vez que haya finalizado el inicio, en segundo plano Congreso Virtual correrá las actualizaciónes de Composer y Artisan, lo que puede tardar un tiempo. Puede ver el estado y los logs de Congreso y sus componentes con

    ./scripts/livelog.sh

Una vez funcionando Congreso Virtual por primera vez, deseará poblar éste con información inicial. Para aquel fin, se recomienda instalar los datos iniciales en la base de datos ejecutando este comando.

    ./scripts/installinitialdata.sh
Se creará con ello un usuario `admin@congresovirtual.cl` con contraseña `abc123456`

**NOTA:** Si necesita cargar un set de datos especiales, puede editar el archivo `/dist/volumefiles/initialdata.sql` e ingresarlos allí. Luego correr `./installinitialdata.sh` para aplicarlo.

De la misma forma, puede parar la ejecución de Congreso Virtual con el comando

    ./scripts/stop.sh

Si bien esto destruye los contenedores, la información persistente (storage, bases de datos, etc) se encuentra intacta en la carpeta dist. Después, puede usar el comando `./start.sh` para volver a iniciar el servidor. 

Para eliminar la instancia de Congreso Virtual, junto con los datos y la base de datos utilice el comando

    ./scripts/configure.sh --clean

Le pedirá una confirmación adicional para verificar que esté seguro con `--yes`

El script de configure tambien permite realizar la actualización del codigo del repositorio a la carpeta dist. Para ello, pare Congreso Virtual, sincronice su git con `git pull` y luego inicie la actualización con

    ./scripts/configure.sh --update

Esto rescatará la configuración y data persistente de Congreso Virtual, y luego actulalizará el código fuente, para finalmente efectuar la restauración de los datos de la antigua versión. Una vez terminado el proceso deberá recompilar el frontend con los pasos descritos mas arriba.

## Instalación con Jenkins
Se ha incluido además en el repositorio un fichero Jenkinsfile para que pueda integrar Congreso Virtual a su entorno de Integración Continua si lo desea (escencialmente, realiza exactamente las mismas tareas que la instalación rápida). 

Antes de empezar asegúrese que el nodo Jenkins donde trabajará tenga instalado Docker como especifica los requerimientos, además de asegurarse que el usuario Jenkins forma parte del grupo Docker (para que pueda ejecutar contenedores):

    usermod -aG docker your_username
Una vez listo, agregue Congreso Virtual a Jenkins como Pipeline, apuntando el origen al repositorio. Es importante mencionar que deberá configurar la plataforma antes de construirla, por lo que deberá activar la opción **"Esta ejecución debe parametrizarse"** con un parámetro de **tipo texto** llamado `CONGRESO_ENV` . El contenido del parámetro como base deberá ser la del archivo [DOCS/jenkins_env_example](https://github.com/eii-pucv/congreso-virtual/blob/master/DOCS/jenkins_env_example) del repositorio. 

![Parametrizado Jenkins](https://raw.github.com/eii-pucv/congreso-virtual/master/DOCS/includes/docs_parametrizado.png)

Acá, o al momento de ejecución podrá personalizar los parámetros a su gusto, tal como indican las instrucciones del fast_setup/update. 

Al iniciar la tarea detectará si CongresoVirtual ya se encuentra funcionando o no, y dependiendo de ello instalará o actualizará la plataforma automáticamente, bajo lo indicado en éste manual. 