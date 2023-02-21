<?php

require_once(__DIR__.'/Inventory.php');
require_once(__DIR__.'/Human.php');
require_once('./utils/Host.php');
require_once('./utils/Utils.php');

use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\level\particle\{FlameParticle, BlockForceFieldParticle};
use pocketmine\item\enchantment\Enchantment;
use pocketmine\network\mcpe\protocol\ {
    TextPacket,
    SetPlayerGameTypePacket,
    SetHealthPacket,
    SetTimePacket,
    RemoveEntityPacket,
    DataPacket,
    InteractPacket,
    ContainerSetSlotPacket,
    AnimatePacket,
    MovePlayerPacket,
    SetEntityMotionPacket,
    CommandStepPacket,
    MobEquipmentPacket,
    LevelSoundEventPacket,
    UseItemPacket,
    AdventureSettingsPacket,
    ResourcePackClientResponsePacket,
    PlayerActionPacket,
    EntityEventPacket,
    SetEntityDataPacket
};

class Player {

    private $host;
    public $strela = [];

    public $bossEID = 0, $eid, $name, $window;

    public $yaw, $bodyYaw, $pitch;
    public $vector3;

    public $onGround = true;

    public $kl = false;
    public $tracers = false;
    public $loot = false;
    public $kb = false;
    public $lj = false;
    public $scaf = false;
    public $fly = false;
    public $hb = false;
    public $blink = false;
    public $blinked = false;
    public $dupe = false;
    public $nm;
    public $food_start = false;
    public $ap = false;
    public $nm_ = false;
    public $fe = false;
    public $crash = false;
    public $criticals = false;
    public $ignoreef = false;
    public $fe2 = false;
    public $hitboost = false;
    public $as = false;
    public $taptp = false;
    public $lev = false;
    public $jump = false;
    public $invis = true;

    public $reach = false;
    public $reach_d = 5;

    public $trigger = false;
    public $trigger_d = 5;

    public $ts = false;
    public $tsdist = 2;
    public $ts_target = null;
    public $ts_attack = false;
    public $ts_circle = false;

    public $nv = false;
    public $fastbreak = false;
    public $spammer = false;
    public $spammsg = '';
    public $is_spamming = false;

    public $spinner = false;
    public $add = false;
    public $rem = false;

    public $mode = 0;
    public $y2 = 0;

    public $is_consumed = false;

    public $friends = [];

    public $hbx = 0.8;
    public $hby = 1.8;

    public $tickTime = ['aura' => 0, 'tracers' => 0];

    public function __construct(Host $host) {
        $this->host = $host;
        $this->inventory = new Inventory($host);
    }

