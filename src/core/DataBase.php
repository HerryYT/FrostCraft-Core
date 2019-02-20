
<?php
namespace core;
use pocketmine\{Server,Player};
use pocketmine\plugin\PluginBase;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use core\Boss\Boss;
use core\Player\AddValues;
use core\AutoMSG\MSG;
use core\Auth\Auth;
use core\Block\Lobby;
use core\Motd\{ModtPlayers,UpdateMotd};
use core\Rank\RankSet;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\network\protocol\LevelSoundEventPacket;
use pocketmine\command\{Command,CommandSender};
use pocketmine\item\Item;
class DataBase extends PluginBase{
  public $ordenBoss = 0;
  public $updateMSGBoss = 20;
  public $reset = 60*60;
public $edit = 'off';
public $id = 9999999;
public $rank = array(
  "Owner" => '§7[§l§4OWNER§r§7]§r§7',
  "Admin" => '§7[§c§lADMIN§r§7]§r§7',
  "OwnerDev" => '§7[§l§4OWNER§r§7][§dDEV§7]§r§7',
  "Mod" => '§7[§l§2MOD§r§7]§r§7',
  "Miniyt" => ' §f» §7[§fMini§4YT§7]§r§7',
  "yt" => '§f» §7[§fYou§4Tuber§7]§r§7',
  "yt+" => '§f» §7[§fYou§4Tuber§b+§7]§r§7',
  "Obsidian" => '§7[§r§d§lObsidiana§r§7]§r§7',
  "Diamond" => '§7[§r§b§lDiamond§r§7]§r§7',
  "Iron" => '§f[§7§lHierro§r§f]§r§7',
  "Helper" => '§7[§l§9HELPER§r§7]§r§7',
);

public function onEnable(){
  //$this->getModifier()->setPlayers(100);
  $this->getServer()->getNetwork()->setName("§aObteniendo Datos§e.....§7");
  @mkdir($this->getDataFolder()."DataBase");
  @mkdir($this->getDataFolder()."Rank");
  $world = $this->getServer()->getDefaultLevel();
  $world->setTime(0);
  $world->stopTime();
  $this->checkBaseData();
  $this->registerEvents();
  $this->registerTask();
}

public function onDisable(){

}
public function registerEvents(){
  new AddValues($this);
  new Lobby($this);
  new ModtPlayers($this);
}
public function registerTask(){
$this->getServer()->getScheduler()->scheduleRepeatingTask(new Auth($this), 20);
$this->getServer()->getScheduler()->scheduleRepeatingTask(new RankSet($this), 10);
$this->getServer()->getScheduler()->scheduleRepeatingTask(new UpdateMotd($this), 10);
$this->getServer()->getScheduler()->scheduleRepeatingTask(new MSG($this), 20);
}
public function checkBaseData(){
  if(!file_exists($this->getDataFolder()."DataBase/Auth.yml")){
    $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
  }
  if(!file_exists($this->getDataFolder()."Rank/Rank.yml")){
    $rank = new Config($this->getDataFolder()."Rank/Rank.yml",Config::YAML);
  }
}
//aauth

public static function EncodePasword($pswd){
  return base64_encode($pswd);
}
public static function DecodePasword($psw){
  return base64_decode($psw);
}

public function getPassword(Player $player){
    $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
return self::DecodePasword($base->get($player->getName()));
}
public function setDatos(Player $player,$password,$correo){
  $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
  $datos = array(
    "password" => self::EncodePasword($password),
    "session" => 1,
    "email" => $correo,
    "ip" => $player->getAddress(),
);
  $base->set($player->getName(),$datos);
  $base->save();
  $this->succes($player);
}

public function getAuthMessage(Player $player){
    $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
    if($base->get($player->getName()) == null){
      $player->sendMessage("§7[§9 Auth §7] §eUsted no esta registrado, use /register {password} {password} {correo}");
    }else{
      $jug = $base->get($player->getName());
      if($jug['ip'] == $player->getAddress() && $jug['session'] == 0){

      }else{
      if($jug["session"] == 0){
      $player->sendMessage("§7[ §9Auth §7] §eUsted ya esta registrado, use /login {password}");
    }
    }
}
}
public function getAuthpopup(Player $player){
  $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
  if($base->get($player->getName()) == null){
    $player->sendTip("§bNo estas registrado");
  $player->sendPopup("§e/register {password} {password} {correo}");
  }else{
    $jug = $base->get($player->getName());
    if($jug['ip'] == $player->getAddress() && $jug['ip'] == $player->getAddress() && $jug['session'] == 0){
$player->sendPopup("§aLogueado por §6IP");
    }
    if($jug["session"] == 0){
      $player->sendTip("§bLa cuenta ya esta registrada");
      $player->sendPopup("§e/login {password}");
    }
  }
  }


public function LoggerAuth(Player $player){
    $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
      $jug = $base->get($player->getName());
      if($jug == null or $jug['session'] == 0){
        $player->setNameTag("§c".$player->getName());
      }else{
        $this->setRankPlayer($player);
      }

}

public function getStatusAuth(Player $player){
  $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
    if($base->get($player->getName()) == null or $jug["session"] == 0){
      $i = 0;
    }else{
      $i = 1;
    }
    return $i;
}

public function DenyAcces(Player $player){
  if($this->getStatusAuth($player) == 0){
    $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
    if($base->get($player->getName()) == null){
      $player->sendMessage("§7[ §9Auth §7] §cNecesitas registrarte para acceder a esta opcion!");
      //  $this->getServer()->getDefaultLevel()->broadcastLevelSoundEvent(new Vector3($player->x,$player->y,$player->z), LevelSoundEventPacket::SOUND_DENY);
      $pk = new LevelSoundEventPacket();
           $pk->x = $player->x;
           $pk->y = $player->y;
           $pk->z = $player->z;
           $pk->eventId = 'SOUND_DENY';
           $player->dataPacket($pk);
    }else{
    $jug = $base->get($player->getName());
    if($jug["session"] == 0){
  $player->sendMessage("§7[ §9Auth §7] §eNecesitas loguearte para acceder a esta opcion!");
  //  $this->getServer()->getDefaultLevel()->broadcastLevelSoundEvent(new Vector3($player->x,$player->y,$player->z), LevelSoundEventPacket::SOUND_DENY);
  $pk = new LevelSoundEventPacket();
       $pk->x = $player->x;
       $pk->y = $player->y;
       $pk->z = $player->z;
       $pk->eventId = 'SOUND_DENY';
       $player->dataPacket($pk);
     }
    }
  }
}
public function LoguearIp(Player $player){
    $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
    $jug = $base->get($player->getName());
    if(!$jug == null){
      if($jug['ip'] == $player->getAddress()){
        $psw = $jug['password'];
        $mail = $jug['email'];
        $ip = $jug['ip'];
        $base->set($player->getName(),["password" => $psw,"session" => 1,"email" => $mail,"ip" => $ip]);
        $base->save();
        $this->succes($player);
        $player->sendMessage("§7[ §9Auth §7] §aTe has logueado por ip");
      }
    }
}
public function onCommand(CommandSender $player, Command $cmd, $label, array $args){
      $fcmd = strtolower($cmd->getName());
    switch($fcmd){
      case 'register':
  $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
  if(isset($args[0])){
    if(isset($args[1])){
      if(isset($args[2])){
if($base->get($player->getName()) == null){
  if($args[0] == $args[1]){
  $this->setDatos($player,$args[1],$args[2]);
}else{
  $player->sendMessage("§7[ §9Auth §7] §cLa contraseña no coincide intente de nuevo");
}
}else{
  $jug = $base->get($player->getName());
  if($jug["session"] == 0){
  $player->sendMessage("§7[ §9Auth §7] §eUsted ya esta registrado use /login {password}");
}else{
    $player->sendMessage("§7[ §9Auth §7] §aYa se encuentra logueado");
}
}
}else{
    $player->sendMessage("§7[ §9Auth §7] §cDebe llenar todos los datos pedidos");
}
    }else{
        $player->sendMessage("§7[ §9Auth §7] §cDebe llenar todos los datos pedidos");
    }
  }else{
    $player->sendMessage("§7[ §9Auth §7] §cDebe llenar todos los datos pedidos");
  }

      return true;
      case "login":
if(isset($args[0])){
  $base = new Config($this->getDataFolder()."DataBase/Auth.yml",Config::YAML);
  if($base->get($player->getName()) == null){
    $this->DenyAcces($player);
  }else{
    $jug = $base->get($player->getName());
    if($jug["session"] == 0 or $jug["session"] == 0){
      $password = self::DecodePasword($jug["password"]);
      if($args[0] == $password){
        $psw = $jug['password'];
        $mail = $jug['email'];
        $base->set($player->getName(),["password" => $psw,"session" => 1,"email" => $mail,"ip" => $player->getAddress()]);
        $base->save();
        $this->succes($player);

      }

    }else{
        $player->sendMessage("§7[ §9Auth §7] §aYa se encuentra logueado");
    }
  }

}else{
    $player->sendMessage("§7[ §9Auth §7] §cDebe llenar todos los datos pedidos");
}
      return true;
      case 'setrank':
if(isset($args[0])){
  if(isset($args[1])){
$this->setRank($player,$args[0],$args[1]);
  }
}
      return true;
      case "edit":
if($player->isOp()){
  if($this->edit == 'off'){
    $player->sendMessage("§7[ §9WorldEdit §7] §aSe ha activado el modo edicion");
    $this->edit = 'on';
  }else{
    $player->sendMessage("§7[ §9WorldEdit §7] §aSe ha desactivado el modo edicion");
    $this->edit = 'off';
  }
}
      return true;
    }
  }

public function succes(Player $player){
    $player->sendMessage("§7[ §9Auth §7] §aTe has logueado correctamente!");
$player->setTitle("§3NextCraft","§aBedrock Edition",4);
  Boss::sendBoss($player,999,"§l§3FrostCraft §bGames");
  Boss::setVida(100,999);
  $pk = new LevelSoundEventPacket();
       $pk->x = $player->x;
       $pk->y = $player->y;
       $pk->z = $player->z;
       $pk->eventId = 'SOUND_BULLET_HIT';
       $player->dataPacket($pk);
}

//RANK ACTIONS
public function getRank(Player $player){
    $base = new Config($this->getDataFolder()."Rank/Rank.yml",Config::YAML);
    $name = $player->getName();
    $r = $base->get($name);
    if($r == null){
      $i = 'user';
    }else{
      $i = $r;
    }
return $i;
}

public function setRankPlayer(Player $p){
  $name = $p->getName();
  if($this->getRank($p) == 'user'){
    $p->setNameTag("§7".$name);
  }
  if($this->getRank($p) == 'owner'){
    $p->setNameTag($this->rank['Owner'].$name);

  }
  if($this->getRank($p) == 'dev'){
    $p->setNameTag($this->rank['OwnerDev'].$name);

  }
  if($this->getRank($p) == 'admin'){
    $p->setNameTag($this->rank['Admin'].$name);

  }
  if($this->getRank($p) == 'helper'){
    $p->setNameTag($this->rank['Helper'].$name);

  }
  if($this->getRank($p) == 'mod'){
    $p->setNameTag($this->rank['Mod'].$name);

  }
  if($this->getRank($p) == 'miniyt'){
    $p->setNameTag($this->rank['Miniyt'].$name);

  }
  if($this->getRank($p) == 'yt'){
    $p->setNameTag($this->rank['yt'].$name);

  }
  if($this->getRank($p) == 'ytmas'){
    $p->setNameTag($this->rank['yt+'].$name);

  }
  if($this->getRank($p) == 'obsidiana'){
    $p->setNameTag($this->rank['Obsidian'].$name);

  }
  if($this->getRank($p) == 'diamond'){
    $p->setNameTag($this->rank['Diamond'].$name);

  }
  if($this->getRank($p) == 'iron'){
    $p->setNameTag($this->rank['Iron'].$name);

  }
}

public function setRank(Player $player,$rank,$name){
  $base = new Config($this->getDataFolder()."Rank/Rank.yml",Config::YAML);
$types = ['owner','dev','mod','admin','helper','miniyt','yt','ytmas','obsidiana','diamond','iron'];
if($player->isOp()){
if($rank == $types[0] or $rank == $types[1] or $rank == $types[2] or $rank == $types[3] or $rank == $types[4] or $rank == $types[5] or $rank == $types[6] or
$rank == $types[7] or $rank == $types[8] or $rank == $types[9] or $rank == $types[10]){
$base->set($name,$rank);
$base->save();
$player->sendMessage("§7[ §9Rank §7] §7 Se ha dado rango : ".$rank." al Jugador : ".$name);
}else{
  $player->sendMessage("§7[ §9Rank §7] §cEste rango no existe");
}
}else{
  $player->sendMessage("§7[ §9Rank §7] §ePuedes obtener Rango comprandolo en nuestra app oficial");
}

}


public function setDefaultSettings(Player $player){
  $player->setGamemode(2);
  $player->getInventory()->clearAll();
  $player->getInventory()->setItem(0,Item::get(345,0,1));
  $player->getInventory()->setItem(1,Item::get(397,3,1));
  $player->getInventory()->setItem(4,Item::get(54,0,1));
  $player->getInventory()->setItem(7,Item::get(351,10,1));
  $player->getInventory()->setItem(8,Item::get(399,0,1));

}


public function getMessage(){
$msg = array(
"Are YouTuber with more than 100 subscribers? Do a review to the server and you will get MiniYT rank",
"Are YouTuber with more than 1000 subscribers? Do a review to the server and you will get YouTuber rank",
"Are YouTuber with more than 3.500 subscribers? Do a review to the server and you will get YouTuber+ rank",
"Use /report to inform [Hackers / Spammers / Teammers] and we will help you immediately",
"Follow us on Twitter: @NextCraftP to be informed of each time there is a new update",
"You can find our Builder Team on Twitter as @NextCraftBT",
"You can join our Discord through this link: https://discord.gg/yu8HSy7"
);
$key = $msg[array_rand($msg)];
$p = "§6»§a ";
return $p.$key;
}

public function getReloadServer(){
  if($this->reset >0){$this->reset--;}
  if($this->reset == 10 || $this->reset == 5 || $this->reset == 4 || $this->reset == 3 || $this->reset == 2 || $this->reset == 1){
    foreach($this->getServer()->getOnlinePlayers() as $p){
      $p->sendMessage("§7El servidor se reiniciara en §a: §6".$this->reset);
    }
  }
  if($this->reset == 1){
    foreach($this->getServer()->getOnlinePlayers() as $p){
$p->kick("§aEl servidor ha sido reiniciado");
    }
  }
  if($this->reset == 0){
  $this->getServer()->reload();
}
}

public function getBossMessage(){
$msg1 = "§bYou are playing on §l§6NextCraft§r";
$msg2 = array(
"§aPets§7, §bRanks §7+ §dMore §l§7> §l§6http://STORE.NEXTCRAFTPE.NET §r",
"§aPets§7, §bRanks §7+ §dMore §l§7> §l§ehttp://STORE.NEXTCRAFTPE.NET §r",
"§aPets§7, §bRanks §7+ §dMore §l§7> §l§fhttp://STORE.NEXTCRAFTPE.NET §r",
"§aPets§7, §bRanks §7+ §dMore §l§7> §l§6http://STORE.NEXTCRAFTPE.NET §r",
);
$msg3 = array(
"§l§6http://PLAY.NEXTCRAFTPE.NET §r",
"§l§ehttp://PLAY.NEXTCRAFTPE.NET §r",
"§l§fhttp://PLAY.NEXTCRAFTPE.NET §r",
"§l§6http://PLAY.NEXTCRAFTPE.NET §r"
);
$msg4 = array(
  "§a§lFollow us on §fTwitter @NextCraftP§r",
  "§a§lFollow us on §bTwitter @NextCraftP§r",
  "§a§lFollow us on §3Twitter @NextCraftP§r",
  "§a§lFollow us on §fTwitter @NextCraftP§r",
  "§a§lFollow us on Twitter @NextCraftP§r",
);
if($this->updateMSGBoss>0){$this->updateMSGBoss--;}
if($this->updateMSGBoss == 0){
$this->ordenBoss++;
if($this->ordenBoss == 4){$this->ordenBoss = 0;}
$this->updateMSGBoss = 20;
}
$
if($this->ordenBoss == 0){
  foreach($this->getServer()->getDefaultLevel()->getPlayers() as $player){
  Boss::setTitle($msg1,999,[$player]);
}
}

if($this->ordenBoss == 1){
  foreach($this->getServer()->getDefaultLevel()->getPlayers() as $player){
  Boss::setTitle($msg2[array_rand($msg2)],999,[$player]);
}
}

if($this->ordenBoss == 2){
  foreach($this->getServer()->getDefaultLevel()->getPlayers() as $player){
  Boss::setTitle($msg3[array_rand($msg3)],999,[$player]);
}
}
if($this->ordenBoss == 3){
  foreach($this->getServer()->getDefaultLevel()->getPlayers() as $player){
  Boss::setTitle($msg4[array_rand($msg4)],999,[$player]);
}
}
if($this->ordenBoss == 4){
  foreach($this->getServer()->getDefaultLevel()->getPlayers() as $player){
  Boss::setTitle($msg4[array_rand($msg4)],999,[$player]);
}
}

}


}


 ?>
