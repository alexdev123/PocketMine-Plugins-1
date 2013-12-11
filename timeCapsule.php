<?php
/*
__PocketMine Plugin__
name=TimeCapsule
description=The numerated changes-only backup assistant for PocketMine-MP
version=0.2
author=Falk
class=TimeCapsule
apiversion=10
*/
class TimeCapsule implements Plugin{
private $api, $path;
public function __construct(ServerAPI $api, $server = false){
$this->api = $api;
}

public function init(){
$this->api->console->register("backup", "Create a new backup", array($this, "backup"));
$this->api->console->register("restore", "Restore the server to a previous backup", array($this, "restore"));
if (file_exists(FILE_PATH . "backups")) {
console("[TimeCapsule] Backup manager started");
$this->config = new Config(FILE_PATH."backups/data.yml", CONFIG_YAML, array(0,0));
	}
	else {
if(mkdir(FILE_PATH . "backups", 0700) == true){
console("[TimeCapsule] Configured Successfully");
console("Check the FAQ on forums.pocketmine.net for help.");
$this->config = new Config(FILE_PATH."backups/data.yml", CONFIG_YAML, array(0,0));
}
else {
	console("[TimeCapsule] Failed to configure, check perms");
}
}
}

public function __destruct(){}
   public function recurse_copy($src,$dst,$past,$time) { 
    $dir = opendir($src); 
    @mkdir($dst,0700,true); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                $this->recurse_copy($src . '/' . $file,$dst . '/' . $file,$past . '/' . $file,$time); 
                
            } 
            else { 
            if (filemtime($src . '/' . $file) > $time) {
            	
                if(copy($src . '/' . $file,$dst . '/' . $file) == false){
                    console($file);
                }
                }
                else {
                	link($past . '/' . $file, $dst . '/' . $file);

                }
            } 
        } 
    } 
    closedir($dir); 
} 
public function restore_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst,0755,true); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                $this->rest_copy($src . '/' . $file,$dst . '/' . $file);
                
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}
public function backup($cmd, $params, $issuer, $alias, $args, $issuer){
$data = $this->api->plugin->readYAML(FILE_PATH. "backups/data.yml");
    $id = $data[0]+1;
    console("[TimeCapsule] Backup started with ID:" . $id);
  console("[TimeCapsule] Making backup directories...");
    mkdir(FILE_PATH . "backups/" . $id);
     mkdir(FILE_PATH . "backups/" . $id . "/plugins");
     mkdir(FILE_PATH . "backups/" . $id . "/players");
     mkdir(FILE_PATH . "backups/" . $id . "/worlds");
     console("[TimeCapsule] File transfer started");
$this->recurse_copy(FILE_PATH . "plugins",FILE_PATH . "backups/" . $id . "/plugins",FILE_PATH . "backups/" . $data[0] . "/plugins", $data[1]);
$this->recurse_copy(FILE_PATH . "players",FILE_PATH . "backups/" . $id . "/players",FILE_PATH . "backups/" . $data[0] . "/players", $data[1]);
$this->recurse_copy(FILE_PATH . "worlds",FILE_PATH . "backups/" . $id . "/worlds",FILE_PATH . "backups/" . $data[0] . "/worlds", $data[1]);
$data[0] = $data[0] + 1;
$data[1] = strtotime("now");
$this->api->plugin->writeYAML(FILE_PATH. "backups/data.yml",$data);
console("Backup may take some time to complete");

}
public function restore($cmd, $params, $issuer, $alias, $args, $issuer){
if (isset($params[0])) {
console("[TimeCapsule] Restore Started");
console("Server may be unstable during this process");
	$this->restore_copy(FILE_PATH . "backups/" . $params[0],FILE_PATH);
}
else {
	console("[TimeCapsule] Backup not specified");
}
}
}
?>