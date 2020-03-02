#!/usr/bin/php
<?php

/**
Congreso Virtual deploy configurator.
2020 BID and PUCV

This file is copyrighted under BID License. See LICENSE.md for details. 
**/

#===================================================================================================

function congreso_check_error($out, $error_msg) {
	if (!$out) {
		echo "ERROR: ". $error_msg . "\n\n";
		exit(1);
	}
}

function congreso_load_env_file() {
	echo "Reading env file\n";
		
	$env = file("/app/dist/volumefiles/.env", FILE_IGNORE_NEW_LINES);
	congreso_check_error($env, "Error while reading env file.");
	$options = array();
		
	//fill options
	for($i=0; $i< count($env); $i++) {
		if (strpos($env[$i], '#') == false && strpos($env[$i], '=') !== false) {
			//tokenize option
			$command = explode("=", $env[$i]);
			$options[$command[0]] = $command[1];	
		}
	}
	
	return $options;
}

function replace_in_file($filename, $array_values) {
	$str=file_get_contents($filename);
	congreso_check_error($str, "Error while opening file " . $filename);
	
	foreach($array_values as $key => $value) {
		$str=str_replace($key, $value, $str);
	}
	
	$out = file_put_contents($filename, $str);
	congreso_check_error($out, "Error while writing file " . $filename);
}

function congreso_execute($command) {
	$out = shell_exec($command . " ; echo $?");
	$out = rtrim($out);
	return !$out;
}

#===================================================================================================
function congreso_command_prepare($UID, $GID) {
	echo "Preparing environment for deploying Congreso Virtual\n";
	echo "Using UID = " . $UID ."\n";
	echo "Using GID = " . $GID ."\n";
	echo "Creating folder dist\n";
	$out = mkdir("/app/dist");
	congreso_check_error($out, "Error while creating folder dist.");
	echo "Copying project files into dist folder\n";
	$out = congreso_execute("cp -r /app/congresovirtual-backend /app/dist/congresovirtual-backend");
	congreso_check_error($out, "Error while copying backend folders.");
	$out = congreso_execute("cp -r /app/congresovirtual-frontend /app/dist/congresovirtual-frontend");
	congreso_check_error($out, "Error while copying frontend folders.");
	$out = congreso_execute("cp -r /app/congresovirtual-data-analytics /app/dist/congresovirtual-data-analytics");
	congreso_check_error($out, "Error while copying analytics folders.");
	echo "Copying volume files into dist\n";
	$out = congreso_execute("cp -r /app/volumefiles /app/dist/volumefiles");
	congreso_check_error($out, "Error while copying volume files.");
	echo "Copying docker files into dist\n";
	$out = congreso_execute("cp -r /app/dist/volumefiles/Dockerfile_php /app/dist/");
	congreso_check_error($out, "Error while copying docker files.");
	$out = congreso_execute("cp -r /app/dist/volumefiles/Dockerfile_python /app/dist/");
	congreso_check_error($out, "Error while copying docker files.");
	$out = congreso_execute("cp -r /app/dist/volumefiles/docker-compose.yml /app/dist/docker-compose.yml");
	congreso_check_error($out, "Error while copying docker files.");
	
	//adding user configuration string at end of env file
	echo "Adding UID configuration to dist env file\n";
	$out = file_put_contents("/app/dist/volumefiles/.env", "\nCONGRESO_USER_UID=" . $UID . "\n", FILE_APPEND);
	$out = file_put_contents("/app/dist/volumefiles/.env", "\nCONGRESO_USER_GID=" . $GID . "\n", FILE_APPEND);
	
	//setting permissions on dist folder
	echo "Setting proper permissions on dist folder\n";
	$out = congreso_execute("chown -R " . $UID . ":" . $GID . " /app/dist/");
	congreso_check_error($out, "Error while setting user permissions to dist folder.");
	$out = congreso_execute("chmod -R 776 /app/dist/");
	congreso_check_error($out, "Error while setting access to dist folder.");
	$out = congreso_execute("chown -R 33:" . $GID . " /app/dist/congresovirtual-backend/");
	congreso_check_error($out, "Error while setting user permissions to backend folder.");
	$out = congreso_execute("chmod -R 777 /app/dist/volumefiles/installinitialdata.sh");
	congreso_check_error($out, "Error while setting execution permissions to install initial data script.");
	
	echo "Deleting dummy file on mysql folder\n";
	$out = congreso_execute("rm -f /app/dist/volumefiles/mysql/delete.me");
	congreso_check_error($out, "Deleting dummy file on mysql folder.");
	
	echo "Setting proper permissions on my.cnf configuration file\n";
	$out = congreso_execute("chmod 0444 /app/dist/volumefiles/my.cnf");
	congreso_check_error($out, "Error setting proper permissions on my.cnf configuration file.");
	
	echo "\nDone!\nPlease now configure dist/volumefiles/.env file with the desired settings and then run configure --applyconfig";
}

