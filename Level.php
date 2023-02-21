<?php

require_once('./utils/Host.php');

use pocketmine\math\Vector3;
use pocketmine\level\particle\{FlameParticle, SmokeParticle};
use pocketmine\network\mcpe\protocol\SetTimePacket;

class Level {

	private $host;

	public function __construct(Host $host) {
		$this->host = $host;
	}

	public function setTime(int $time = 0) : void {
        $pk = new SetTimePacket;
        $pk->time = $time;
        $this->host->player->SendToClient($pk);
    }

    public function addLine(Vector3 $start, Vector3 $end) : void {
        $start->y -= 0.5;
        $end->y -= 0.2;
        $distance = $start->distance($end);
        $space = $end->subtract($start)->normalize()->multiply(0.5);
        
        for($covered = 0; $covered < $distance; $start = $start->add($space)){
            $this->addParticle(new FlameParticle($start));
            $covered += 0.5;
        }
    }

    public function moveCircle(Vector3 $vector_t, int $r) : void {
        $host = $this->host;
        $x = $vector_t->x;
        $z = $vector_t->z;

        $time = 0;

        for($i = 0; $i < 360; $i += 120){
            $angle = $i;
            $x1 = $r * cos($angle * M_PI / 180);
            $z1 = $r * sin($angle * M_PI / 180);
            $host->proxy->lib->RunDelayed($time, function () use (&$host, &$x, &$x1, &$vector_t, &$z, &$z1) {
                $host->player->move(new Vector3($x + $x1, $vector_t->y, $z + $z1), $host->player->pitch, $host->player->yaw, true);
            });
            $time += 800;
        }
    }

    public function addCircle(Vector3 $vector, int $r) : void {
        $x = $vector->x;
        $z = $vector->z;

        for($i = 0; $i < 360; $i += 1){
            $angle = $i;
            $x1 = $r * cos($angle * M_PI / 180);
            $z1 = $r * sin($angle * M_PI / 180);
            $this->addParticle(new SmokeParticle(new Vector3($x + $x1, $vector->y, $z + $z1)));
        }
    }

    public function addParticle($particle) : void { 
        $this->host->player->SendToClient($particle->encode());
    }

}