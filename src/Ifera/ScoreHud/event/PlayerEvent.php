<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\event;

use pocketmine\player\Player;

abstract class PlayerEvent extends ScoreHudEvent{

	public function __construct(private Player $player){
		parent::__construct();
	}

	public function getPlayer(): Player{
		return $this->player;
	}
}