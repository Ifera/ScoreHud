<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\session;

use Ifera\ScoreHud\scoreboard\Scoreboard;
use Ifera\ScoreHud\ScoreHud;
use pocketmine\Player;

class PlayerSession{

	/** @var ScoreHud */
	private $plugin;
	/** @var Player */
	private $player;
	/** @var Scoreboard */
	private $scoreboard;

	public function __construct(Player $player){
		$this->plugin = ScoreHud::getInstance();
		$this->player = $player;
	}

	public function getScoreboard(): Scoreboard{
		return $this->scoreboard;
	}

	public function setScoreboard(Scoreboard $scoreboard): void{
		$this->scoreboard = $scoreboard;
	}

	public function close(): void{

	}
}