
<?php
namespace core\Rank;
use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\{Server,Player};
use pocketmine\item\Item;
use core\DataBase;
class RankSet extends PluginTask{
public function __construct(DataBase $base){
  parent::__construct($base);
  $this->base = $base;
}
public function onRun($currentTick){
  $this->base->getBossMessage();
foreach($this->base->getServer()->getlevelByName("world")->getPlayers() as $p){
  $p->setFood(20);
  $p->setHealth(20);
  $this->base->LoggerAuth($p);
}


}
}
