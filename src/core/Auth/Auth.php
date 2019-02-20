
<?php
namespace core\Auth;
use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\{Server,Player};
use core\DataBase;
class Auth extends PluginTask{
public $time = 20;
public function __construct(DataBase $base){
  parent::__construct($base);
  $this->base = $base;
}
public function onRun($currentTick){
  $base = $this->base;
  if($this->time >0){$this->time--;}
  if($this->time == 0){
    foreach($base->getServer()->getOnlinePlayers() as $p){
$base->getAuthMessage($p);
$this->time = 20;
}
  }
  foreach($base->getServer()->getOnlinePlayers() as $p){
$base->getAuthpopup($p);
  }
}
}


 ?>