#===================================================================================================

function congreso_command_clean() {
		echo "Clearing all files\n";
		echo "Deleting dist folder\n";
		$out = congreso_execute("rm -rf /app/dist/");
		congreso_check_error($out, "Error while deleting folder dist.");
		echo "Done\n";
}

#===================================================================================================

function congreso_command_applyconfig() {
	//applying configuration
		echo "Applying configuration from env file\n";
		
		$options = congreso_load_env_file();
		
		echo "Checking required settings\n";
		congreso_check_error((array_key_exists("APP_URL", $options) && $options["APP_URL"]!=""), "APP_URL missing.");
		congreso_check_error((array_key_exists("APP_CLIENT_URL", $options) && $options["APP_CLIENT_URL"]!=""), "APP_CLIENT_URL missing.");
		congreso_check_error((array_key_exists("CONGRESO_DPL_USE_HTTPS", $options) && $options["CONGRESO_DPL_USE_HTTPS"]!=""), "CONGRESO_DPL_USE_HTTPS missing.");
		congreso_check_error((array_key_exists("DB_DATABASE", $options) && $options["DB_DATABASE"]!=""), "DB_DATABASE missing.");
		congreso_check_error((array_key_exists("DB_USERNAME", $options) && $options["DB_USERNAME"]!=""), "DB_USERNAME missing.");
		congreso_check_error((array_key_exists("DB_PASSWORD", $options) && $options["DB_PASSWORD"]!=""), "DB_PASSWORD missing.");
		congreso_check_error((array_key_exists("CONGRESO_USER_UID", $options) && $options["CONGRESO_USER_UID"]!=""), "CONGRESO_USER_UID missing.");
		congreso_check_error((array_key_exists("CONGRESO_USER_GID", $options) && $options["CONGRESO_USER_GID"]!=""), "CONGRESO_USER_GID missing.");
		
		echo "Writing apache configuration\n";
		replace_in_file("/app/dist/volumefiles/httpd.conf", array(
			"{{CONGRESO_DPL_USE_HTTPS}}" => ($options["CONGRESO_DPL_USE_HTTPS"]=="1"?"":"#"),
			"{{APP_CLIENT_URL}}" => explode("/", explode("://", $options["APP_CLIENT_URL"])[1])[0],
			"{{APP_URL}}" => explode("/", explode("://", $options["APP_URL"])[1])[0]
		));
		
		echo "Writing docker compose configuration\n";
		replace_in_file("/app/dist/docker-compose.yml", array(
			"{{DB_DATABASE}}" => $options["DB_DATABASE"],
			"{{DB_USERNAME}}" => $options["DB_USERNAME"],
			"{{DB_PASSWORD}}" => $options["DB_PASSWORD"],
		));
		
		echo "Writing analytics configuration\n";
		replace_in_file("/app/dist/congresovirtual-data-analytics/dbconfig.py", array(
			"{{DB_DATABASE}}" => $options["DB_DATABASE"],
			"{{DB_USERNAME}}" => $options["DB_USERNAME"],
			"{{DB_PASSWORD}}" => $options["DB_PASSWORD"],
		));
		
		echo "Writing initial data script configuration\n";
		replace_in_file("/app/dist/volumefiles/installinitialdata.sh", array(
			"{{DB_DATABASE}}" => $options["DB_DATABASE"],
			"{{DB_USERNAME}}" => $options["DB_USERNAME"],
			"{{DB_PASSWORD}}" => $options["DB_PASSWORD"],
		));
		
		echo "Writing backend configuration\n";
		$out = congreso_execute("cp -r /app/dist/volumefiles/.env /app/dist/congresovirtual-backend/.env");
		congreso_check_error($out, "Error while copying configuration to backend.");
		$out = congreso_execute("chown 33:" . $options["CONGRESO_USER_GID"] . " /app/dist/congresovirtual-backend/.env");
		congreso_check_error($out, "Error while setting user permissions to backend configuration.");
		
		echo "Writing frontend configuration\n";
		replace_in_file("/app/dist/congresovirtual-frontend/src/backend/data_server.js", array(
			"{{APP_URL}}" => $options["APP_URL"],
		));
		
		echo "Configuration is ready. Now you must execute compile_frontend to compile the frontend.\n";		
}

