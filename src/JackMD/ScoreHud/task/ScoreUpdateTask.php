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
 * Copyright (c) 2018 JackMD  < https://github.com/JackMD >
 *
 * Discord: JackMD#3717
 * Twitter: JackMTaylor_
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

namespace JackMD\ScoreHud\task;

use JackMD\ScoreHud\Main;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class ScoreUpdateTask extends Task{
	
	/** @var Main */
	private $plugin;
	/** @var int */
	private $titleIndex = 0;
	
	/**
	 * ScoreUpdateTask constructor.
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
		$this->titleIndex = 0;
	}
	
	/**
	 * @param int $tick
	 */
	public function onRun(int $tick){
		$players = $this->plugin->getServer()->getOnlinePlayers();
		$dataConfig = new Config($this->plugin->getDataFolder() . "data.yml", Config::YAML);
		$titles = $dataConfig->get("server-names");
		if((is_null($titles)) || empty($titles) || !isset($titles)){
			$this->plugin->getLogger()->error("Please set server-names in data.yml properly.");
			$this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
			return;
		}
		if(!isset($titles[$this->titleIndex])){
			$this->titleIndex = 0;
		}
		foreach($players as $player){
			$this->plugin->addScore($player, $titles[$this->titleIndex]);
		}
		$this->titleIndex++;
	}
}