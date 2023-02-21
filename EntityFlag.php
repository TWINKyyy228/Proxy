<?php

require_once(__DIR__.'/../proxyface.php');
require_once('./other/Human.php');

use pocketmine\utils\UUID;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\ {
    TextPacket,
    SetPlayerGameTypePacket,
    SetHealthPacket,
    SetTimePacket,
    RemoveEntityPacket,
    DataPacket,
    SetEntityDataPacket
};

class EntityFlag {
	protected $dataProperties = [];

    public function setDataFlag($propertyId, $id, $value = true, $type = Entity::DATA_TYPE_LONG){
        if($this->getDataFlag($propertyId, $id) !== $value){
            $flags = (int) $this->getDataProperty($propertyId);
            $flags ^= 1 << $id;
            $this->setDataProperty($propertyId, $type, $flags);
        }
    }

    public function getDataFlag($propertyId, $id){
        return (((int) $this->getDataProperty($propertyId)) & (1 << $id)) > 0;
    }

    public function getDataProperty($id){
        return isset($this->dataProperties[$id]) ? $this->dataProperties[$id][1] : null;
    }

    public function setDataProperty($id, $type, $value){
        if($this->getDataProperty($id) !== $value){
            $this->dataProperties[$id] = [$type, $value];

            $pk = new SetEntityDataPacket();
            $pk->entityRuntimeId = $this->eid;
            $pk->metadata = [$id => $this->dataProperties[$id]];
            $this->host->player->SendToClient($pk);
        }
    }

}