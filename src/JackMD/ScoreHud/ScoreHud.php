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

namespace JackMD\ScoreHud;

use JackMD\ConfigUpdater\ConfigUpdater;
use JackMD\ScoreFactory\ScoreFactory;
use JackMD\ScoreHud\addon\AddonManager;
use JackMD\ScoreHud\commands\ScoreHudCommand;
use JackMD\ScoreHud\task\ScoreUpdateTask;
use JackMD\ScoreHud\updater\AddonUpdater;
use JackMD\ScoreHud\utils\Utils;
use JackMD\UpdateNotifier\UpdateNotifier;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class ScoreHud extends PluginBase{

	/** @var string */
	public const PREFIX = "§8[§6S§eH§8]§r ";

	/** @var int */
	private const CONFIG_VERSION = 8;
	/** @var int */
	private const SCOREHUD_VERSION = 1;

	/** @var string */
	public static $addonPath = "";
	/** @var ScoreHud|null */
	private static $instance = null;

	/** @var AddonUpdater */
	private $addonUpdater;
	/** @var AddonManager */
	private $addonManager;

	/** @var array */
	public $disabledScoreHudPlayers = [];
	/** @var Config */
	private $scoreHudConfig;
	/** @var null|array */
	private $scoreboards = [];
	/** @var null|array */
	private $scorelines = [];

	/**
	 * @return ScoreHud|null
	 */
	public static function getInstance(): ?ScoreHud{
		return self::$instance;
	}

	public function onLoad(){
		self::$instance = $this;
		self::$addonPath = realpath($this->getDataFolder() . "addons") . DIRECTORY_SEPARATOR;

		UpdateNotifier::checkUpdate($this, $this->getDescription()->getName(), $this->getDescription()->getVersion());
		Utils::checkVirions();

		$this->checkConfigs();
		$this->initScoreboards();
	}

	/**
	 * Check if the configs is up-to-date.
	 */
	private function checkConfigs(): void{
		$this->saveDefaultConfig();

		$this->saveResource("addons" . DIRECTORY_SEPARATOR . "README.txt");
		$this->saveResource("scorehud.yml");
		$this->scoreHudConfig = new Config($this->getDataFolder() . "scorehud.yml", Config::YAML);

		ConfigUpdater::checkUpdate($this, $this->getConfig(), "config-version", self::CONFIG_VERSION);
		ConfigUpdater::checkUpdate($this, $this->scoreHudConfig, "scorehud-version", self::SCOREHUD_VERSION);
	}

	private function initScoreboards(): void{
		foreach($this->scoreHudConfig->getNested("scoreboards") as $world => $data){
			$world = strtolower($world);

			$this->scoreboards[$world] = $data;
			$this->scorelines[$world] = $data["lines"];
		}
	}

	public function onEnable(){
		$this->addonUpdater = new AddonUpdater($this);
		$this->addonManager = new AddonManager($this);

		$this->getServer()->getCommandMap()->register("scorehud", new ScoreHudCommand($this));
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

		$this->getScheduler()->scheduleRepeatingTask(new ScoreUpdateTask($this), (int) $this->getConfig()->get("update-interval") * 20);
	}

	/**
	 * @return Config
	 */
	public function getScoreHudConfig(): Config{
		return $this->scoreHudConfig;
	}

	/**
	 * @return array|null
	 */
	public function getScoreboards(): ?array{
		return $this->scoreboards;
	}

	/**
	 * @param string $world
	 * @return array|null
	 */
	public function getScoreboardData(string $world): ?array{
		return !isset($this->scoreboards[$world]) ? null : $this->scoreboards[$world];
	}

	/**
	 * @return array|null
	 */
	public function getScoreWorlds(): ?array{
		return is_null($this->scoreboards) ? null : array_keys($this->scoreboards);
	}

	/**
	 * @param Player $player
	 * @param string $title
	 */
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

	/**
	 * @param Player $player
	 */
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

	/**
	 * @param string $world
	 * @return array|null
	 */
	public function getScorelines(string $world): ?array{
		return !isset($this->scorelines[$world]) ? null : $this->scorelines[$world];
	}

	/**
	 * @param Player $player
	 * @param string $string
	 * @return string
	 */
	public function process(Player $player, string $string): string{
		$tags = [];

		foreach($this->addonManager->getAddons() as $addon){
			foreach($addon->getProcessedTags($player) as $identifier => $processedTag){
				$tags[$identifier] = $processedTag;
			}
		}

		$formattedString = str_replace(
			array_keys($tags),
			array_values($tags),
			$string
		);

		return $formattedString;
	}

	/**
	 * @param Player $player
	 */
	public function displayDefaultScoreboard(Player $player): void{
		$dataConfig = $this->scoreHudConfig;

		$lines = $dataConfig->get("score-lines");

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

	/**
	 * @return AddonUpdater
	 */
	public function getAddonUpdater(): AddonUpdater{
		return $this->addonUpdater;
	}

	/**
	 * @return AddonManager
	 */
	public function getAddonManager(): AddonManager{
		return $this->addonManager;
	}
}
