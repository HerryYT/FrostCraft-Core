
<?php

namespace core\Boss;

use pocketmine\entity\Entity;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\v120\BossEventPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\network\protocol\SetEntityDataPacket;
use pocketmine\network\protocol\UpdateAttributesPacket;
use pocketmine\{Server,Player};
use pocketmine\level\Level;
use pocketmine\math\Vector3;

class Boss{
		const ENTITY = 57;

	public static function sendBoss(Player $player, int $eid, string $title, $ticks = null){
		self::removeBoss([$player], $eid);//remove same bars
		$packet = new AddEntityPacket();
		$packet->eid = $eid;
		$packet->type = self::ENTITY;
		$packet->x = $player->getX();
		$packet->y = $player->getY();
		$packet->z = $player->getZ();
		$packet->metadata = [Entity::DATA_LEAD_HOLDER => [Entity::DATA_TYPE_LONG, -1], Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, 0 ^ 1 << Entity::DATA_FLAG_SILENT ^ 1 << Entity::DATA_FLAG_INVISIBLE ^ 1 << Entity::DATA_FLAG_NO_AI], Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title]];
		$player->dataPacket($packet);
		$bpk = new BossEventPacket(); // This updates the bar
		$bpk->eid = $eid;
		$bpk->eventType = BossEventPacket::EVENT_TYPE_ADD;
		$bpk->bossName = $title;
		$bpk->healthPercent = 1;
		$bpk->color = 5;
		$bpk->overlay = 0;
		$bpk->playerEid = $player->getId();//TODO TEST!!!
		$player->dataPacket($bpk);
	}
	public static function removeBoss($players, int $eid){
		if (empty($players)) return false;
		$pk = new RemoveEntityPacket();
		$pk->eid = $eid;
		Server::getInstance()->broadcastPacket($players, $pk);
		return true;
	}

	public static function setTitle(string $title, int $eid, $players = []){
		if (!count(Server::getInstance()->getOnlinePlayers()) > 0) return;
		$npk = new SetEntityDataPacket();
		foreach($players as $player) {
			$pl = $player->getPlayer();
			$npk->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title]];
		}
		$npk->eid = $eid;
		Server::getInstance()->broadcastPacket($players, $npk);

		$bpk = new BossEventPacket(); // This updates the bar
		$bpk->eid = $eid;
		$bpk->eventType = BossEventPacket::EVENT_TYPE_ADD;
		$bpk->bossName = $title;
		$bpk->healthPercent = 1;
		$bpk->color = 5;
		$bpk->overlay = 0;
		foreach($players as $player) {
			$pl = $player->getPlayer();
			$bpk->playerEid = $pl->getId();
		}
		Server::getInstance()->broadcastPacket($players, $bpk);
	}
	public static function setVida(int $percentage, int $eid, $players = []){
		if (empty($players)) $players = Server::getInstance()->getOnlinePlayers();
		if (!count($players) > 0) return;

		$upk = new UpdateAttributesPacket(); // Change health of fake wither -> bar progress
		$upk->minValue = 1;
		$upk->maxValue = 600;
		$upk->defaultValue = 1;
		$upk->value = max(1, min([$percentage, 100])) / 100 * 600;
		$upk->name = "minecraft:health";
		$upk->entityId = $eid;
		Server::getInstance()->broadcastPacket($players, $upk);

		$bpk = new BossEventPacket(); // This updates the bar
		$bpk->eid = $eid;
		$bpk->eventType = BossEventPacket::EVENT_TYPE_ADD;
		$bpk->bossName = "";
		$bpk->healthPercent = 1;
		$bpk->color = 5;
		$bpk->overlay = 0;
		foreach($players as $player) {
			$pl = $player->getPlayer();
			$bpk->playerEid = $pl->getId();
		}
		Server::getInstance()->broadcastPacket($players, $bpk);
	}
}
