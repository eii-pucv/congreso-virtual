# Guía de instalación de Congreso Virtual

La arquitectura de Congreso Virtual está compuesta con 3 partes para su correcto funcionamiento: Backend, Frontend y Data-Analytics.

Por lo tanto, para instalar y hacer funcionar el sistema, deberá clonar el repositorio completo en una carpeta arbitraria que ud. elija, ya que luego tendrá mover las carpetas contenidas en el repositorio a las rutas que deben quedar cada una de las partes.

Clonar el repositorio usando el comando:
```
git clone https://github.com/eii-pucv/congreso-virtual.git
```

### Instalación y configuración del Backend/API

---

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
composer install
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

6. Una vez configurado el archivo de variables de entorno `.env`, es recomendable ejecutar el siguiente comando para que Laravel actualice la cache con dichas configuraciones.

```
php artisan config:clear
```

7. Finalizar instalación creando una API key para el backend y el relleno inicial de bases de datos.

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