    public function tick() : void {
        $host = $this->host;
        if($this->ts){
            $target = $this->ts_target;

            // GET TARGET
            if(is_null($target) || !isset($host->server->getPlayers()[$target->getEID()]) || $target->asVector3()->distance($this->vector3) >= $this->tsdist || isset($this->friends[$target->getEID()])){
                $target = null;
                $this->ts_target = null;
                foreach($host->server->getPlayers() as $eid => $human){
                    if($human->asVector3()->distance($this->vector3) <= $this->tsdist){
                        $target = $human;
                        $this->ts_target = $human;
                        break;
                    }
                }
            }

            if($target !== null) {
                // ATTACK TARGET
                if(!$this->ts_attack){
                    $this->ts_attack = true;
                    $host->proxy->lib->RunDelayed(40, function () use (&$host, &$target) {
                        $host->player->attack($target->getEID());
                        // $host->player->sendMessage('§l§cTS §r§7⇒ §fЦЕЛЬ §a'.$target->getName(), 3);
                        $host->player->ts_attack = false;
                    });
                }
                // DRAW CIRCLE AROUND TARGET
                if(!$this->ts_circle){
                    $this->ts_circle = true;
                    $host->proxy->lib->RunDelayed(400, function () use (&$host, &$target) {
                        $vec = clone $target->asVector3();
                        $host->level->addCircle($vec->subtract(0, 1.1, 0), $host->player->tsdist);
                        // $host->level->moveCircle($vec->add(0, 2, 0), $host->player->tsdist);
                        $host->level->addParticle(new BlockForceFieldParticle($vec, Block::get(1, 0)));
                        $host->level->addParticle(new BlockForceFieldParticle($vec->subtract(0, 1, 0), Block::get(1, 0)));
                        $host->player->ts_circle = false;
                    });
                }
            }
        }

        // SPAMMING SMS
        if($this->spammer && !$this->is_spamming){
            $this->is_spamming = true;
            $host->proxy->lib->RunDelayed(7000, function () use (&$host) {
                if($this->spammer){
                    $host->server->sendChat('!'.Utils::randomString() . ' ' . $this->spammsg . ' ' . Utils::randomString());
                    $this->is_spamming = false;
                }
            });
        }
        // KILLAURA TICKER / ATTACK
        if(--$this->tickTime['aura'] <= 0){
            $this->tickTime['aura'] = 5;
            if($this->kl){
                foreach($host->server->getPlayers() as $eid => $player){
                    if($player->asVector3()->distance($this->vector3) <= 8 && !isset($this->friends[$player->getEID()])){
                        // $this->aimAt($player->asVector3());
                        $this->attack($eid);
                        $this->sendMessage('§l§cKL §r§7⇒ §fЦЕЛЬ §a'.$player->getName(), 3);
                    }
                }
            }

            if($this->trigger){
                foreach($host->server->getPlayers() as $eid => $player){
                    if($host->player->isLookingAt($player->asVector3()) && $player->asVector3()->distance($this->vector3) <= $this->trigger_d && !isset($this->friends[$eid])){
                        $this->attack($eid, true);
                        break;
                    }
                }
            }

            if($this->criticals){
                $pk = new AnimatePacket();
                $pk->entityRuntimeId = $this->eid;
                $pk->action = 4;
                $this->SendToServer($pk);
            }
        }

        if(($this->tickTime['tracers'] <= time() || $this->tickTime['tracers'] === 0)){
            $this->tickTime['tracers'] = time() + 0.5;

            if($this->crash){
                $pk = new EntityEventPacket();
                $pk->entityRuntimeId = $this->eid;
                $pk->x = $this->vector3->x;
                $pk->y = $this->vector3->y;
                $pk->z = $this->vector3->z;
                $pk->event = 57;
                $pk->data = PHP_INT_MAX;
                $this->SendToServer($pk);
            }

            if($this->tracers){
                foreach($host->server->getPlayers() as $eid => $player){
                    $host->level->addLine(clone $this->vector3, clone $player->asVector3());
                }
            }
        }
    }

    public function move(Vector3 $vector, float $pitch, float $yaw, $client = true) : void {
        $pk = new MovePlayerPacket;
        $pk->x = $vector->x;
        $pk->y = $vector->y;
        $pk->z = $vector->z;
        $pk->entityRuntimeId = $this->eid;
        $pk->bodyYaw = $yaw;
        $pk->yaw = $yaw;
        $pk->pitch = $pitch;
        $this->SendToServer($pk);
        if($client){
            $pk->mode = 2;
            $this->SendToClient($pk);
        }
    }

    public function setCrosshair($pitch, $yaw) : void {
        $pk = new MovePlayerPacket;
        $pk->x = $this->vector3->x;
        $pk->y = $this->vector3->y;
        $pk->z = $this->vector3->z;
        $pk->entityRuntimeId = $this->eid;
        $pk->bodyYaw = $yaw;
        $pk->yaw = $yaw;
        $pk->pitch = $pitch;
        $this->SendToServer($pk);
        $this->SendToClient($pk);
    }

    public function setMotion(Vector3 $vector) : void {
        $pk = new SetEntityMotionPacket;
        $pk->entityRuntimeId = $this->eid;
        $pk->motionX = $vector->x;
        $pk->motionY = $vector->y;
        $pk->motionZ = $vector->z;
        $this->SendToClient($pk);
    }

    public function attack(int $eid, $animate = false) : void {
        $pk = new InteractPacket;
        $pk->action = 2;
        $pk->target = $eid;
        $this->SendToServer($pk);

        if($animate){
            $pk = new AnimatePacket;
            $pk->action = 1;
            $pk->entityRuntimeId = $this->eid;
            $this->SendToServer($pk);
            $this->SendToClient($pk);
        }
    }

    public function sendMessage(string $msg, int $type = TextPacket::TYPE_RAW) : void {
        $pk = new TextPacket;
        $pk->type = $type;
        $pk->message = $msg;
        $pk->source = '';
        $this->SendToClient($pk);
    }

    public function setGamemode(int $mode = 0) : void {
        $pk = new SetPlayerGameTypePacket;
        $pk->gamemode = $mode;
        $this->SendToClient($pk);
    }

    public function setHealth(int $health) : void {
        $pk = new SetHealthPacket;
        $pk->health = $health;
        $this->SendToClient($pk);
    }

    public function sendSettings($allowFlight) : void {
        $pk = new AdventureSettingsPacket();
        $pk->allowFlight = $allowFlight;
        $pk->userPermission = 0;
        $this->SendToClient($pk);
    }

