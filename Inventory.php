<?php

require_once('./utils/Host.php');

use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\level\particle\FlameParticle;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\network\mcpe\protocol\ {
    ContainerSetSlotPacket,
    ContainerClosePacket,
    MobEquipmentPacket
};

class Inventory {

    public $items = [];
    public $offhand; // TODO

    public $selectedSlot, $itemInHand;

    private $host;

    public function __construct(Host $host) {
        $this->host = $host;

        $this->selectedSlot = 0;
        $this->itemInHand = Item::get(0);
        for($i = 0; $i <= 35; $i++){
            $this->items[$i] = Item::get(0);
        }
    }

    public function findSlot() : ?int {
        foreach($this->items as $slot => $item){
            if($item->getId() === 0) return $slot;
        }
        return null;
    }

    public function findHotbarSlot() : ?int {
        foreach($this->items as $slot => $item){
            if($item->getId() === 0 && $slot <= 8) return $slot;
        }
        return null;
    }

    public function findItemSlot(Item $item) : ?int {
        foreach($this->items as $slot => $it){
            if($it->equals($item, true, true, true)) return $slot;
        }
        return null;
    }

    public function getSelectedSlot() : int {
        return $this->selectedSlot;
    }

    public function sendSelectedSlot($slot, $client = true) : void {
        $pk = new MobEquipmentPacket;
        $pk->entityRuntimeId = $this->host->player->eid;
        $pk->item = $this->items[$slot];
        $pk->windowId = 0;
        $pk->inventorySlot = $slot + 9;
        $pk->hotbarSlot = $slot;
        if($client) $this->host->player->SendToClient($pk);
        $this->host->player->SendToServer($pk);
    }

    public function setItemInHand($item) : void {
        $pk = new MobEquipmentPacket;
        $pk->entityRuntimeId = $this->host->player->eid;
        $pk->item = $item;
        $pk->windowId = 0;
        $pk->inventorySlot = $this->selectedSlot + 9;
        $pk->hotbarSlot = $this->selectedSlot;
        $this->host->player->SendToClient($pk);
        $this->host->player->SendToServer($pk);
    }

    public function getItemInHand() : Item {
        return $this->itemInHand;
    }

    public function setItemInOffHand(Item $item) : void {
        $pk = new MobEquipmentPacket;
        $pk->entityRuntimeId = $this->host->player->playerEID;
        $pk->item = $item;
        $pk->windowId = 119;
        $pk->inventorySlot = 0;
        $pk->hotbarSlot = $this->selectedSlot + 9;
        $this->host->player->sendMessage(print_r($pk, true));
        $this->host->player->SendToClient($pk);
        $this->host->player->SendToServer($pk);
    }

    public function setItem(int $slot, Item $item, $windowid = 0, $client = true) : void {
        if($windowid === 0) $this->items[$slot] = $item;
        $pk = new ContainerSetSlotPacket;
        $pk->item = $item;
        $pk->slot = $slot;
        $pk->windowid = $windowid;
        $this->host->player->SendToServer($pk);
        if($client) $this->host->player->SendToClient($pk);
    }

    public function close(int $window) : void {
        $pk = new ContainerClosePacket;
        $pk->windowid = $window;
        $this->host->player->SendToServer($pk);
    }

    public function getItems() : array {
        return $this->items;
    }

}