<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\event;

use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\ScoreHud;
use pocketmine\event\Event;
use pocketmine\Player;

abstract class ScoreHudEvent extends Event{

	/** @var ScoreHud|null */
	protected $plugin = null;
	/** @var Player */
	protected $player;
	/** @var ScoreTag */
	protected $tag;

	public function __construct(Player $player, ScoreTag $tag){
		$this->plugin = ScoreHud::getInstance();
		$this->player = $player;
		$this->tag = $tag;
	}

	public function getPlugin(): ?ScoreHud{
		return $this->plugin;
	}

	public function getPlayer(): Player{
		return $this->player;
	}

	public function getTag(): ScoreTag{
		return $this->tag;
	}
}