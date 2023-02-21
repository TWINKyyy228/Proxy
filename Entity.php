<?php

use pocketmine\math\Vector3;
use pocketmine\entity\Arrow;
use pocketmine\network\mcpe\protocol\ {
    TextPacket,
    SetPlayerGameTypePacket,
    SetHealthPacket,
    SetTimePacket,
    RemoveEntityPacket,
    DataPacket
};

class Entity {

    private $eid, $type;

    private $vector3;
    private $yaw, $pitch;

    private $onGround = true;

    public function __construct(int $eid, int $type, Vector3 $vector3, $pitch, $yaw) {
        $this->eid = $eid;
        $this->type = $type;
        $this->vector3 = $vector3;
        $this->yaw = $yaw;
        $this->pitch = $pitch;
    }

    public function isArrow() : bool {
        return $this->type === Arrow::NETWORK_ID;
    }

    public function getEID() : int {
    	return $this->eid;
    }

    public function asVector3() : Vector3 {
    	return $this->vector3;
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

    public function setComponents(Vector3 $vector3, float $yaw, float $pitch, bool $onGround){
    	$this->vector3 = $vector3;
    	$this->yaw = $yaw;
    	$this->pitch = $pitch;
    	$this->onGround = $onGround;
        return $this;
    }

}