    public function removeEntity(int $entityEID) : void {
        $pk = new RemoveEntityPacket;
        $pk->entityUniqueId = $entityEID;
        $this->SendToClient($pk);
    }

    public function isLookingAt(Vector3 $vector, $client = false) {
        $l = $vector->subtract($this->vector3);

        $a = 0.5 + $vector->x;
        $b = $vector->y;
        $c = 0.5 + $vector->z;
        $len = sqrt($l->x * $l->x + $l->y * $l->y + $l->z * $l->z);
        if($len == 0) return false;
        $y = $l->y / $len;

        $pitch = asin($y);
        $pitch = $pitch * 180.0 / M_PI;
        $pitch = round(-$pitch);

        $yaw = round(-atan2($a - ($this->vector3->x + 0.5), $c - ($this->vector3->z + 0.5)) * (180 / M_PI));

        if($yaw < 0) $yaw = 360 + $yaw;

        $yaw2 = $this->yaw;
        $pitch2 = $this->pitch;

        return ($yaw2 >= $yaw - 10 && $yaw2 <= $yaw + 10 && $pitch2 >= $pitch - 35 && $pitch2 <= $pitch + 25);
    }

    public function aimAt(Vector3 $vector, $client = false) {
        $l = $vector->subtract($this->vector3);

        $a = 0.5 + $vector->x;
        $b = $vector->y;
        $c = 0.5 + $vector->z;
        $len = sqrt($l->x * $l->x + $l->y * $l->y + $l->z * $l->z);
        if($len == 0) return;
        $y = $l->y / $len;

        $pitch = asin($y);
        $pitch = $pitch * 180.0 / M_PI;
        $pitch = round(-$pitch);

        $yaw = round(-atan2($a - ($this->vector3->x + 0.5), $c - ($this->vector3->z + 0.5)) * (180 / M_PI));

        if($yaw < 0) $yaw = 360 + $yaw;

        $pk = new MovePlayerPacket;
        $pk->x = $this->vector3->x;
        $pk->y = $this->vector3->y;
        $pk->z = $this->vector3->z;
        $pk->entityRuntimeId = $this->eid;
        $pk->bodyYaw = $yaw;
        $pk->yaw = $yaw;
        $pk->pitch = $pitch;
        $this->SendToServer($pk);
        if($client) $this->SendToClient($pk);
    }

    public function setHitbox($player, $hbx = 0.6, $hby = 1.8) : void {
        $pk = new SetEntityDataPacket();
        $pk->entityRuntimeId = $player->getEID();
        $pk->metadata = [Entity::DATA_BOUNDING_BOX_HEIGHT => [Entity::DATA_TYPE_FLOAT, $hby], Entity::DATA_BOUNDING_BOX_WIDTH => [Entity::DATA_TYPE_FLOAT, $hbx]];
        $this->SendToClient($pk);
    }

    public function enchant(Item $item) : Item{
        // if($item->getId() !== 218) $item->setCustomName('§r§crс-рe.ru 19132'.PHP_EOL.'§r§avk.com/sanekmelkov');
        // $used = [9, 13, 15, 16, ]
        // foreach(Enchantment::$enchantments as $enchant2) {
            // if(!is_null($enchant2)){
                // $enchant2->setLevel(6);
                // $item->addEnchantment($enchant2);
            // }
        // }
        $item->setCount(1);
        return $item;
    }

    public function getDirectionVector() : Vector3 {
        $xz = cos(deg2rad($this->pitch));

        return (new Vector3(-$xz * sin(deg2rad($this->yaw)), -sin(deg2rad($this->pitch)), $xz * cos(deg2rad($this->yaw))))->normalize();
    }

    public function getName() : string {
        return $this->name;
    }

    public function getInventory() : Inventory {
        return $this->inventory;
    }

    public function getEyeHeight() : float {
        return 1.62;
    }

    public function setComponents(Vector3 $vector3, float $pitch, float $yaw, bool $onGround, int $eid) {
        $this->vector3 = $vector3;
        $this->pitch = $pitch;
        $this->yaw = $yaw;
        $this->bodyYaw  = $yaw;
        $this->onGround = $onGround;
        $this->eid = $eid;
        return $this;
    }

    public function SendToClient(DataPacket $packet) : void {
        $packet->encode();
        $this->host->proxy->lib->SendToClient($packet->buffer, strlen($packet->buffer));
    }

    public function SendToServer($packet) : void {
        $packet->encode();
        $this->host->proxy->lib->SendToServer($packet->buffer, strlen($packet->buffer));
    }

}
