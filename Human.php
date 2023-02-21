<?php

require_once(__DIR__.'/../proxyface.php');
require_once('./utils/Host.php');
require_once('./other/EntityFlag.php');

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

class Human extends EntityFlag{

    protected $eid;
    protected $username, $nametag;
    protected $host;

    protected $vector3;
    protected $yaw, $pitch;

    protected $onGround = true;

    public function __construct(Host $host, ?string $nametag, string $name, int $eid, Vector3 $vector3, $pitch, $yaw) {
        $this->eid = $eid;
        $this->username = $name;
        $this->vector3 = $vector3;
        $this->yaw = $yaw;
        $this->pitch = $pitch;
        $this->host = $host;
        $this->nametag = $nametag;
    }

    public function setInvisible($value = true) : void {
        $this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, $value);
    }
    
    public function setNameTag(string $nametag) : void {
        $this->setDataProperty(Entity::DATA_NAMETAG, Entity::DATA_TYPE_STRING, $nametag);
        $this->nametag = $nametag;
    }

    public function getEID() : int {
    	return $this->eid;
    }

    public function asVector3() : Vector3 {
    	return $this->vector3;
    }

    public function getName() : string {
    	return $this->username;
    }

    public function getNameTag() : ?string {
        return $this->nametag;
    }

    public function getPitch() : float {
    	return $this->pitch;
    }

    public function getYaw() : float {
    	return $this->yaw;
    }

    public function isOnGround() : bool {
    	return $this->onGround;
    }

    public function getEyeHeight() : float {
        return 1.62;
    }

    public function setComponents(Vector3 $vector3, float $yaw, float $pitch, bool $onGround){
    	$this->vector3 = $vector3;
    	$this->yaw = $yaw;
    	$this->pitch = $pitch;
    	$this->onGround = $onGround;
        return $this;
    }

}
