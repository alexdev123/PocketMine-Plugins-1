<?php
/*
__PocketMine Plugin__
name=Tap to do
description=A simple plugin to automate commands
version=0.3
author=Falk,alexdev123
class=tapdo
apiversion=10
*/
/*
_Change Log_
0.1 - Intial release
0.2 - 
	*Added new /tapcmd command 
	*Added :user:
	*Code format improvements
0.3 -
	*Added multiple commands!
	*Removed support for setcmd
	*Chnaged :player: to @player
	
*/
class tapdo implements Plugin{
private $api, $path;
public function __construct(ServerAPI $api, $server = false){
$this->api = $api;
}

public function init(){

$this->api->addHandler("player.move", array($this,"eventHandle"),50);
$this->api->console->register("tapcmd", "Sets the tap cmd for the block you stand on", array($this, "command"));
$this->config = new Config($this->api->plugin->configPath($this)."blocks.yml", CONFIG_YAML, array());
}

public function __destruct(){}
public function command($cmd, $params, $issuer, $alias, $args, $issuer){
switch ($cmd) {
	case "tapcmd": 
	$cmd = implode(" ", $params);
	$this->picked[$issuer->username] = $cmd;
	$issuer->sendChat("Tap a block to add the command!");
break;
		default:
	$issuer->sendChat("Error!");
}
}
public function eventHandle($data, $event) {
if (isset($this->picked[$data["player"]->username])) {
$block = $data["target"];
	$read = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "blocks.yml");
    $x = $block->x;
	$y = $block->y;
	$z = $block->z;
	$level = $block->level->getName();
	$id = $x . "!" . $y . "!" . $z . "!" . $level;
	$read[$id][] = $this->picked[$data["player"]->username];
	
		$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."blocks.yml", $read);
		unset($this->picked[$data["player"]->username]);
		$data["player"]->sendChat("Command added to block!");
		}
else {
$block = $data["target"];
$read = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "blocks.yml");
    $x = $block->x;
	$y = $block->y;
	$z = $block->z;
	$level = $block->level->getName();
	$search = $x . "!" . $y . "!" . $z . "!" . $level;
	if (array_key_exists($search,$read)) {
	foreach ($read[$search] as $command) {
	$command = str_replace("@player", $data["player"]->username, $command);
	 $this->api->console->run($command);
	 }
		}
		}
	}
}
