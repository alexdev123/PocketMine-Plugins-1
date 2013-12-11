<?php
/*
__PocketMine Plugin__
name=MineReset
description=Powerful mine reseting plugin
version=0.1
author=Falk
class=mineReset
apiversion=10
*/
class mineReset implements Plugin{
private $api, $path;
public function __construct(ServerAPI $api, $server = false){
$this->api = $api;
}

public function init(){

$this->api->console->register("mine", "Mange mines", array($this, "command"));
$this->config = new Config($this->api->plugin->configPath($this)."mines.yml", CONFIG_YAML, array());
}

public function __destruct(){}
public function command($cmd, $params, $issuer, $alias, $args, $issuer){
switch ($params[0]) {
	case "create": 
	if (isset($this->pos1[$issuer->username]) && isset($this->pos2[$issuer->username])) {
	$mines = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "mines.yml");
	if (array_key_exists($params[1], $mines)) {
		$issuer->sendChat('[MineReset] This mine already exists!');
		$issuer->sendChat('Modify with /mine set');
	}
	else {
	$mines[$params[1]] = array($this->pos1[$issuer->username][0],$this->pos2[$issuer->username][0],$this->pos1[$issuer->username][1],$this->pos2[$issuer->username][1],$this->pos1[$issuer->username][2],$this->pos2[$issuer->username][2],"null");
		$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."mines.yml", $mines);
		unset($this->pos1[$issuer->username]);
		unset($this->pos2[$issuer->username]);
		$issuer->sendChat('[MineReset] Mine created!');
		}
		}
		else {
			$issuer->sendChat('Positions not set!');
		}
	break;
	case "reset": 
	$name = $params[1];
	$mines = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "mines.yml");
if (array_key_exists($name, $mines)) {
	if ($mines[$name][6] !== "null") {
		$x1 = $mines[$name][0];
		$x2 = $mines[$name][1];
		$y1 = $mines[$name][2];
		$y2 = $mines[$name][3];
		$z1 = $mines[$name][4];
		$z2 = $mines[$name][5];

		if ($x1 > $x2) {
			$temp = $x1;
			$x1 = $x2;
			$x2 = $temp;
		}
			if ($y1 > $y2) {
			$temp = $y1;
			$y1 = $y2;
			$y2 = $temp;
		}
			if ($z1 > $z2) {
			$temp = $z1;
			$z1 = $z2;
			$z2 = $temp;
		}
		
		$sets = $mines[$name][6];
		$id = array_keys($sets);
		$m = array_values($sets);
		//Temp
			$sum[0] = $m[0];
			for ($l = 1; $l < count($m); $l++) {
			$sum[$l] = $sum[$l-1] + $m[$l];
				
			}
	for ($i = $x1; $i <= $x2; $i++) {
	 for ($j = $y1; $j <= $y2; $j++) {
			for ($k = $z1; $k <= $z2; $k++) {
			$a = rand(0,end($sum));
			for ($l = 0; $l < count($sum); $l++) {
			if ($a < $sum[$l]) {
			$air = BlockAPI::get($id[$l],0);
			$level = $issuer->level;
			$x = $i;
			$y = $j;
			$z = $k;
			$level->setBlock(new Vector3($x, $y, $z, $level), $air);
			$l = count($sum);
				
			}
				
			}
				
			}
			
		}
	}
	$issuer->sendChat("[MineReset] " . $name . " has been reset");	
		

	}
	else {
		$issuer->sendChat("[MineReset] Mine not resetable");
	}
}
else {
	$issuer->sendChat("[MineReset] No mine with that name");
}

break;
case "pos1": 
$this->pos1[$issuer->username][0] = ceil($issuer->entity->x);
$this->pos1[$issuer->username][1] = ceil($issuer->entity->y);
$this->pos1[$issuer->username][2] = ceil($issuer->entity->z);
$issuer->sendChat("[MineReset] Position 1 set");
break;
case "pos2": 
$this->pos2[$issuer->username][0] = ceil($issuer->entity->x);
$this->pos2[$issuer->username][1] = ceil($issuer->entity->y);
$this->pos2[$issuer->username][2] = ceil($issuer->entity->z);
$issuer->sendChat("[MineReset] Position 2 set");
break;
case "set": 
$name = $params[1];
$mines = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "mines.yml");
if (array_key_exists($name, $mines)) {
$sets = array_slice($params, 2);
foreach ($sets as $key => $item) {
	if ( $key & 1 ) {
  $save[$sets[$key-1]] = $item;
} else {
  
}
}


$mines[$name][6] = $save;

$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."mines.yml", $mines);
$issuer->sendChat("[MineReset] Mine setted");
}
else {
	$issuer->sendChat("[MineReset] No mine found");
}
break;
		default:
	$issuer->sendChat("---MineReset---");
	$issuer->sendChat("/mine pos1");
	$issuer->sendChat("/mine pos2");
	$issuer->sendChat("/mine create <NAME>");
	$issuer->sendChat("/mine set <NAME> <ITEM> <PERCENT>...");
	$issuer->sendChat("/mine reset <NAME>");
}
}
}