
<?php
namespace core\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerJoinEvent,PlayerQuitEvent,PlayerChatEvent,PlayerMoveEvent};
use pocketmine\utils\Config;
use pocketmine\{Server,Player};
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use core\DataBase;
use core\Boss\Boss;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerDropItemEvent;
class AddValues implements Listener{
  public function __construct(DataBase $plugin){

  $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
  $this->db = $plugin;
  }

public function sendPlayerSettings(PlayerJoinEvent $event){
$player = $event->getPlayer();
$player->teleport($this->db->getServer()->getDefaultLevel()->getSafeSpawn());
$player->getInventory()->clearAll();
$base = new Config($this->db->getDataFolder()."DataBase/Auth.yml",Config::YAML);
if(!$base->get($player->getName()) == null){
    $jug = $base->get($player->getName());
    $psw = $jug['password'];
    $mail = $jug['email'];
    $ip = $jug['ip'];
    $base->set($player->getName(),["password" => $psw,"session" => 0,"email" => $mail,"ip" => $ip,]);
    $base->save();
}
$this->db->setDefaultSettings($player);
$this->db->LoguearIp($player);
}
public function DeAutj(PlayerQuitEvent $event){
  $player = $event->getPlayer();
  $base = new Config($this->db->getDataFolder()."DataBase/Auth.yml",Config::YAML);
  if(!$base->get($player->getName()) == null){
      $jug = $base->get($player->getName());
      $psw = $jug['password'];
      $mail = $jug['email'];
      $ip = $jug['ip'];
      $base->set($player->getName(),["password" => $psw,"session" => 0,"email" => $mail,"ip" => $ip]);
      $base->save();
  }

}

public function onChat(PlayerChatEvent $event){
$player = $event->getPlayer();
$msg = $event->getMessage();
$base = new Config($this->db->getDataFolder()."DataBase/Auth.yml",Config::YAML);
$jug = $base->get($player->getName());
if($jug == null or $jug['session'] == 0){
  $event->setCancelled(true);
  $player->sendMessage("§7[ §9Auth §7] §7Necesitas estar registrado o loguearte para poder chatear con los demas!");
}else{
  $b = $this->db;
  $str = "§7» ";
  $rank = new Config($this->db->getDataFolder()."Rank/Rank.yml",Config::YAML);
  $name = $player->getName();
$r = $rank->get($player->getName());
if($r == 'owner'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['Owner']."§c".$name.$str."§7".$msg);
  }

}
if($r == 'dev'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['OwnerDev']."§b".$name.$str."§7".$msg);
  }
}
if($r == 'admin'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['Admin']."§c".$name.$str."§7".$msg);
  }

}
if($r == 'mod'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['Mod']."§b".$name.$str."§7".$msg);
  }

}
if($r == 'helper'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['Helper']."§b".$name.$str."§7".$msg);
  }

}
if($r == 'miniyt'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['Miniyt']."§c".$name.$str."§f".$msg);
  }

}
if($r == 'yt'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['yt']."§c".$name.$str."§f".$msg);
  }

}
if($r == 'ytmas'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['yt+']."§c".$name.$str."§f".$msg);
  }

}
if($r == 'obsidiana'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['Obsidian']."§d".$name.$str."§7".$msg);
  }

}
if($r == 'diamond'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['Diamond']."§b".$name.$str."§a".$msg);
  }

}
if($r == 'iron'){
  $event->setCancelled(true);
  foreach($b->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage($b->rank['Iron']."§b".$name.$str."§7".$msg);
  }

}
}
}


public function enDrop(PlayerDropItemEvent $event) {
         $player = $event->getPlayer();
         if($event->getPlayer()->getLevel()->getFolderName() == "world"){
         $event->setCancelled(true);
         }
       }

public function onMinVoid(PlayerMoveEvent $event){
  $player = $event->getPlayer();
  $min = 4;
  $max = $player->y;
    if($event->getPlayer()->getLevel()->getFolderName() == "world"){
  if($max <= $min){
    $player->teleport($this->db->getServer()->getDefaultLevel()->getSpawnLocation());
  }
}
}




}

 ?>
