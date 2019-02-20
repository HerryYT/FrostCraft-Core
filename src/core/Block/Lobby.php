
<?php
namespace core\Block;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\{Server,Player};
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use core\DataBase;
use core\Boss\Boss;
use pocketmine\item\Item;
use pocketmine\event\block\{BlockBreakEvent,BlockPlaceEvent};
use pocketmine\network\protocol\LevelSoundEventPacket;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\server\QueryRegenerateEvent;
class Lobby implements Listener{
  public function __construct(DataBase $plugin){

  $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
  $this->db = $plugin;
  }


  public function caida(EntityDamageEvent $event){
  	$player = $event->getEntity();
    if($event->getEntity()->getLevel()->getFolderName() == "world"){
  if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
  $event->setCancelled(true);
  }
  if ($event->getCause() === EntityDamageEvent::CAUSE_ENTITY_EXPLOSION) {
  $event->setCancelled(true);
  }
  }
  }

  public function onEntityDamangeEvent(EntityDamageEvent $event){
  if($event instanceof EntityDamageByEntityEvent)
    {
      $player = $event->getEntity();
      $damager = $event->getDamager();
      if($player instanceof Player)
      {
        if($damager instanceof Player)
        {
  if($event->getEntity()->getLevel()->getFolderName() == "world"){
    $pk = new LevelSoundEventPacket();
         $pk->x = $damager->x;
         $pk->y = $damager->y;
         $pk->z = $damager->z;
         $pk->eventId = 'SOUND_DENY';
         $damager->dataPacket($pk);
  $event->setCancelled(true);
  }
  }
        }
      }
    }
    public function onBlockPlaceEvent(BlockPlaceEvent $event){
    if($event->getPlayer()->getLevel()->getFolderName() == "world"){
    if($this->db->edit == 'off'){
    $event->setCancelled(true);
    }else{
    if($event->getPlayer()->isOp()){
    $event->setCancelled(false);
    }else{
    $event->setCancelled(true);
    }
    }


    }
    }
    public function Break(BlockBreakEvent $event){
    if($event->getPlayer()->getLevel()->getFolderName() == "world"){
    if($this->db->edit == 'off'){
    $event->setCancelled(true);
    }else{
    if($event->getPlayer()->isOp()){
    $event->setCancelled(false);
    }else{
    $event->setCancelled(true);
    }
    }


    }
    }





}
