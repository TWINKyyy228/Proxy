<?php

require_once('./proxyface.php');
require_once('./utils/Host.php');
require_once('./utils/Logger.php');
require_once('./utils/Binary.php');
require_once('./utils/Utils.php');
require_once('./utils/Address.php');
require_once('./other/Player.php');
require_once('./other/Level.php');
require_once('./other/Server.php');
require_once('./vendor/autoload.php');

use pocketmine\utils\{TextFormat, Terminal, UUID};
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\entity\{Entity, Attribute};
use pocketmine\inventory\InventoryType;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\entity\Effect;
use pocketmine\tile\Tile;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\Network;

class Host {

	public $address, $nick, $os, $model, $skindata, $functions, $asoci;
	public $proxy, $logger, $player, $server, $level, $network;

	public function __construct($address, string $nick, $os, string $model, string $skindata) {
		$this->nick = $nick;
		$this->address = $address;
		$this->os = $os;
		$this->model = $model;
		$this->skindata = $skindata;

		Entity::init();
		Tile::init();
		InventoryType::init();
		Block::init();
		Enchantment::init();
		Item::init();
		Attribute::init();
		Terminal::init();
		PacketPool::init();

		ini_set('memory_limit', '-1');
		set_time_limit(-1);
		define("ENDIANNESS", (pack("d", 1) === "\77\360\0\0\0\0\00" ? 0x00 : 0x01));
		define("INT32_MASK", is_int(0xffffffff) ? 0xffffffff : -1);

		$this->proxy = new Proxyface();
		$this->logger = new Logger($this);
		$this->server = new Server($this);
		$this->player = new Player($this);
		$this->level = new Level($this);
		$this->network = new Network();

		$this->player->name = $nick;
		$this->functions = [
			"\n\n",
			'§l§cПРОКСИ §r§7⇒ §fКоманды доступных читов:',
			'.kl' => 'Включение функции §aКИЛЛАУРЫ §8(§7KillAura§8)',
			'.kb' => 'Отключает ваше §aОТКИДЫВАНИЕ §8(§7AntiKnockBack§8)',
		    '.loot' => 'Вместо вас собирает ресурсы с §aСУНДУКА §8(§7AutoLoot§8)',
		    '.lj' => 'Включение функции дальнего §aПРЫЖКА §8(§7LongJump§8)',
		    '.trace' => 'Показывает где находится §aИГРОК §8(§7Tracers§8)',
		    '.fly' => 'Включить функцию §aФЛАЯ §8(§7Fly§8)',
		    '.hb (x) (y)' => 'Меняет дальность ваших §aУДАРОВ §8(§7HitBox§8)',
		    '.spinner' => 'Опускает голову вниз и начинает крутить §aТЕЛО §8(§7Spinner§8)',
		    '.fe' => 'Начинает есть еду из §aИНВЕНТАРЯ §8(§7FastEat§8)',
		    '.fe2' => 'Ест еду которая находится в §aРУКЕ §8(§7FastEat2§8)',
     	    '.blink' => 'При использовании шифта оставляет вашу §aСУЩНОСТЬ §8(§7Blink§8)',
     	    '.ap' => 'Автоматически использует команду §a/AP §8(§7AutoAP§8)',		  
     	    '.as' => 'Включает функцию автомаческого §aБЕГА §8(§7AutoSprint§8)',
		    '.sp (speed)' => 'Включает функцию быстрой §aХОДЬБЫ §8(§7SpeedHack§8)',
		    '.hitb' => 'При ударе противника вас §aПОДБРАСЫВАЕТ §8(§7HitBoost§8)',
		    '.ts (distance)' => 'Включение киллауры с §aВИЗУАЛАМИ §8(§7TargetStrafe§8)',
		    '.reach (distance)' => 'Включение §aРИЧЕЙ §8(§7Reach: ГП/ПК§8)',
		    '.trigger (distance)' => 'Включение §aТРИГГЕРА §8(§7TriggerBot§8)',
		    '.tp' => 'Включает функцию телепортации по §aНАЖАТИЮ §8(§7TapTP§8)',
		    "\n\n",
		    '§l§cПРОКСИ §r§7⇒ §fПомощь по друзьям:',
		    '.add' => 'Добавить игрока в §aДРУЗЬЯ',
		    '.list' => 'Список ваших §aДРУЗЕЙ',
		    '.rem' => 'Удалить игрока из §aДРУЗЕЙ',
		    '.clear' => 'Очистить всех §aДРУЗЕЙ',
		    '.clip (высота)' => 'Включение клипа по §aВЫСОТЕ §8(§7Clip§8)',
		    "\n\n",
		    '§l§cПРОКСИ §r§7⇒ §fРазличные доступные функции',
		    '.tr (ip:port)' => 'Переносит вас на указаный §aСЕРВЕР',
		    '.dupe' => 'Выдает различные §aПРЕДМЕТЫ §8(§7Не работает у обычных юзеров§8)',
		    '.nm (nick)' => 'Меняет указанному игроку §aНИКНЕЙМ',
		    '.msg (message)' => 'Отправляет вам указанное §aСООБЩЕНИЕ',
		    '.tmsg (time in seconds) (message)' => 'Отправляет сообщение через указанное кол-во §aСЕКУНД',
		    '.crash' => 'Отправляет краш-пакет игрокам §aВОКРУГ',
		    '.criticals' => 'Вокруг вас начнут спамится §aКРИТ-ПАРТИЛЫ',
		    '.rmbar' => 'Удаляет для вас боссбар на §aСЕРВЕРЕ',
		    '.spam (message)' => 'Спамит сообщение каждые §a7 СЕКУНД',
		    "\n\n",
		    '§l§cПРОКСИ §r§7⇒ §fВзаимодействие с эффектами:',
		    '.nv' => 'Выдает вам ночное §aЗРЕНИЕ',
		    '.fb' => 'Выдает вам эффект §aСПЕШКИ',
		    '.lev (сила)' => 'Выдает вам эффект §aЛЕВИТАЦИИ',
		    '.jump (сила)' => 'Выдает вам эффект §aПРЫЖКА',
		    '.invis' => 'Отключает невидимость у других §aИГРОКОВ §8(§7Временно не работает§8)',
		    '.ignoreef' => 'Игнорирует эффекты §aСЕРВЕРОВ §8(§7Плохие§8)',
		    "\n\n",
		];
		$this->asoci = [
			'.kl' => 'kl',
			'.kb' => 'kb',
			'.loot' => 'loot',
			'.lj' => 'lj',
			'.trace' => 'tracers',
			'.fly' => 'fly',
			'.hb (x) (y)' => 'hb',
			'.spinner' => 'spinner',
			'.blink' => 'blink',
			'.add' => 'add',
			'.dupe' => 'dupe',
			'.ap' => 'ap',
			'.fe' => 'fe',
			'.crash' => 'crash',
			'.criticals' => 'criticals',
			'.ignoreef' => 'ignoreef',
			'.fe2' => 'fe2',
			'.nv' => 'nv',
			'.fb' => 'fastbreak',
			'.nm (nick)' => 'nm_',
			'.spam (message)' => 'spammer',
			'.rem' => 'rem',
			'.add' => 'add',
			'.hitb' => 'hitboost',
			'.ts (distance)' => 'ts',
			'.as' => 'as',
			'.reach (distance)' => 'reach',
			'.trigger (distance)' => 'trigger',
			'.tp' => 'taptp',
			'.lev' => 'lev',
			'.jump' => 'jump',
			'.invis' => 'invis',
		];

		$this->proxy->lib->SetSkinID('Minecon_MineconSteveCape2011');
		// $this->proxy->lib->SetSkinData($skindata);
		// $this->proxy->lib->SetNickname($nick);
		// // $this->proxy->lib->SetClientID(-956249315208516525);
		$this->proxy->lib->SetRPDownloadBypass(true);
		$this->proxy->lib->SetInputMode(2);
		$this->proxy->lib->SetDefaultInputMode(2);
		$this->proxy->lib->SetUIProfile(1);
		$this->proxy->lib->SetDeviceOS($os);
		$this->proxy->lib->GenerateAndSaveClientID();
		$this->proxy->lib->SetDeviceModel($model);
	}


}