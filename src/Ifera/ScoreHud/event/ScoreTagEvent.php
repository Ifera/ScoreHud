<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\event;

use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\Player;

abstract class ScoreTagEvent extends ScoreHudEvent{

	/** @var ScoreTag */
	protected $tag;

	public function __construct(Player $player, ScoreTag $tag){
		$this->tag = $tag;

		parent::__construct($player);
	}

	public function getTag(): ScoreTag{
		return $this->tag;
	}

	public function setTag(ScoreTag $tag): void{
		$this->tag = $tag;
	}
}