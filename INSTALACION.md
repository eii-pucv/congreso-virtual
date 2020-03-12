
# Guía de instalación de Congreso Virtual

Ofrecemos dos alternativas de instalación de la Plataforma Congreso Virtual. Una instalación completa desde la clonar el repositorio y su configuración de ambiente y dependencias: [Instalación tradicional](#Instalación-tradicional). La otra alternativa, es usar contenedores  Docker, que permite una instalación más ágil, ya que configurando algunos párametros y ejecutando unos pocos comandos, podrás montar rápidamente la Plataforma: [Instalación con Docker](#Instalación-con-Docker).

---

## Instalación tradicional

---

La arquitectura de Congreso Virtual está compuesta con 3 partes para su correcto funcionamiento: Backend, Frontend y Data-Analytics.

Por lo tanto, para instalar y hacer funcionar el sistema, deberá clonar el repositorio completo en una carpeta arbitraria que ud. elija, ya que luego tendrá mover las carpetas contenidas en el repositorio a las rutas que deben quedar cada una de las partes.

## Instalación de Ambiente (componente obligatorios)

### Preparación del Sistema Operativo

1. Requerimientos:
    * Sistema Operativo Requerido: CentOS 7
    * Firewal: No configurado

2. Actualizar paquetería del sistema operativo.

```
        sudo yum update
```

3. Instalar paqueterías EPEL y REMI, necesarias para obtener numerosas dependencias del proyecto.

```
        sudo yum install epel-release
        sudo rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-7.rpm
```

4. Instalar herramientas de uso general.

```
        sudo yum install zip unzip gcc-c++ make screen
```

5. Instalar paquetes necesarios para la generación de reportes en PDF por parte de la API.

```
        sudo yum install libXrender wkhtmltopdf
```

6. Preparar la carpeta `/var/www` para que pueda ser usada por el usuario actual, y por el servidor HTTP de
Apache. Reemplazar USUARIO por el usuario actual de sistema.

```
        sudo chown -R USUARIO:apache /var/www/
        sudo chmod -R 755 /var/www/
```

---

### Instalación de Base de Datos

1. Instalar MariaDB desde el repositorio EPEL.

```
        sudo chown -R USUARIO:apache /var/www/
        sudo chmod -R 755 /var/www/
```

2. Iniciar servicio, y marcarlo para inicio automático en caso de reinicio del servidor.

```
        sudo systemctl enable mariadb
        sudo service mariadb start
```

3. Efectuar configuración inicial de MySQL. Contestar las preguntas acordes a como desee su configuración.

```
        /usr/bin/mysql_secure_installation
```

4. Crear una base de datos y un usuario para la plataforma de Congreso Virtual. Guarde el usuario,contraseña y bases de datos creadas para su uso mas adelante.

A modo de ayuda, se muestra un ejemplo de cómo debe efectuarse la configuración, aunque puede
efectuarse de cualquier forma como el instalador desee.

```
        sudo mysql -u root -p
        mysql> CREATE DATABASE mibd;
        mysql> CREATE USER 'miusuario' IDENTIFIED BY 'mipassword';
        mysql> GRANT USAGE ON *.* TO 'congreso'@localhost IDENTIFIED BY 'congreso';
        mysql> GRANT USAGE ON *.* TO 'congreso'@'%' IDENTIFIED BY 'congreso';
        mysql> GRANT ALL privileges ON `congreso`.* TO 'congreso'@localhost;
        mysql> FLUSH PRIVILEGES;
```

---

### Instalación del servidor web

1. Instalar servidor Apache HTTPD Server.

```
        sudo yum --enablerepo=remi,epel install httpd -y
```

2. Iniciar servicio y marcarlo para inicio automático en caso de reinicio de servidor.

```
        sudo systemctl enable httpd
        sudo service httpd start
```

3. Configurar firewall para permitir comunicación por puerto 80. Puede repetir el paso si desea habilitar HTTPS (puerto 443) si gusta.

```
        sudo firewall-cmd --zone=public --add-port=80/tcp --permanent
        sudo firewall-cmd –reload
```

4. Configurar servidor web apache para servir una página web (backend/api) en `/var/www/congresovirtual-backend/public`. Si desea puede cambiar la ruta a otra, bajo su responsabilidad. Del mismo modo, puede
modificar esta configuración para permitir HTTPS si lo desea.

5. Agregar el siguiente contenido al fichero `/etc/httpd/conf/httpd.conf` (puede usar editores como Vim o
Nano, o bien editores integrados de su cliente FTP para tal fin). Reemplace dominiocongresoapi.com por
el dominio que utilizará.

```
        <VirtualHost *:80>
            ServerName dominiocongresoapi.com
            DocumentRoot /var/www/congresovirtual-backend/public
            <Directory /var/www/congresovirtual-backend>
                AllowOverride All
            </Directory>
        </VirtualHost>
```

6. Configurar servidor web de Apache para servir el frontend del sitio en `/var/www/congresovirtual-frontend/dist`. Si desea puede cambiar la ruta a otra, bajo su responsabilidad. Del mismo modo, puede modificar esta configuración para permitir HTTPS si lo desea.

7. Agregar el siguiente contenido al fichero `/etc/httpd/conf/httpd.conf` (puede usar editores como Vim o Nano, o bien editores integrados de su cliente FTP para tal fin). Reemplace `dominiocongreso.com` por el dominio que utilizará.

```
        <VirtualHost *:80>
            ServerName dominiocongreso.com
            DocumentRoot /var/www/congresovirtual-frontend/dist
            <Directory /var/www/congresovirtual-frontend/dist>
                AllowOverride All
            </Directory>
        </VirtualHost>
```

8. Reinicie el servidor web de Apache

```
        sudo service httpd restart
```

---

### Instalación de PHP y Composer

1. Activar la paquetería REMI para PHP 7.3, previamente instalada.

```
sudo yum install yum-utils
sudo yum-config-manager --enable remi-php73
```

2. Instalar PHP, junto con las extensiones necesarias para el funcionamiento de congreso.
```
        sudo yum --enablerepo=remi,epel install php php-zip php-mysql php-mcrypt php-xml php-mbstring php-gd
```

3. Descargar e instalar Composer desde el sitio oficial (las versiones de paqueterías usualmente presentan incompatibilidades).

```
        curl -sS https://getcomposer.org/installer | php
        sudo mv composer.phar /usr/bin/composer
        sudo chmod +x /usr/bin/composer
```

4. Configurar PHP para que acepte la subida de ficheros de un mayor tamaño al que viene configurado por defecto. Para ello se debe modificar el fichero `/etc/php.ini` y establecer las siguientes directivas con los siguientes valores recomendados o cómo se estime conveniente.

```
post_max_size = 60M
file_uploads = On
upload_max_filesize = 20M
max_file_uploads = 20
```

5.  Una vez configurado PHP debe reiniciar el servidor web de Apache.

```
        sudo service httpd restart
```

---

### Instalación de GIT

1. Instalar GIT desde el gestor de paquetes

```
        sudo yum install git
```

---

### Instalación de Elasticsearch

1. Agregar paquetería propia de Elasticsearch al sistema operativo. Se empezará agregando la llave GPG.
```
        sudo rpm --import https://artifacts.elastic.co/GPG-KEY-elasticsearch
```

2. Luego hay que editar el archivo `/etc/yum.repos.d/elasticsearch.repo` (crearlo si no existe) y colocar el siguiente contenido en él.

```
        [elasticsearch]
        name=Elasticsearch repository for 7.x packages
        baseurl=https://artifacts.elastic.co/packages/7.x/yum
        gpgcheck=1
        gpgkey=https://artifacts.elastic.co/GPG-KEY-elasticsearch
        enabled=0
        autorefresh=1
        type=rpm-md
```

3. Instalar Elasticsearch desde la paquetería recién añadida.

```
        sudo yum install --enablerepo=elasticsearch elasticsearch
```

4. Iniciar servicio y marcarlo para inicio automático en caso de reinicio de servidor.

```
        sudo /bin/systemctl daemon-reload
        sudo /bin/systemctl enable elasticsearch.service
        sudo systemctl start elasticsearch.service
```

---

### Instalación de NodeJS

Instalar NodeJS 12.x desde los repositorios oficiales de Nodesource.

```
        Instalar NodeJS 12.x desde los repositorios oficiales de Nodesource.
```

---

### Instalación de Python

1. Instalar Python junto con las librerías de cabecera del anterior y MariaDB (usado para las dependencias a
instalar).

```
        sudo yum install python36 mariadb-devel python36-devel
```

2. Actualizar el gestor de paquetes Pip3.
```
        sudo python3.6 -m ensurepip
```

---

### Clonar el Repositorio

Clonar el repositorio usando el comando:
```
git clone https://github.com/eii-pucv/congreso-virtual.git
```

---

### Instalación y configuración del Backend/API

1. Desde el repositorio clonado, mover completamente la carpeta `congresovirtual-backend` a la ruta `/var/www/`
2. Asignar permisos indicados a las carpetas del proyecto recién copiado.

```
sudo chown -R $USER:apache /var/www/
sudo chmod -R 755 /var/www/congresovirtual-backend/
sudo chmod -R 755 /var/www/congresovirtual-backend/storage
sudo chcon -R -t httpd_sys_rw_content_t /var/www/congresovirtual-backend/storage
```

3. Inicializar dependencias del backend con Composer.

```
cd /var/www/congresovirtual-backend
```

4. En esta carpeta crear un archivo llamado `.env`

_El contenido de este archivo contendrá la configuración de la base de datos, entre otros atributos._
```
APP_NAME="Congreso Virtual"
APP_ENV=staging
APP_KEY=
APP_DEBUG=false
*APP_URL=http://url_api/api
*APP_CLIENT_URL=http://url_frontend/
APP_ANALYTIC_URL=http://127.0.0.1:8080
*ELASTICSEARCH_ENABLED=true
*ELASTICSEARCH_HOSTS="localhost:9200"
LOG_CHANNEL=stack
DB_CONNECTION=mysql
*DB_HOST=127.0.0.1
*DB_PORT=3306
*DB_DATABASE=
*DB_USERNAME=
*DB_PASSWORD=
BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
*MAIL_DRIVER=smtp
*MAIL_HOST=smtp.dominio.com
*MAIL_PORT=587
*MAIL_USERNAME=direccion@dominio.com
*MAIL_PASSWORD=
*MAIL_FROM_ADDRESS=direccion@dominio.com
MAIL_FROM_NAME="Congreso Virtual"
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
*TWITTER_ID=
*TWITTER_SECRET=
*TWITTER_URL=http://url_api/api/auth/twitter/callback
*FACEBOOK_ID=
*FACEBOOK_SECRET=
*FACEBOOK_URL=http://url_api/api/auth/facebook/callback
*GOOGLE_ID=
*GOOGLE_SECRET=
*GOOGLE_URL=
*GITHUB_ID=
*GITHUB_SECRET=
*GITHUB_URL=http://url_api/api/auth/github/callback
*CLAVEUNICA_CLIENT_ID=
*CLAVEUNICA_SECRET_ID=
*CLAVEUNICA_REDIRECT=http://url_api/api/auth/claveunica/callback
```

5.	Cambiar los valores resaltados con (*) por los siguientes:


|Valor(es) | Descripción|
|--- | ---|
| APP_URL | URL al backend/api (lo que estamos montando ahora) |
| APP_CLIENT_URL | URL al frontend |
| DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD | Configuración de la base de datos  previamente hecha (como referencia, el puerto MySQL predeterminado es el 3306, y el host de bases de datos es localhost, al menos que se quiera especificar algo distinto). |
| ELASTICSEARCH_ENABLED, ELASTICSEARCH_HOSTS | Configuración de Elasticsearch, por defecto Elasticsearch elige el puerto 9200 para escuchar las conexiones, pero si se cambia e incluso si se ejecutará en otro servidor, se debe especificar en este archivo de configuración. |
| MAIL_DRIVER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS | Configuración del correo electrónico desde el cual se enviarán las notificaciones a los usuarios de Congreso Virtual.|
| TWITTER_ID, TWITTER_SECRET, TWITTER_URL, FACEBOOK_ID, FACEBOOK_SECRET, FACEBOOK_URL, GOOGLE_ID, GOOGLE_SECRET, GOOGLE_URL, GITHUB_ID, GITHUB_SECRET, GITHUB_URL, CLAVEUNICA_CLIENT_ID, CLAVEUNICA_SECRET_ID, CLAVEUNICA_REDIRECT | Configuración de claves identificadoras para el registro de usuarios mediante servicios externos tales como Twitter, Facebook, Google, GitHub y Clave Única (Registro Civil). |


**Importante:** Por favor borrar los (*) del archivo, ya que son utilizados solo como guía visual para identificar los párametros que debe configurar.

6. Instalar las dependencias del backend con Composer

```
composer install
```

7. Una vez configurado el archivo de variables de entorno `.env`, es recomendable ejecutar el siguiente comando para que Laravel actualice la cache con dichas configuraciones.

```
php artisan config:clear
```

8. Finalizar instalación creando una API key para el backend y el relleno inicial de bases de datos.

```
php artisan key:generate
php artisan migrate:refresh
```

Instalar scripts SQL adicionales si existiesen en la base de datos utilizada.
Una vez llenada la base de datos con los datos iniciales y cada vez que se editen datos directamente desde la base de datos, es necesario ejecutar el siguiente comando que tiene por objetivo actualizar los índices de datos generados en Elasticsearch.
php artisan search:reindex

Instalar paquetes opcionales de assets si existiesen, siguiendo los pasos correspondientes.

### Instalación y configuración del Frontend

---

1. Desde el repositorio clonado, mover completamente la carpeta `congresovirtual-frontend` a la ruta `/var/www/`
2. Instalar dependencias del frontend, entrando a la carpeta de éste.

```
cd /var/www/congresovirtual-frontend
npm install
```

3. Configurar el acceso al backend/API, editando para ello el archivo `/src/backend/data_server.js`. Allí, buscar `API_URL` y reemplazarla por la URL configurada en la instalación del backend. Por ejemplo:

```
const API_URL = 'http://url_api/api’;
export {
    API_URL
};
```

**Importante:** Cada vez que se haga un cambio, habrá que correr los siguientes pasos en adelante en cuanto a frontend de Congreso Virtual. 

4. Correr el compilador de VueJS ejecutando el siguiente comando en la carpeta raíz del proyecto.

```
cd/var/www/congresovirtual-frontend
npm run build --max-old-space-size=4096
```

_Reemplace 4096 por la cantidad de RAM que piensa asignarle para la compilación. Usualmente aquel valor funciona bien en la mayoría de los casos. Espere un momento, ya que el proceso puede tardar unos minutos sin responder._

5. Al terminar la compilación, se habrá generado una carpeta dist. Dentro de esta carpeta cree un fichero llamado `.htaccess`. El contenido del archivo debe ser el siguiente:

```
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteRule ^index\.html$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.html [L]
</IfModule>
```

_Cuando se regenere el proyecto, este archivo puede ser sobreescrito o eliminado, por lo que deberá crear el archivo nuevamente. De forma alternativa, puede dejar una copia de seguridad lista para restaurar luego de correr el compilador de VueJS._

### Instalación y configuración de Data-Analytics

---

1. Desde el repositorio clonado, mover completamente la carpeta `congresovirtual-data-analytics` a la ruta `/var/www/`
2. Instalar las dependencias de los módulos de analítica.

```
cd /var/www/congresovirtual-data-analytics
sudo pip3 install -r requirements.txt
```

3. Editar el archivo `dbconfig.py`. En ella, ingresar los datos de conexión de base de datos de congreso.

```
CONGRESO_MYSQL_HOST="localhost"
CONGRESO_MYSQL_USER="congreso"
CONGRESO_MYSQL_PASSWD="congreso"
CONGRESO_MYSQL_DATABASE="congreso"
```

4. Configurar el sistema operativo para que inicie el servicio de analítica ante un reinicio del servidor con crontab. Para tal fin, iniciar Crontab.

```
crontab -e
```

_Crontab utiliza el editor de texto Vim. Si desea utilizar otro editor (como Nano), luego de iniciar sesión por SSH escribir el siguiente comando._

```
export EDITOR=nano
```

5.	En el editor escribir la siguiente línea.

```
@reboot screen -d -m bash -c "cd /var/www/congresovirtual-data-analytics/; python3 -m flask run"
```

6. Guardar y salir.
7. Indicar al sistema operativo que debe correr Cron al inicio del sistema.

```
sudo chkconfig crond on
sudo systemctl enable crond.service
```

8.	Iniciar el servicio de analítica ejecutando lo siguiente:

```
screen -d -m bash -c "cd /var/www/congresovirtual-data-analytics/; python3 -m flask run"
```

_Para ver el estado del servidor de analítica, se deberá usar una terminal paralela creada para tal fin. Para acceder a la terminal paralela, escribir screen -r y para salir de ella hay que presionar las teclas Ctrl+a+d ._

---

## Instalación con Docker

---
Congreso Virtual ofrece la opción de Instalación rápida, el cual luego de configurar algunos parámetros permite construir y correr Congreso Virtual en su servidor mediante contenedores Docker. Es una alternativa más ágil que montar la plataforma de forma tradicional, según describe la documentación.

1. Para efectuar la instalación rápida de Congreso Virtual se necesitará tener instalado en su sistema operativo los siguientes componentes:

* [Docker CE](https://docs.docker.com/get-started/)
* [Docker-compose](https://docs.docker.com/compose/install/)

2. Además, asegúrese que el servidor posea los puertos 80, 443 y 3306 desocupados.

**NOTAS:**

* Jenkins: Es posible ejecutar la instalación rápida desde su servidor de Integración Continua (Jenkins) vía este repositorio. Ver mas abajo el capítulo [Instalación con Jenkins](#instalación-con-jenkins).

* Windows: Si bien, estos scripts son hechos para bash, y por ende, pensados para sistemas basados en UNIX (Linux, OSX, otros), también pueden correrse en **Windows** con ayuda de componentes adicionales, tales como _MSYS2_, _Git for Windows_ o _WSL_.

3. Luego de clonar el repositorio de Congreso Virtual, por favor copie el archivo `.env` localizado en `/volumefiles/` a la raiz del repositorio, y configure los parámetros de acuerdo a su criterio (viene ya preconfigurado, aunque deberá especificar algunos parámetros tales como las URL de funcionamiento).

**Importante:** El script de instalación **CREARÁ una base de datos** en un contenedor Docker, no es necesario que usted prepare una. Los datos que aparece en la configuración son usados al momento de crear las tablas.

**NOTA:** Si desea **habilitar HTTPS**, por favor, ponga su certificado en `/volumefiles/certs/cert.crt` y `/volumefiles/certs/cert.key`

4. Una vez listo ejecute el siguiente comando para iniciar la instalación. Es recomendable hacer primero `cd` a la carpeta `scripts` y luego ejecutar cada comando. Se aplica lo mismo para todos los comandos de este manual.

```
./scripts/fast_setup.sh
```

* Se iniciará automáticamente el proceso de instalación y puesta en punto, el cual dependiendo del rendimiento de su servidor tardará dentro de 30 a 40 minutos.

5. Una vez finalizado el proceso, deje aproximadamente unos 10 a 15 minutos adicionales para que Congreso Virtual termine de inicializarse en segundo plano (librerías, dependencias y bases de datos).  Una forma de comprobar si Congreso Virtual ha inicializado satisfactoriamente es entrando a la URL de la API. Si este indica un error 503 Service Unavailable, entonces este no ha terminado de inicializar aún. 

6. Poblar Base de Datos: Una vez funcionando Congreso Virtual, deseará poblar éste con información inicial. Para esto, se recomienda instalar los datos iniciales en la base de datos ejecutando este comando.

```
    ./scripts/installinitialdata.sh
```

* Se creará con ello un **usuario** `admin@congresovirtual.cl` con **contraseña** `abc123456`

* En cualquier momento usted puede ver los **logs** de Congreso Virtual (y sus componentes) ejecutando

```
    ./scripts/livelog.sh
```

* Puede **detener** la ejecución de congreso virtual en cualquier momento ejecutando el comando:

```
    ./scripts/stop.sh
```

* De la misma forma puede **volver a iniciar** el servidor ejecutando

```
    ./scripts/run.sh
```

---

### Actualización con uso de consola bash

De la misma forma que la instalación, Congreso Virtual permite una forma rápida de actualizar la plataforma a su versión más reciente.

1. Para ello, simplemente actualice su repositorio (git pull), y luego ejecute el siguiente comando para iniciar la actualización.

```
    ./scripts/fast_update.sh
```

2. Deberá esperar aproximadamente 30 minutos dependiendo del rendimiento del servidor (durante este periodo Congreso Virtual estará fuera de servicio). Una vez finalizado y esperado los 5 minutos post instalación, Congreso Virtual se encontrará actualizado y nuevamente listo para operar.

---

## Mantenimiento manual con scripts

Si desea ir paso por paso por los procesos de instalación y actualización, es posible operar los scripts de forma manual.

El primer script es `./scripts/configure.sh`  cuya tarea es ayudar en las tareas de configurar una instancia de Congreso Virtual en un entorno especial para ello, creando una carpeta `dist`, donde en `volumefiles` contiene las configuraciones de cada componente. `./scripts/configure.sh` se encarga de tomar las configuraciones en el archivo `.env` y las reparte en los distintos componentes.

1. Para inicializar una carpeta `dist` se deberá ejecutar el comando

```
    ./scripts/configure.sh --prepare=$UID
```

2. Donde UID es el ID de usuario actual. Una vez inicializado, el script invitará a configurar el archivo `/dist/volumefiles/.env` con la información correspondiente.

**NOTA:** Si desea habilitar HTTPS, por favor, ponga su certificado en `/dist/volumefiles/certs/cert.crt` y `/dist/volumefiles/certs/cert.key`

3. Para aplicar la configuración del `.env` ejecutar

```
    ./scripts/configure.sh --applyconfig
```

4. Una vez finalizada la aplicación de la configuración, podrá compilar el frontend de la carpeta dist (basado en vue), ejecutando el comando

```
    ./scripts/compile_frontend.sh
```

* Inicializará una instancia temporal de vue y compilará el frontend (esto puede tardar mucho tiempo, y puede consumir mucha RAM). Luego añadirá archivos especiales y asignará permisos para apache.  

5. Con el frontend compilado, ya se puede inicializar el proyecto con el comando 

```
    ./scripts/run.sh
``` 

* Si se **corre por primera vez**, se inicializarán los contenedores, y se crearán los archivos de bases de datos segun lo descrito en el archivo de configuración en `/dist/volumefiles/mysql` y `/dist/volumefiles/elasticsearch`. Si no es la primera vez que se corren, entonces se usarán los archivos ya existentes.

6. Una vez que haya finalizado el inicio, en segundo plano Congreso Virtual correrá las actualizaciónes de Composer y Artisan, lo que puede tardar un tiempo. Una forma de comprobar si Congreso Virtual ha inicializado satisfactoriamente es entrando a la URL de la API. Si este indica un error 503 Service Unavailable, entonces este no ha terminado de inicializar aún. Además, uede ver el estado y los logs de Congreso y sus componentes con

```
    ./scripts/livelog.sh
``` 

7. Una vez funcionando Congreso Virtual por primera vez, deseará poblar éste con información inicial. Para aquel fin, se recomienda instalar los datos iniciales en la base de datos ejecutando este comando.

```
    ./scripts/installinitialdata.sh
```

* Se creará con ello un **usuario** `admin@congresovirtual.cl` con contraseña `abc123456`

**NOTA:** Si necesita cargar un set de datos especiales, puede editar el archivo `/dist/volumefiles/initialdata.sql` e ingresarlos allí. Luego correr `./scripts/installinitialdata.sh` para aplicarlo.

* De la misma forma, puede **detener** la ejecución de Congreso Virtual con el comando

```
    ./scripts/stop.sh
```

* Si bien esto destruye los contenedores, la información persistente (storage, bases de datos, etc) se encuentra intacta en la carpeta `dist`. Después, puede usar el comando `./scripts/start.sh` para volver a iniciar el servidor.

* Para **eliminar** la instancia de Congreso Virtual, junto con los datos y la base de datos utilice el comando

```
    ./scripts/configure.sh --clean
```

  * Le pedirá una confirmación adicional para verificar que esté seguro con `--yes`

* El script de configure tambien permite realizar la **actualización** del código del repositorio a la carpeta `dist`. Para ello, pare Congreso Virtual, sincronice su git con `git pull` y luego inicie la actualización con

```
    ./scripts/configure.sh --update
```

* Esto rescatará la configuración y data persistente de Congreso Virtual, y luego actulalizará el código fuente, para finalmente efectuar la restauración de los datos de la antigua versión. Una vez terminado el proceso deberá recompilar el frontend con los pasos descritos mas arriba.

---

## Instalación con Jenkins

Se ha incluido además en el repositorio un fichero Jenkinsfile para que pueda integrar Congreso Virtual a su entorno de Integración Continua si lo desea (escencialmente, realiza exactamente las mismas tareas que la instalación rápida).

**importante:** Antes de empezar asegúrese que el nodo Jenkins donde trabajará tenga **instalado Docker** como especifica los requerimientos, además de asegurarse que el usuario Jenkins forma parte del **grupo Docker** (para que pueda ejecutar contenedores):
```
    usermod -aG docker your_username
```
Una vez listo, agregue Congreso Virtual a Jenkins como Pipeline, apuntando el origen al repositorio. Es importante mencionar que deberá configurar la plataforma antes de construirla, por lo que deberá activar la opción **"Esta ejecución debe parametrizarse"** con un parámetro de **tipo texto** llamado `CONGRESO_ENV` . El contenido del parámetro como base deberá ser la del archivo [DOCS/jenkins_env_example](https://github.com/eii-pucv/congreso-virtual/blob/master/DOCS/jenkins_env_example) del repositorio.

![Parametrizado Jenkins](https://raw.github.com/eii-pucv/congreso-virtual/master/DOCS/includes/docs_parametrizado.png)

Acá, o al momento de ejecución podrá personalizar los parámetros a su gusto, tal como indican las instrucciones del `fast_setup/update`.

Al iniciar la tarea detectará si CongresoVirtual ya se encuentra funcionando o no, y dependiendo de ello instalará o actualizará la plataforma automáticamente, bajo lo indicado en éste manual.
