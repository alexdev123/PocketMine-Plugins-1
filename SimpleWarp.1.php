<?php
/*
__PocketMine Plugin__
name=SimpleWarp
description=Simple plugin to make warps
version=0.2.1
author=Falk
class=simplewarp
apiversion=10
*/
class simplewarp implements Plugin{
private $api, $path;
public function __construct(ServerAPI $api, $server = false){
$this->api = $api;
}

public function init(){

$this->api->console->register("addwarp", "Create a new warp", array($this, "command"));
$this->api->console->register("delwarp", "Delete a warp", array($this, "command"));
$this->api->console->register("warp", "Warp to a location", array($this, "command"));
$this->api->console->register("openwarp", "Make a warp open to everyone", array($this, "command"));
$this->api->console->register("closewarp", "Make warp OPS only", array($this, "command"));

$this->config = new Config($this->api->plugin->configPath($this)."warps.yml", CONFIG_YAML, array());
$this->api->ban->cmdWhitelist("warp");
console("[INFO] SimpleWarp Loaded!");
}

public function __destruct(){}
public function command($cmd, $params, $issuer, $alias, $args, $issuer){
switch ($cmd) {
case "addwarp": 
if (isset($params[0])) {
	$data = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "warps.yml");
	if (array_key_exists($params[0], $data)) {
	$issuer->sendChat("[SimpleWarp] Warp exists with that name!");
	}
	else {
		$name = $params[0];
		$x = round($issuer->entity->x);
		$y = round($issuer->entity->y);
		$z = round($issuer->entity->z);
		$level = $issuer->level->getName();
$data["$name"] = array($x,$y,$z,$level);

		$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."warps.yml", $data);
$issuer->sendChat("[SimpleWarp] Warp Added!");

	}
	}
	else {
		$issuer->sendChat("Usage: /addwarp <NAME>");
	}

break;
case "delwarp": 
	$data = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "warps.yml");
	if (isset($params[0])) {
		if (array_key_exists($params[0], $data)) {
			unset($data[$params[0]]);
			$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."warps.yml", $data);
			$issuer->sendChat("[SimpleWarp] Warp removed!");
		}
		else {
			$issuer->sendChat("[SimpleWarp] Warp doesnt exist!");
		}
	}
else {
	$issuer->sendChat("Usage: /delwarp <NAME>");
}
break;
case "warp": 
$data = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "warps.yml");
if (isset($params[0])) {
$name = $params[0];
	if (array_key_exists($params[0], $data)) {
	$x = $data[$params[0]][0];
	$y = $data[$params[0]][1];
	$z = $data[$params[0]][2];
	$status = $data[$params[0]][4];
	$level = $this->api->level->get($data[$params[0]][3]);
	if ($level === false){
	$issuer->sendChat("[SimpleWarp] Warp level is not loaded");
return false;
	}

	
		if ($status == true){
		//Warp is Public
			$issuer->teleport(new Position($x, $y, $z, $level));
			$issuer->sendChat("[SimpleWarp] You have been warped to " . $params[0]);
		}
		else {
			//Warp is private
			if ($this->api->ban->isOP($issuer->username) == TRUE) {
			$issuer->teleport(new Position($x, $y, $z, $level));
			$issuer->sendChat("[SimpleWarp] You have been warped to " . $params[0]);
			$issuer->sendChat("This warp is private!");
				
			}
			else {
				$issuer->sendChat("[SimpleWarp] Warp is private");
			}
		}
	}
	else {
		$issuer->sendChat("[SimpleWarp] Warp doesnt exist!");
	}
}
else {
	$issuer->sendChat("---WARP LIST---");
foreach ($data as $key => $value) {
if ($value[4] == true) {
	$issuer->sendChat("Name: " . $key . " Level Name: " . $value[3]);
	}
}
}
break;
case "openwarp": 
$data = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "warps.yml");
if (isset($params[0])) {
if (array_key_exists($params[0], $data)) {
	$data[$params[0]][] = "true";
	$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."warps.yml", $data);
	$issuer->sendChat("[SimpleWarp] Warp Opened!");
}
else {
	$issuer->sendChat("[SimpleWarp] Warp doesnt exist!");
}
}
else {
	$issuer->sendChat("Usage: /openwarp <NAME>");
}
break;
case "closewarp": 
$data = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "warps.yml");
if (isset($params[0])) {
	if (array_key_exists($params[0], $data)) {
		unset($data["$params[0]"][4]);
		$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."warps.yml", $data);
		$issuer->sendChat("[SimpleWarp] Warp Closed");
	}
	else {
		$issuer->sendChat("[SimpleWarps] Warp doesnt exists!");
	}
}
else {
	$issuer->sendChat("Usage: /closewarp <NAME>");
}
break;
}
}
}