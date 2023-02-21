<?php

require_once(__DIR__.'/../other/Human.php');
require_once(__DIR__.'/../other/Entity.php');
require_once(__DIR__.'/../utils/Host.php');

use pocketmine\math\Vector3;
use pocketmine\utils\{UUID, TextFormat};
use pocketmine\network\mcpe\protocol\ {
    TextPacket,
    SetPlayerGameTypePacket,
    SetHealthPacket,
    SetTimePacket,
    RemoveEntityPacket,
    DataPacket,
    AddPlayerPacket,
    AddEntityPacket,
    MovePlayerPacket,
    MoveEntityPacket,
    CommandStepPacket
};

class Server {

    private $players = [];
    private $entities = [];
    private $entries = [];

    private $host;

    public function __construct(Host $host) {
        $this->host = $host;
    }

    public function sendChat(string $msg) : void {
        $pk = new TextPacket;
        $pk->type = 1;
        $pk->message = $msg;
        $pk->source = '';
        $this->host->player->SendToServer($pk);
    }

    public function sendCommand(string $command) : void {
        $pk = new CommandStepPacket;
        $cmd = explode(' ', $command);
        $pk->command = $cmd[0];
        $pk->overload = 'default';
        $pk->uvarint1 = 0;
        $pk->currentStep = 0;
        $pk->done = false;
        $pk->clientId = -1;
        if(isset($cmd[1])){
            unset($cmd[0]);
            $pk->inputJson = ['args' => implode(' ', $cmd)];
        }
        $this->host->player->SendToServer($pk);
    }

    public function reset($pk) : void {
        $yaw = $pk->yaw;
        if($yaw >= 360){
            $yaw = $yaw - 360;
        }elseif($yaw < 0){
            $yaw = $yaw + 360;
        }
        $pitch = $pk->pitch > 90 ? $pk->pitch - 360 : $pk->pitch;
        if($pk->entityRuntimeId === $this->host->player->eid){
            $this->host->player = $this->host->player->setComponents(new Vector3($pk->x, $pk->y, $pk->z), $pitch, $yaw, $pk->onGround, $pk->entityRuntimeId);
        }elseif(isset($this->players[$pk->entityRuntimeId])){
            $this->players[$pk->entityRuntimeId] = $this->players[$pk->entityRuntimeId]->setComponents(new Vector3($pk->x, $pk->y, $pk->z), $yaw, $pitch, $pk->onGround);
        }elseif(isset($this->entities[$pk->entityRuntimeId])){
            $this->entities[$pk->entityRuntimeId] = $this->entities[$pk->entityRuntimeId]->setComponents(new Vector3($pk->x, $pk->y, $pk->z), $yaw, $pitch, $pk->onGround);
        }
    }

    public function addPlayer(AddPlayerPacket $pk) : void {
        if(strpos($pk->username, '§') !== false || strpos($pk->username, ' ') !== false || $pk->username === 'null' || !isset($pk->username[0]) || $pk->username === null) return;
        $nametag = isset($this->entries[$pk->entityRuntimeId]) ? $this->entries[$pk->entityRuntimeId] : null;
        $human = new Human($this->host, $nametag, $pk->username, $pk->entityRuntimeId, new Vector3($pk->x, $pk->y + 1, $pk->z), $pk->pitch, $pk->yaw);
        $this->host->logger->add('Added new Player - '.TextFormat::toANSI($pk->username));

        $this->players[$pk->entityRuntimeId] = $human;
    }

    public function remove(int $eid) : void {
        if(isset($this->players[$eid])){
            $this->host->logger->remove('Removed Player - '.TextFormat::toANSI($this->players[$eid]->getName()));
            unset($this->players[$eid]);
            if(isset($this->host->player->friends[$eid])){
                unset($this->host->player->friends[$eid]);
            }
        }elseif(isset($this->entities[$eid])){
            unset($this->entities[$eid]);
        }
    }

    public function addEntity(AddEntityPacket $pk) : void {
        $entity = new Entity($pk->entityRuntimeId, $pk->type, new Vector3($pk->x, $pk->y, $pk->z), $pk->pitch, $pk->yaw);
        $this->entities[$pk->entityRuntimeId] = $entity;
    }

    public function addEntry(int $eid, string $nametag) : void {
        $this->entries[$eid] = $nametag;
    }

    public function clear(){
        $this->players = [];
        $this->entities = [];
        $this->host->logger->info('Player cache cleared');
        $this->host->logger->info('Entity cache cleared');
    }

    public function getPlayers() : array {
        return $this->players;
    }

    public function getEntities() : array {
        return $this->entities;
    }

}
