<?php
declare(strict_types = 1);

/**
 *     _____                    _   _           _
 *    /  ___|                  | | | |         | |
 *    \ `--.  ___ ___  _ __ ___| |_| |_   _  __| |
 *     `--. \/ __/ _ \| '__/ _ \  _  | | | |/ _` |
 *    /\__/ / (_| (_) | | |  __/ | | | |_| | (_| |
 *    \____/ \___\___/|_|  \___\_| |_/\__,_|\__,_|
 *
 * ScoreHud, a Scoreboard plugin for PocketMine-MP
 * Copyright (c) 2020 Ifera  < https://github.com/Ifera >
 *
 * Discord: Ifera#3717
 * Twitter: ifera_tr
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * ScoreHud is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace Ifera\ScoreHud\session;

use Ifera\ScoreHud\scoreboard\Scoreboard;
use Ifera\ScoreHud\scoreboard\ScoreboardHelper;
use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\ScoreHudSettings;
use Ifera\ScoreHud\utils\HelperUtils;
use jackmd\scorefactory\ScoreFactory;
use pocketmine\player\Player;
use function is_null;

class PlayerSession{

	/** @var ScoreHud */
	private ScoreHud $plugin;
	/** @var Scoreboard|null */
	private ?Scoreboard $scoreboard;

	public function __construct(private Player $player){
		$this->plugin = ScoreHud::getInstance();
		$this->scoreboard = null;
	}

	public function getPlayer(): Player{
		return $this->player;
	}

	public function getScoreboard(): ?Scoreboard{
		return $this->scoreboard;
	}

	public function setScoreboard(Scoreboard $scoreboard): void{
		$this->scoreboard = $scoreboard;
	}

	public function handle(string $world = null, bool $calledFromTask = false): void{
		$player = $this->player;

		if(!$player->isOnline() || HelperUtils::isDisabled($player)){
			return;
		}

		$world = $world ?? $player->getWorld()->getFolderName();

		// remove scoreboard if player is in a world where scoreboard is disabled
		if(ScoreHudSettings::isInDisabledWorld($world)){
			ScoreFactory::removeObjective($player);

			return;
		}

		// check for multi world board first
		if(ScoreHudSettings::isMultiWorld()){
			// construct the board for this level and send
			if(ScoreHudSettings::worldExists($world)){
				$this->plugin->setScore($player, $calledFromTask);

				$scoreboard = ScoreboardHelper::create($this, $world);
				$scoreboard->update()->display();

				$this->scoreboard = $scoreboard;

				return;
			}

			// use the default board since the scoreboard for the world is unknown
			if(ScoreHudSettings::useDefaultBoard()){
				$this->constructDefaultBoard($calledFromTask);

				return;
			}

			// no scoreboard is to be displayed
			ScoreFactory::removeObjective($player);

			return;
		}

		// construct the default board since multi world support is not enabled
		$this->constructDefaultBoard($calledFromTask);
	}

	/**
	 * Used for handling default scoreboard
	 */
	private function constructDefaultBoard(bool $calledFromTask): void{
		$this->plugin->setScore($this->player, $calledFromTask);

		if($calledFromTask && !is_null($this->scoreboard)){
			$this->scoreboard->display();

			return;
		}

		$scoreboard = ScoreboardHelper::createDefault($this);
		$scoreboard->update()->display();

		$this->scoreboard = $scoreboard;
	}

	public function close(): void{
		HelperUtils::destroy($this->player);
		ScoreFactory::removeObjective($this->player, true);
	}
}