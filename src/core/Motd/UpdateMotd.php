
<?php
namespace core\Motd;
use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\{Server,Player};
use core\DataBase;
class UpdateMotd extends PluginTask{
  public $time = 5;
public function __construct(DataBase $base){
  parent::__construct($base);
  $this->base = $base;
}
public function onRun($currentTick){
  if($this->time>0){$this->time--;}
  $prefix = "NextCraft : ";
  $names = array("SkyWars","MobEvolution","Msg3","msg4");
  if($this->time == 0){
    $this->base->getServer()->getNetwork()->setName($prefix.$names[array_rand($names)]);
    $this->time = 5;
  }
}
}
