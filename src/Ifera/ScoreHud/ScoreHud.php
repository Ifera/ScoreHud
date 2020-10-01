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

namespace Ifera\ScoreHud;

use Ifera\ScoreHud\session\PlayerSessionHandler;
use JackMD\ConfigUpdater\ConfigUpdater;
use Ifera\ScoreHud\utils\Utils;
use JackMD\UpdateNotifier\UpdateNotifier;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class ScoreHud extends PluginBase{

	/** @var int */
	private const CONFIG_VERSION = 9;
	/** @var int */
	private const SCOREHUD_VERSION = 2;

	/** @var ScoreHud|null */
	private static $instance = null;

	/** @var Config */
	private $scoreConfig;

	/**
	 * @return ScoreHud|null
	 */
	public static function getInstance(): ?ScoreHud{
		return self::$instance;
	}

	public function onLoad(){
		self::$instance = $this;
	}

	public function onEnable(){
		$this->checkConfigs();

		Utils::checkVirions();
		UpdateNotifier::checkUpdate($this->getDescription()->getName(), $this->getDescription()->getVersion());
		ScoreHudSettings::init($this);

		if(!$this->canLoad()){
			return;
		}

		if(ScoreHudSettings::isTimezoneChanged() && Utils::setTimezone()){
			$this->getLogger()->notice("Server timezone successfully set to " . ScoreHudSettings::getTimezone());
		}else{
			$this->getLogger()->error("Unable to set timezone. Invalid timezone: " . ScoreHudSettings::getTimezone() . ", provided under 'time.zone' in config.yml.");
		}

		$this->getServer()->getPluginManager()->registerEvents(new PlayerSessionHandler(), $this);

		//$this->getServer()->getCommandMap()->register("scorehud", new ScoreHudCommand($this));
		//$this->getScheduler()->scheduleRepeatingTask(new ScoreUpdateTask($this), (int) $this->getConfig()->get("update-interval") * 20);
	}

	private function checkConfigs(): void{
		$this->saveDefaultConfig();

		$this->saveResource("scorehud.yml");
		$this->scoreConfig = new Config($this->getDataFolder() . "scorehud.yml", Config::YAML);

		if(ConfigUpdater::checkUpdate($this, $this->getConfig(), "config-version", self::CONFIG_VERSION)){
			$this->reloadConfig();
		}

		if(ConfigUpdater::checkUpdate($this, $this->scoreConfig, "scorehud-version", self::SCOREHUD_VERSION)){
			$this->scoreConfig = new Config($this->getDataFolder() . "scorehud.yml", Config::YAML);
		}
	}

	private function canLoad(): bool{
		$load = true;

		if(!ScoreHudSettings::isMultiWorld() && empty(ScoreHudSettings::getDefaultBoard())){
			$load = false;
		}

		if(ScoreHudSettings::useDefaultBoard() && empty(ScoreHudSettings::getDefaultBoard())){
			$load = false;
		}

		if(!$load){
			$this->getLogger()->error("Please set the lines under 'default-board' properly, in scorehud.yml.");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}

		return $load;
	}

	public function getScoreConfig(): Config{
		return $this->scoreConfig;
	}

	public function getScoreboards(): ?array{
		return $this->scoreboards;
	}

	public function getScoreboardData(string $world): ?array{
		return !isset($this->scoreboards[$world]) ? null : $this->scoreboards[$world];
	}

	public function getScoreWorlds(): ?array{
		return is_null($this->scoreboards) ? null : array_keys($this->scoreboards);
	}

	public function addScore(Player $player, string $title): void{
		if(!$player->isOnline()){
			return;
		}

		if(isset($this->disabledScoreHudPlayers[strtolower($player->getName())])){
			return;
		}

		ScoreFactory::setScore($player, $title);
		$this->updateScore($player);
	}

	public function updateScore(Player $player): void{
		if($this->getConfig()->get("per-world-scoreboards")){
			if(!$player->isOnline()){
				return;
			}

			$levelName = strtolower($player->getLevel()->getFolderName());

			if(!is_null($lines = $this->getScorelines($levelName))){
				if(empty($lines)){
					$this->getLogger()->error("Please set lines key for $levelName correctly for scoreboards in scorehud.yml.");
					$this->getServer()->getPluginManager()->disablePlugin($this);

					return;
				}

				$i = 0;

				foreach($lines as $line){
					$i++;

					if($i <= 15){
						ScoreFactory::setScoreLine($player, $i, $this->process($player, $line));
					}
				}
			}elseif($this->getConfig()->get("use-default-score-lines")){
				$this->displayDefaultScoreboard($player);
			}else{
				ScoreFactory::removeScore($player);
			}
		}else{
			$this->displayDefaultScoreboard($player);
		}
	}

	public function getScorelines(string $world): ?array{
		return !isset($this->scorelines[$world]) ? null : $this->scorelines[$world];
	}

	public function process(Player $player, string $string): string{
		$tags = [];

		$formattedString = str_replace(
			array_keys($tags),
			array_values($tags),
			$string
		);

		return $formattedString;
	}

	public function displayDefaultScoreboard(Player $player): void{
		$dataConfig = $this->scoreHudConfig;

		$lines = $dataConfig->get("default-board");

		if(empty($lines)){
			$this->getLogger()->error("Please set score-lines in scorehud.yml properly.");
			$this->getServer()->getPluginManager()->disablePlugin($this);

			return;
		}

		$i = 0;

		foreach($lines as $line){
			$i++;

			if($i <= 15){
				ScoreFactory::setScoreLine($player, $i, $this->process($player, $line));
			}
		}
	}
}
