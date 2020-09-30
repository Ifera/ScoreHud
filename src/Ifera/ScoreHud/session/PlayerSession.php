<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\session;

use Ifera\ScoreHud\scoreboard\Scoreboard;
use Ifera\ScoreHud\scoreboard\ScoreboardHelper;
use Ifera\ScoreHud\ScoreHud;
use jackmd\scorefactory\ScoreFactory;
use pocketmine\Player;
use function array_keys;
use function array_values;
use function str_replace;

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

	public function getPlayer(): Player{
		return $this->player;
	}

	public function getScoreboard(): Scoreboard{
		return $this->scoreboard;
	}

	public function setScoreboard(Scoreboard $scoreboard): void{
		$this->scoreboard = $scoreboard;
	}

	public function initialize(): void{
		ScoreFactory::setScore($this->player, "TEST");//todo move this?
		$sb = ScoreboardHelper::createDefault($this);
		$tags = $sb->getProcessedTags();

		$lines = $this->plugin->getScoreHudConfig()->get("score-lines");

		$i = 0;

		foreach($lines as $line){
			$i++;

			if($i <= 15){
				$formattedString = str_replace(
					array_keys($tags),
					array_values($tags),
					$line
				);

				ScoreFactory::setScoreLine($this->player, $i, $formattedString);
			}
		}
	}

	public function close(): void{
		ScoreFactory::removeScore($this->player);
	}
}