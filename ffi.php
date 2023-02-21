<?php

require_once('./utils/Host.php');

use pocketmine\utils\{TextFormat, Terminal, UUID};
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\utils\Config;
use pocketmine\entity\{Entity, Attribute};
use pocketmine\level\particle\FlameParticle;
use pocketmine\nbt\tag\{CompoundTag, NamedTag, ListTag};
use pocketmine\nbt\NBT;
use pocketmine\inventory\InventoryType;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\entity\Effect;
use pocketmine\tile\Tile;
use pocketmine\network\mcpe\protocol\ {LoginPacket, ProtocolInfo, PacketPool, TextPacket, BossEventPacket, StartGamePacket, PlayerActionPacket,  MovePlayerPacket, PlayerListPacket, SetEntityDataPacket, UpdateAttributesPacket, SetHealthPacket, UpdateBlockPacket, InteractPacket, MoveEntityPacket, AddPlayerPacket, RemoveEntityPacket, SetEntityMotionPacket, ContainerSetSlotPacket, ContainerSetContentPacket, AddItemPacket, AddItemEntityPacket, ContainerOpenPacket, CommandStepPacket, MobEquipmentPacket, BatchPacket, UseItemPacket, AdventureSettingsPacket, AddEntityPacket, FullChunkDataPacket, ResourcePacksInfoPacket, ResourcePackStackPacket, RemoveBlockPacket, BlockEntityDataPacket, AnimatePacket, MobEffectPacket, EntityEventPacket};
// 45.93.200.202
$host = new Host(new Address('play.breadixpe.ru', 19132), 'milkav1337', Utils::ANDROID, 'XIAOMI Redmi 5', base64_encode(file_get_contents('skin.png'))); // '65.108.142.183'

