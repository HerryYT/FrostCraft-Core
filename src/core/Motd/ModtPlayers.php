
<?php
namespace core\Motd;
use pocketmine\event\Listener;
use pocketmine\{Server,Player};
use core\DataBase;
use pocketmine\event\server\QueryRegenerateEvent;
class ModtPlayers implements Listener{
  public function __construct(DataBase $plugin){

  $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
  $this->db = $plugin;
  }

  public function onQueryRegenerate(QueryRegenerateEvent $event){
  $event->setPlayerCount(count($this->db->getServer()->getOnlinePlayers())+10);
  $event->setMaxPlayerCount(8190);
  }





}