#===================================================================================================

function congreso_command_update() {
	echo "Updating congreso virtual ... \n";
	
	//read configuration
	$options = congreso_load_env_file();
	
	//create temp folder
	$out = congreso_execute("mkdir /app/tmp");
	congreso_check_error($out, "Error creating tmp folder.");
	
	//in dist/volumefiles save env, and folders: certs, elasticsearch, mysql
	echo "Moving configurations and data from apache, mysql and elasticsearch\n";
	$out = congreso_execute("mv -f /app/dist/volumefiles/mysql/ /app/tmp/");
	congreso_check_error($out, "Error while moving mysql data.");
	$out = congreso_execute("mv -f /app/dist/volumefiles/elasticsearch/ /app/tmp/");
	congreso_check_error($out, "Error while moving elasticsearch data.");
	$out = congreso_execute("mv -f /app/dist/volumefiles/certs/ /app/tmp/");
	congreso_check_error($out, "Error while moving apache certs data.");
	$out = congreso_execute("mv -f /app/dist/volumefiles/.env /app/tmp/");
	congreso_check_error($out, "Error while moving configuration.");
	
	//congresovirtual-backend: save folders storage, and delete storage/framework/cache and storage\framework\views
	echo "Moving storage data from backend\n";
	$out = congreso_execute("mv -f /app/dist/congresovirtual-backend/storage /app/tmp/");
	congreso_check_error($out, "Error while moving backend storage.");
	echo "Clearing backend caches\n";
	$out = congreso_execute("rm -rf /app/tmp/storage/framework/cache/*");
	congreso_check_error($out, "Error while clearing backend cache.");
	$out = congreso_execute("rm -rf /app/tmp/storage/framework/views/*");
	congreso_check_error($out, "Error while clearing backend view cache.");
	
	//delete dist
	echo "Deleting old congreso virtual files... \n";
	congreso_command_clean();
	
	//recreate dist folder
	echo "Installing new files... \n";
	congreso_command_prepare($options["CONGRESO_USER_UID"], $options["CONGRESO_USER_GID"]);
	
	//copy env file from tmp to dist location
	echo "Restoring old configuration data... \n";
	$out = congreso_execute("cp -f /app/tmp/.env /app/dist/volumefiles/");
	congreso_check_error($out, "Error while restoring old configuration data.");
	
	//apply configuration
	echo "Applying configuration to the new installation... \n";
	congreso_command_applyconfig();
	
	//restore certs elasticsearch and mysql
	echo "Restoring data from old version (backend, apache, mysql, elasticsearch)... \n";
	
	$out = congreso_execute("rm -rf /app/dist/volumefiles/certs && mv -f /app/tmp/certs /app/dist/volumefiles/");
	congreso_check_error($out, "Error while restoring apache certs.");
	$out = congreso_execute("rm -rf /app/dist/volumefiles/mysql && mv -f /app/tmp/mysql /app/dist/volumefiles/");
	congreso_check_error($out, "Error while mysql data.");
	$out = congreso_execute("rm -rf /app/dist/volumefiles/elasticsearch && mv -f /app/tmp/elasticsearch /app/dist/volumefiles/");
	congreso_check_error($out, "Error while elasticsearch data.");
	$out = congreso_execute("rm -rf /app/dist/congresovirtual-backend/storage && mv -f /app/tmp/storage /app/dist/congresovirtual-backend/");
	congreso_check_error($out, "Error while elasticsearch data.");
	
	echo "Cleaning up\n";
	$out = congreso_execute("rm -rf /app/tmp/");
	congreso_check_error($out, "Error while cleaning up.");
	
	echo "Main update process is ready. Now you must execute compile_frontend to compile the new frontend.\n";
}