$host->proxy->subscribeOnClientPayloadSendEvent(function($payload, $len) use(&$host, &$functions){
    switch(ord($payload[0])){
        case ProtocolInfo::CONTAINER_SET_SLOT_PACKET:
            $pk = new ContainerSetSlotPacket($payload);
            $pk->decode();
            if($pk->windowid === 0 && $pk->slot !== null){
                $host->player->getInventory()->items[$pk->slot] = $pk->item;
            }
        break;
        case ProtocolInfo::TEXT_PACKET:
            $pk = new TextPacket($payload);
            $pk->decode();
            $message = explode(' ', $pk->message);
            switch($message[0]){
                case '.tr':
                	if(!isset($message[1])) return;
                    $host->proxy->lib->LiveTransfer($message[1]);
                return false;
                case '.kl':
                    $host->player->kl ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fКИЛЛАУРА §cвыключена') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fКИЛЛАУРА §aвключена');
                    $host->player->kl = !$host->player->kl;
                return false;
                case '.trace':
                	$host->player->tracers ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТРЕЙСЕРЫ §cвыключены') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТРЕЙСЕРЫ §aвключены');
                	$host->player->tracers = !$host->player->tracers;
                return false;
                case '.dupe':
                	$host->player->dupe ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fДЮП §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fДЮП §aвключен');
                	$host->player->dupe = !$host->player->dupe;
                return false;
                case '.loot':
                	$host->player->loot ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fАВТОЛУТ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fАВТОЛУТ §aвключен');
                	$host->player->loot = !$host->player->loot;
                return false;
                case '.hb':
                	if(!isset($message[1]) or !isset($message[2])){
                		$host->player->hb = !$host->player->hb;
                		$host->proxy->lib->SetHitbox($host->player->hb, 0.8, 1.8);
                		$host->logger->message('§l§cПРОКСИ §r§7⇒ §fХИТБОКС §cвыключен');
                		return;
                	}
                	$host->player->hb ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fХИТБОКСЫ §cвыключены') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fХИТБОКСЫ §aустановлены §fна §7'.$message[1].'x '.$message[2].'y');
                	$host->player->hb = !$host->player->hb;
                    $host->proxy->lib->SetHitbox($host->player->hb, (float) $message[1], (float) $message[2]);
                return false;
                case '.lj':
                	$host->player->lj ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fЛОНГДЖАМП §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fЛОНГДЖАМП §aвключен');
                	$host->player->lj = !$host->player->lj;
                return false;
                case '.kb':
                	$host->player->kb ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fКБ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fКБ §aвключен');
                	$host->player->kb = !$host->player->kb;
                return false;
                case '.spinner':
                	$host->player->spinner ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fСПИННЕР §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fСПИННЕР §aвключен');
                	$host->player->spinner = !$host->player->spinner;
                return false;
                case '.fly':
                	$host->player->fly ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fФЛАЙ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fФЛАЙ §aвключен');
                    $host->player->fly = !$host->player->fly;
                    $host->player->sendSettings($host->player->fly);
                return false;
                case '.blink':
                	$host->player->blink ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fБЛИНК §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fБЛИНК §aвключен');
                    if($host->player->blink){
                        $host->player->blinked = false;
                        $host->player->removeEntity(9991);
                    }
                    !$host->player->blink = !$host->player->blink;
                return false;
                case '.sp':
                    $host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fСКОРОСТЬ §aустановлена на §7'.$message[1]);
                    $host->proxy->lib->SetMovementSpeed((float) $message[1]);
                return false;
                case '.nm':
                    unset($message[0]);
                    $host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fНЕЙМТЕГ §aустановлен на §7'. implode(' ', $message));
                    $host->player->nm = implode(' ', $message);
                    $host->player->nm_ = true;
                return false;
                case '.add':
                	$host->player->add = true;
                	$host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fНАЖМИ на игрока');
                return false;
                case '.list':
                    $host->logger->message('§l§cПРОКСИ §r§7⇒ §fСПИСОК ваших друзей');
                	foreach($host->player->friends as $eid => $value){
                		$host->logger->message('§a'.$host->server->getPlayers()[$eid]->getName().'§7(id'.$eid.')'.'§f ваш друг');
                	}
                return false;
                case '.rem':
                	$host->player->rem = true;
                	$host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fНАЖМИ на игрока');
                return false;
                case '.clear':
                	$host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fДРУЗЬЯ §cочищены');
                	$host->player->friends = [];
                return false;
                case '.tmsg':
                	if(!isset($message[2])) return;
                	$host->proxy->lib->RunDelayed(1000 * $message[1], function () use (&$host, &$message) {
                		unset($message[0]);
                		unset($message[1]);
                       	$host->player->sendMessage(implode(' ', $message));
                    });
                return false;
                case '.msg':
                	if(!isset($message[1])) return;
                	unset($message[0]);
                	$host->player->sendMessage(implode(' ', $message));
                return false;
                case '.nv':
                    $host->player->nv ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fНОЧНОЕ ЗРЕНИЕ §cвыключено') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fНОЧНОЕ ЗРЕНИЕ §aвключено');
                    if($host->player->nv){
                        $pk = new MobEffectPacket();
                        $pk->entityRuntimeId = $host->player->eid;
                        $pk->eventId = 3;
                        $pk->effectId = 16;
                        $pk->amplifier = 15;
                        $pk->duration = 99999;
                        $pk->particles = false;
                        $host->player->SendToClient($pk);
                    }else{
                        $pk = new MobEffectPacket();
                        $pk->entityRuntimeId = $host->player->eid;
                        $pk->eventId = 1;
                        $pk->effectId = 16;
                        $pk->amplifier = 15;
                        $pk->duration = 99999;
                        $pk->particles = false;
                        $host->player->SendToClient($pk);
                    }
                    !$host->player->nv = !$host->player->nv;
                return false;
                case '.lev':
                    if(!isset($message[1]) && !$host->player->lev) return false;
                    $host->player->lev ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fЛЕВИТАЦИЯ §cвыключена') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fЛЕВИТАЦИЯ §aвключена');
                    if($host->player->lev){
                        $pk = new MobEffectPacket();
                        $pk->entityRuntimeId = $host->player->eid;
                        $pk->eventId = 3;
                        $pk->effectId = 24;
                        $pk->amplifier = 15;
                        $pk->duration = 99999;
                        $pk->particles = false;
                        $host->player->SendToClient($pk);
                    }else{
                        $pk = new MobEffectPacket();
                        $pk->entityRuntimeId = $host->player->eid;
                        $pk->eventId = 1;
                        $pk->effectId = 24;
                        $pk->amplifier = (int) $message[1];
                        $pk->duration = 99999;
                        $pk->particles = false;
                        $host->player->SendToClient($pk);
                    }
                    !$host->player->lev = !$host->player->lev;
                return false;
                case '.jump':
                    if(!isset($message[1]) && !$host->player->jump) return false;
                    $host->player->jump ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fПРЫЖОК §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fПРЫЖОК §aвключен');
                    if($host->player->jump){
                        $pk = new MobEffectPacket();
                        $pk->entityRuntimeId = $host->player->eid;
                        $pk->eventId = 3;
                        $pk->effectId = 8;
                        $pk->amplifier = 15;
                        $pk->duration = 99999;
                        $pk->particles = false;
                        $host->player->SendToClient($pk);
                    }else{
                        $pk = new MobEffectPacket();
                        $pk->entityRuntimeId = $host->player->eid;
                        $pk->eventId = 1;
                        $pk->effectId = 8;
                        $pk->amplifier = (int) $message[1];
                        $pk->duration = 99999;
                        $pk->particles = false;
                        $host->player->SendToClient($pk);
                    }
                    !$host->player->jump = !$host->player->jump;
                return false;
                case '.fb':
                    $host->player->fastbreak ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fБЫСТРОЕ ЛОМАНИЕ §cвыключено') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fБЫСТРОЕ ЛОМАНИЕ §aвключено');
                    if($host->player->fastbreak){
                        $pk = new MobEffectPacket();
                        $pk->entityRuntimeId = $host->player->eid;
                        $pk->eventId = 3;
                        $pk->effectId = 3;
                        $pk->amplifier = 15;
                        $pk->duration = 99999;
                        $pk->particles = false;
                        $host->player->SendToClient($pk);
                    }else{
                        $pk = new MobEffectPacket();
                        $pk->entityRuntimeId = $host->player->eid;
                        $pk->eventId = 1;
                        $pk->effectId = 3;
                        $pk->amplifier = 15;
                        $pk->duration = 99999;
                        $pk->particles = false;
                        $host->player->SendToClient($pk);
                    }
                    !$host->player->fastbreak = !$host->player->fastbreak;
                return false;
                case '.ap':
                	$host->player->ap ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fАВТОАП §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fАВТОАП §aвключен');
                	$host->player->ap = !$host->player->ap;
                return false;
                case '.clip':
                    if(!isset($message[1]) or !is_numeric($message[1])) return false;
                    $host->logger->message('§l§cПРОКСИ §r§7⇒ §fКлипаю на §a'.$message[1].' §fблоков вверх');
                    $y2 = round($host->player->vector3->y) + $message[1];
                    $time = 3;
                    for($y = $y2 - $message[1]; $y <= $y2; $y += 2){
                        $host->proxy->lib->RunDelayed($time, function () use (&$host, $y, $time) {
                            $host->player->move(new Vector3($host->player->vector3->x, $y, $host->player->vector3->z), $host->player->pitch, $host->player->yaw);
                        });
                        $time += 10;
                    }
                return false;
                case '.fe':
                	$host->player->fe ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fБЫСТРАЯ ЕДА §cвыключена') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fБЫСТРАЯ ЕДА §aвключена');
                	$host->player->fe = !$host->player->fe;
                return false;
                case '.criticals':
                	$host->player->criticals ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fСПАМ КРИТАМИ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fСПАМ КРИТАМИ §aвключен');
                	$host->player->criticals = !$host->player->criticals;
                return false;
                case '.crash':
                	$host->player->crash ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fКРАШ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fКРАШ §aвключен');
                	$host->player->crash = !$host->player->crash;
                return false;
                case '.ignoreef':
                    $host->player->ignoreef ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fИГНОР ПЛОХИХ ЭФФЕКТОВ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fИГНОР ПЛОХИХ ЭФФЕКТОВ §aвключен');
                    $host->player->ignoreef = !$host->player->ignoreef;
                return false;
                case '.rmbar':
                    $host->logger->message('§l§cПРОКСИ §r§7⇒ §fБОССБАР §aудален');
                    $host->player->removeEntity($host->player->bossEID);
                return false;
                case '.fe2':
                    $host->player->fe2 ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fБЫСТРАЯ ЕДА2 §cвыключена') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fБЫСТРАЯ ЕДА2 §aвключена');
                    $host->player->fe2 = !$host->player->fe2;
                return false;
                case '.spam':
                    if($host->player->spammer){
                        $host->logger->message('§l§cПРОКСИ §r§7⇒ §fСПАММЕР §cвыключен');
                        $host->player->is_spamming = false;
                    }else{
                        if(!isset($message[1])) return false;
                        $host->logger->message('§l§cПРОКСИ §r§7⇒ §fСПАММЕР §aвключен');
                        unset($message[0]);
                        $host->player->spammsg = implode(' ', $message);
                        $host->player->is_spamming = true;
                        $host->proxy->lib->RunDelayed(7000, function () use (&$host) {
                            $host->server->sendChat(Utils::randomString() . ' ' . $host->player->spammsg . ' ' . Utils::randomString());
                            $host->player->is_spamming = false;
                        });
                    }
                    $host->player->spammer = !$host->player->spammer;
                return false;
                case '.hitb':
                    $host->player->hitboost ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fХИТБУСТ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fХИТБУСТ §aвключен');
                    $host->player->hitboost = !$host->player->hitboost;
                return false;
                case '.ts':
                    if($host->player->ts) {
                        $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТАРГЕТ СТРЕЙФ §cвыключен');
                        $host->player->ts = false;
                        return false;
                    }
                    if(!isset($message[1]) or !is_numeric($message[1])) return false;
                    $host->player->tsdist = $message[1];
                    $host->player->ts ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТАРГЕТ СТРЕЙФ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТАРГЕТ СТРЕЙФ §aвключен');
                    $host->player->ts = !$host->player->ts;
                return false;
                case '.as':
                    $host->player->as ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fАВТОСПРИНТ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fАВТОСПРИНТ §aвключен');
                    $host->player->as = !$host->player->as;
                return false;
                case '.reach':
                    if($host->player->reach) {
                        $host->logger->message('§l§cПРОКСИ §r§7⇒ §fРИЧ §cвыключен');
                        $host->player->reach = false;
                        return false;
                    }
                    if(!isset($message[1]) or !is_numeric($message[1])) return false;
                    $host->player->reach_d = $message[1];
                    $host->player->reach ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fРИЧ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fРИЧ §aвключен');
                    $host->player->reach = !$host->player->reach;
                return false;
                case '.trigger':
                    if($host->player->trigger) {
                        $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТРИГГЕР §cвыключен');
                        $host->player->trigger = false;
                        return false;
                    }
                    if(!isset($message[1]) or !is_numeric($message[1])) return false;
                    $host->player->trigger_d = $message[1];
                    $host->player->trigger ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТРИГГЕР §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТРИГГЕР §aвключен');
                    $host->player->trigger = !$host->player->trigger;
                return false;
                case '.tp':
                    $host->player->taptp ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТАПТП §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fТАПТП §aвключен');
                    $host->player->taptp = !$host->player->taptp;
                return false;
                case '.invis':
                    $host->player->invis ? $host->logger->message('§l§cПРОКСИ §r§7⇒ §fИНВИЗ §cвыключен') : $host->logger->message('§l§cПРОКСИ §r§7⇒ §fИНВИЗ §aвключен');
                    $host->player->invis = !$host->player->invis;
                return false;
                case '.help':
                	foreach($host->functions as $func => $string){
                		if(!is_numeric($func)){
                			if(isset($host->asoci[$func])){
                				$fun = $host->asoci[$func];
                				$host->player->$fun ? $host->logger->message('§c'.$func.' §f— '.$string) : $host->logger->message('§a'.$func.' §f— '.$string);
                			}else{
                				$host->logger->message('§a'.$func.' §f— '.$string);
                			}
                		}else{
                			$host->logger->message($string);
                		}
                	}
                return false;
            }
        return true;
        case ProtocolInfo::USE_ITEM_PACKET:
            $pk = new UseItemPacket($payload);
            $pk->decode();
            if($host->player->taptp && $pk->blockId !== 0){
                $host->player->move(new Vector3($pk->x, $pk->y + 1, $pk->z), $host->player->pitch, $host->player->yaw);
            }
        return true;
        case ProtocolInfo::ENTITY_FALL_PACKET:
        return false;
        case ProtocolInfo::PLAYER_ACTION_PACKET:
            $pk = new PlayerActionPacket($payload);
            $pk->decode();
            if($pk->action === PlayerActionPacket::ACTION_JUMP) return false;
            if($host->player->lj && $pk->action === PlayerActionPacket::ACTION_JUMP){
                $host->player->setMotion($host->player->getDirectionVector()->multiply(1.2));
            }elseif($pk->action === PlayerActionPacket::ACTION_START_SNEAK && $host->player->blink){
                if(!$host->player->blinked){
                    $pk = new AddPlayerPacket();
                    $pk->entityRuntimeId = 9991;
                    $pk->x = (float) $host->player->vector3->x;
                    $pk->y = (float) $host->player->vector3->y - 2;
                    $pk->z = (float) $host->player->vector3->z;
                    $pk->item = $host->player->getInventory()->itemInHand;
                    $pk->uuid = UUID::fromRandom();
                    $pk->username = 'blinked';
                    $pk->bodyYaw = $host->player->yaw;
                    $pk->yaw = $host->player->yaw;
                    $pk->pitch = $host->player->pitch;
                    $host->player->SendToClient($pk);

                    $host->player->blinked = true;
                }else{
                    $host->player->removeEntity(9991);
                    $host->player->blinked = false;
                }
            }
        break;
        case ProtocolInfo::INTERACT_PACKET:
            $pk = new InteractPacket($payload);
            $pk->decode();
            if($pk->action === 4){
                $host->player->tick();
            }elseif($pk->action === 1 && $host->player->nm_){
                if(isset($host->server->getPlayers()[$pk->target])){
                    $player = $host->server->getPlayers()[$pk->target];
                    $player->setNameTag($host->player->nm);
                    $host->player->nm_ = false;
                }
            }elseif($pk->action === 1 && $host->player->add){
            	$host->player->add = false;
            	if(!isset($host->server->getPlayers()[$pk->target])){
            		$host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fЭТО НЕ ИГРОК', 3);
            		return false;
            	}
            	$host->player->friends[$pk->target] = true;
            	$host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §f '.$host->server->getPlayers()[$pk->target]->getName().' добавлен в друзья', 3);
            	return false;
            }elseif($pk->action === 1 && $host->player->rem){
            	$host->player->rem = false;
            	if(!isset($host->server->getPlayers()[$pk->target])){
            		$host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fЭТО НЕ ИГРОК', 3);
            		return false;
            	}elseif(!isset($host->player->friends[$pk->target])){
            		$host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fЭТО НЕ ВАШ ДРУГ', 3);
            		return false;
            	}
            	unset($host->player->friends[$pk->target]);
            	$host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §f'.$host->server->getPlayers()[$pk->target]->getName().' удален из друзей', 3);
            	return false;
            }elseif($pk->action === 2 && $host->player->hitboost){
                if(isset($host->server->getPlayers()[$pk->target])){
                    $pitch = (($host->player->pitch + 90) * M_PI) / 180;
                    $yaw = (($host->player->yaw + 90) * M_PI) / 180;
                    $host->player->setMotion(new Vector3((sin($pitch) * cos($yaw)) * 1.3, cos($pitch) * 1.3, (sin($pitch) * sin($yaw)) * 1.3));
                }
            }
        break;
        case ProtocolInfo::MOVE_PLAYER_PACKET:
            $pk = new MovePlayerPacket($payload);
            $pk->decode();
            $host->player->eid = $pk->entityRuntimeId;
            $host->server->reset($pk);
            if($host->player->as) {
                $pk = new PlayerActionPacket();
                $pk->entityRuntimeId = $host->player->eid;
                $pk->action = PlayerActionPacket::ACTION_START_SPRINT;
                $pk->x = (int) $host->player->vector3->x;
                $pk->y = (int) $host->player->vector3->y;
                $pk->z = (int) $host->player->vector3->z;
                $host->player->SendToClient($pk);
                $host->player->SendToServer($pk);
            }
            if($host->player->spinner && !$host->player->blinked){
            	$pk->pitch = -180;
            	$pk->yaw = rand(0, 360);
            	$pk->bodyYaw = rand(0, 360);
            	$host->player->SendToServer($pk);
                return false;
            }
        return !$host->player->blinked;
        case ProtocolInfo::MOB_EQUIPMENT_PACKET:
            $pk = new MobEquipmentPacket($payload);
            $pk->decode();
            if($pk->entityRuntimeId === $host->player->eid && $pk->hotbarSlot <= 8 && $pk->hotbarSlot >= 0){
                if($host->player->fe2 && $pk->item->canBeConsumed() && !$host->player->is_consumed){
                    $host->player->is_consumed = true;
                    $host->proxy->lib->RunDelayed(30, function () use (&$host, $pk) {
                        $pk2 = new EntityEventPacket();
                        $pk2->entityRuntimeId = $host->player->eid;
                        $pk2->event = 9;
                        $pk2->data = $pk->item->getId();
                        $host->player->SendToServer($pk2);
                    });
                    $slot = $host->player->getInventory()->selectedSlot;
                    $host->proxy->lib->RunDelayed(320, function () use (&$host, &$slot) {
                        $host->player->getInventory()->sendSelectedSlot($slot);
                        $host->player->is_consumed = false;
                    });
                    return true;
                }
                $host->player->getInventory()->selectedSlot = $pk->hotbarSlot;
                $host->player->getInventory()->itemInHand = $pk->item;
            }
        return !$host->player->food_start;
        case ProtocolInfo::ANIMATE_PACKET:
        	$pk = new AnimatePacket($payload);
        	$pk->decode();
            if($pk->action === 1 && $host->player->reach) {
                foreach($host->server->getPlayers() as $eid => $player) {
                    if(!isset($host->player->friends[$eid]) && $player->asVector3()->distance($host->player->vector3) <= $host->player->reach_d && $host->player->isLookingAt($player->asVector3())) {
                        $host->player->attack($eid, true);
                        break;
                    }
                }
            }
        	if($host->player->blinked) {
        		$pk->entityRuntimeId = 9991;
        		$host->player->SendToClient($pk);
        	}
        break;
        case ProtocolInfo::ADVENTURE_SETTINGS_PACKET:
            $pk = new AdventureSettingsPacket($payload);
            $pk->decode();
        return !$host->player->fly;
    }
    return true;
});

