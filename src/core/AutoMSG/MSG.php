
<?php
namespace core\AutoMSG;
use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\{Server,Player};
use core\DataBase;
use pocketmine\network\protocol\LevelSoundEventPacket;
class MSG extends PluginTask{
  public $time = 2*60;
public function __construct(DataBase $base){
  parent::__construct($base);
  $this->base = $base;
}
public function onRun($currentTick){

  $base = $this->base;
  $base->getReloadServer();
  if($this->time >0){$this->time--;}
  if($this->time == 0){
  foreach($base->getServer()->getDefaultLevel()->getPlayers() as $p){
    $p->sendMessage($base->getMessage());
    $this->time = 2*60;
    $pk = new LevelSoundEventPacket();
         $pk->x = $p->x;
         $pk->y = $p->y;
         $pk->z = $p->z;
         $pk->eventId = 'SOUND_NOTE';
         $p->dataPacket($pk);

  }
  }
}
}
