<?php

require_once('./utils/Host.php');
use pocketmine\utils\TextFormat;

class Logger {

    public $host;

    public function __construct(Host $host) {
        $this->host = $host;
    }

    public function debug(string $message) : void {
        date_default_timezone_set('Europe/Moscow');
        echo TextFormat::toANSI(TextFormat::GRAY.'['.TextFormat::DARK_GRAY.'DEBUG'.TextFormat::GRAY.'] '.TextFormat::GRAY.'['.TextFormat::WHITE.date('H:i:s', time()).TextFormat::GRAY.']: '.TextFormat::WHITE.$message.TextFormat::RESET).PHP_EOL;
    }

    public function info(string $message) : void {
        date_default_timezone_set('Europe/Moscow');
        echo TextFormat::toANSI(TextFormat::GRAY.'['.TextFormat::AQUA.'INFO'.TextFormat::GRAY.'] '.TextFormat::GRAY.'['.TextFormat::WHITE.date('H:i:s', time()).TextFormat::GRAY.']: '.TextFormat::WHITE.$message.TextFormat::RESET).PHP_EOL;
    }

    public function add(string $message) : void {
        date_default_timezone_set('Europe/Moscow');
        echo TextFormat::toANSI(TextFormat::GRAY.'['.TextFormat::GREEN.'ADD'.TextFormat::GRAY.'] '.TextFormat::GRAY.'['.TextFormat::WHITE.date('H:i:s', time()).TextFormat::GRAY.']: '.TextFormat::WHITE.$message.TextFormat::RESET).PHP_EOL;
    }

    public function remove(string $message) : void {
        date_default_timezone_set('Europe/Moscow');
        echo TextFormat::toANSI(TextFormat::GRAY.'['.TextFormat::RED.'REMOVE'.TextFormat::GRAY.'] '.TextFormat::GRAY.'['.TextFormat::WHITE.date('H:i:s', time()).TextFormat::GRAY.']: '.TextFormat::WHITE.$message.TextFormat::RESET).PHP_EOL;
    }

    public function error(string $message) : void {
        date_default_timezone_set('Europe/Moscow');
        echo TextFormat::toANSI(TextFormat::GRAY.'['.TextFormat::RED.'ERROR'.TextFormat::GRAY.'] '.TextFormat::GRAY.'['.TextFormat::WHITE.date('H:i:s', time()).TextFormat::GRAY.']: '.TextFormat::WHITE.$message.TextFormat::RESET).PHP_EOL;
    }

    public function message(string $message) : void {
        $this->host->player->sendMessage($message);
        $this->info($message);
    }

}