#===================================================================================================

if (php_sapi_name() !== 'cli') {
    exit;
}

echo "\n================================================================================\n";
echo "Congreso Virtual Deploy configurator\n2020 BID and PUCV.\nThis program is copyrighted under BID license. See LICENSE.md for details\n";
echo "================================================================================\n\n";

//parse commands
$commands = array();
$commands_header = array();

for ($i=0; $i<count($argv); $i++) {
	if (strpos($argv[$i], '=') !== false) {
		//tokenize parameter
		$command = explode("=", $argv[$i]);
		array_push($commands, array(
			"name" => $command[0],
			"value" => $command[1]
			));
		array_push($commands_header, $command[0]);
		
	} else {
		array_push($commands, array(
			"name" => $argv[$i],
			"value" => NULL
			));
		array_push($commands_header, $argv[$i]);
	}
}

//for each command
if ( $index = array_search("--prepare" , $commands_header)!== FALSE ) {
	#===================================================================================================
	if ( $uidindex = array_search("--UID" , $commands_header)!== FALSE ) && ( $gidindex = array_search("--GID" , $commands_header)!== FALSE ) ) {
		congreso_check_error(false, "Missing UID and/or GID. Unable to continue");
	} else {
		//prepare enviroment
		congreso_command_prepare($commands[$uidindex]["value"], $commands[$gidindex]["value"],);
	}

	#===================================================================================================
} else if ( $index = array_search("--clean" , $commands_header)!== FALSE ) {
	//deleting enviroment
	if ( $index = array_search("--yes" , $commands_header)!== FALSE ) {
		congreso_command_clean();
	} else {
		congreso_check_error(false, "THIS WILL DELETE ALL ENVIROMENT AND DATA FILES FOR CONGRESO VIRTUAL. To confirm, please provide an additional --yes parameter.");
	}
	
	#===================================================================================================
} else if ( $index = array_search("--applyconfig" , $commands_header)!== FALSE ) {
	congreso_command_applyconfig();
	
		
		#===================================================================================================
} else if ( $index = array_search("--update" , $commands_header)!== FALSE ) {
	
	congreso_command_update();
	
	#===================================================================================================
} else {
	//print help
	
	echo "Possible options: \n\n";
	echo "--prepare=(UID)\tPrepares environment for deploying Congreso Virtual. Use it with --UID=uid and --GID=gid \n";
	echo "--clean\t\tDeletes environment and all data files of Congreso Virtual.\n";
	echo "--applyconfig\tTakes env configuration and applies to the project\n";
	echo "--update\tUpdates Congreso Virtual to the last version\n";
}

echo "\n";