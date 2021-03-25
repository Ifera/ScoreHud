<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\event;

use pocketmine\Player;

class PlayerEvent extends ScoreHudEvent{
	
	/** @var Player */
	protected $player;

	public function __construct(Player $player){
		$this->player = $player;

		parent::__construct();
	}

	public function getPlayer(): Player{
		return $this->player;
	}
}