$host->proxy->subscribeOnServerPayloadRecvEvent(function($payload, $len) use(&$host){
    switch(ord($payload[0])){
        case ProtocolInfo::TEXT_PACKET:
            $pk = new TextPacket($payload);
            $pk->decode();
            if($pk->type === 0){
            	foreach($host->player->friends as $eid => $bool){
            		if(isset($host->server->getPlayers()[$eid])){
            			$player = $host->server->getPlayers()[$eid];
            			if(strpos(TextFormat::clean($pk->message), $player->getName()) !== false){
            				$host->player->sendMessage($pk->message, 3);
            			}
            		}
            	}
                foreach(explode(PHP_EOL, TextFormat::toANSI($pk->message)) as $msg) $host->logger->info($msg);
            }
        break;
        case ProtocolInfo::BOSS_EVENT_PACKET:
            $pk = new BossEventPacket($payload);
            $pk->decode();
            $host->player->bossEID = $pk->bossEid;
            // $host->player->removeEntity($pk->bossEid);
        break;
        case ProtocolInfo::ADD_PLAYER_PACKET:
            $pk = new AddPlayerPacket($payload);
            $pk->decode();
            $host->server->addPlayer($pk);
        break;
        case ProtocolInfo::ADD_ENTITY_PACKET:
            $pk = new AddEntityPacket($payload);
            $pk->decode();
            $host->server->addEntity($pk);
        break;
        case ProtocolInfo::REMOVE_ENTITY_PACKET:
            $pk = new RemoveEntityPacket($payload);
            $pk->decode();
            $host->server->remove($pk->entityUniqueId);
        break;
        case ProtocolInfo::MOVE_ENTITY_PACKET:
            $pk = new MoveEntityPacket($payload);
            $pk->decode();
            $host->server->reset($pk);
        break;
        case ProtocolInfo::MOVE_PLAYER_PACKET:
            $pk = new MovePlayerPacket($payload);
            $pk->decode();
            $host->server->reset($pk);
        break;
        case ProtocolInfo::TRANSFER_PACKET:
            $host->server->clear();
        break;
        case ProtocolInfo::SET_ENTITY_MOTION_PACKET:
            $pk = new SetEntityMotionPacket($payload);
            $pk->decode();
            if($host->player->kb && $pk->entityRuntimeId === $host->player->eid) return false;
        break;
        case ProtocolInfo::CONTAINER_OPEN_PACKET:
            $pk = new ContainerOpenPacket($payload);
            $pk->decode();
            $host->player->window = $pk->windowid;
        return !$host->player->loot;
        case ProtocolInfo::CONTAINER_SET_SLOT_PACKET:
            $pk = new ContainerSetSlotPacket($payload);
            $pk->decode();
            if($host->player->food_start){
            	return false;
            }
            if($pk->windowid === 0 && $pk->slot !== null){
                $host->player->getInventory()->items[$pk->slot] = $pk->item;
            }
        break;
        case ProtocolInfo::CONTAINER_SET_CONTENT_PACKET:
            $pk = new ContainerSetContentPacket($payload);
            $pk->decode();
            if($pk->windowid === $host->player->window && $host->player->loot){
                foreach($pk->slots as $slot => $item){
                    $slot_inv = $host->player->getInventory()->findSlot();
                    if($slot_inv === null) return $host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fИнвентарь полный', 4);
                    if($slot !== null && $item->getId() !== 0){
                        $host->player->getInventory()->setItem($slot, Item::get(0), $pk->windowid);
                        $host->player->getInventory()->setItem($slot_inv, $item);
                    }
                }
                $host->player->sendMessage('§l§cПРОКСИ §r§7⇒ §fСУНДУК ЗАЛУТАН', 4);
                $host->player->getInventory()->close($pk->windowid);
                return false;
            }
        break;
        case ProtocolInfo::ADVENTURE_SETTINGS_PACKET:
            $pk = new AdventureSettingsPacket($payload);
            $pk->decode();
            $pk->allowFlight = $host->player->fly;
            $host->player->SendToClient($pk);
        return false;
        case ProtocolInfo::ENTITY_EVENT_PACKET:
        	$pk = new EntityEventPacket($payload);
        	$pk->decode();
        	if($pk->event == 57) return false;
        break;
        case ProtocolInfo::MOB_EQUIPMENT_PACKET:
        	if($host->player->food_start) return false;
        break;
        case ProtocolInfo::MOB_EFFECT_PACKET:
            $pk = new MobEffectPacket($payload);
            $pk->decode();
            if($pk->eventId === 1 && $host->player->ignoreef){
                return !($pk->effectId === 2 || $pk->effectId === 15);
            }
        break;
        case ProtocolInfo::PLAYER_LIST_PACKET:
            $pk = new PlayerListPacket($payload);
            $pk->decode();
            if($pk->type === 0) {
                foreach($pk->entries as $entry){
                    $host->server->addEntry($entry[1], $entry[2]);
                }
            }
        break;
        case ProtocolInfo::SET_ENTITY_DATA_PACKET:
            $pk = new SetEntityDataPacket($payload);
            $pk->decode();
            foreach($pk->metadata as $meta => $data) {
                if($meta === Entity::DATA_POTION_COLOR && $data[1] == -8420462 && $host->player->invis){
                    if(isset($host->server->getPlayers()[$pk->entityRuntimeId])){
                        $player = $host->server->getPlayers()[$pk->entityRuntimeId];
                        $host->proxy->lib->RunDelayed(500, function () use (&$host, &$player) {
                            $player->setNameTag($player->getNameTag().' §8(§7INVIS§8])');
                            $player->setInvisible(false);
                        });
                        return false;
                    }
                }
            }
        break;
        case ProtocolInfo::UPDATE_ATTRIBUTES_PACKET:
        	$pk = new UpdateAttributesPacket($payload);
        	$pk->decode();
        	foreach($pk->entries as $entry){
        		switch($entry->getName()){
        			case 'minecraft:player.hunger':
        				var_dump(0);
        				if($entry->getValue() <= 15 && ($slot_inv = $host->player->getInventory()->findSlot()) !== null && $host->player->fe){
        					foreach($host->player->getInventory()->items as $slot_item => $item){
        						if($item->canBeConsumed() && !$host->player->food_start && $item->getCount() > 0){
        							$host->player->food_start = true;
        							if($slot_item > 8){
        								if(($slot_hot = $host->player->getInventory()->findHotbarSlot()) !== null){

        									$host->player->getInventory()->setItem($slot_item, Item::get(0), 0, false);
        									$host->player->getInventory()->setItem($slot_hot, $item, 0, false);
        									$host->player->getInventory()->sendSelectedSlot($slot_hot, false);
        									$host->proxy->lib->RunDelayed(100, function () use (&$host, &$item) {
        										$pk = new EntityEventPacket();
        										$pk->entityRuntimeId = $host->player->eid;
        										$pk->event = 9;
        										$host->player->SendToServer($pk);
        										$item->setCount($item->getCount() - 1);
                    						});

        									$host->proxy->lib->RunDelayed(200, function () use (&$host, &$item, &$slot_hot, &$slot_item) {
        										$host->player->getInventory()->sendSelectedSlot($host->player->getInventory()->getSelectedSlot(), false);
        										$host->player->getInventory()->setItem($slot_hot, Item::get(0), 0, false);
        										if($item->getCount() > 0){
        											$host->player->getInventory()->setItem($slot_item, $item);
        										}
        										$host->player->food_start = false;
                    						});
        								}
        							}else{
        								$host->player->getInventory()->sendSelectedSlot($slot_item, false);
        								$pk = new EntityEventPacket();
        								$pk->entityRuntimeId = $host->player->eid;
        								$pk->event = 9;
        								$host->player->SendToServer($pk);
        								$item->setCount($item->getCount() - 1);

        								$host->proxy->lib->RunDelayed(200, function () use (&$host, &$slot_item, &$item) {
        									$host->player->getInventory()->sendSelectedSlot($host->player->getInventory()->getSelectedSlot(), false);
        									$host->player->food_start = false;
        									$host->player->getInventory()->setItem($slot_item, $item);
                    					});
        							}
        							return true;
        						}
        					}
        				}
        			break;
        			case 'minecraft:health':
        				if($entry->getValue() <= 10 && $host->player->ap){
        					$host->server->sendCommand('ap');
        				}
        			break;
        		}
        	}
        break;
    }
    return true;
});

$host->proxy->lib->StartProxy($host->address->asString());
