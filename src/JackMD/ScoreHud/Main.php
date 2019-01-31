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

use JackMD\ScoreFactory\ScoreFactory;
use JackMD\ScoreHud\commands\ScoreHudCommand;
use JackMD\ScoreHud\data\DataManager;
use JackMD\ScoreHud\task\ScoreUpdateTask;
use JackMD\UpdateNotifier\UpdateNotifier;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase{

	/** @var string */
	public const PREFIX = "§8[§6S§eH§8]§r ";

	/** @var string */
	private const CONFIG_VERSION = 6;

	/** @var string */
	private const DATA_CONFIG_VERSION = 2;
	/** @var array */
	public $disabledScoreHudPlayers = [];
	/** @var DataManager */
	private $dataManager;
	/** @var null|array */
	private $scoreboards = [];
	/** @var null|array */
	private $scorelines = [];

	public function onLoad(){
		$this->checkVirions();
		$this->initScoreboards();

		UpdateNotifier::checkUpdate($this, $this->getDescription()->getName(), $this->getDescription()->getVersion());
	}

	/**
	 * Checks if the required virions/libraries are present before enabling the plugin.
	 */
	private function checkVirions(): void{
		if(!class_exists(ScoreFactory::class) || !class_exists(UpdateNotifier::class)){
			throw new \RuntimeException("ScoreHud plugin will only work if you use the plugin phar from Poggit.");
		}
	}

	private function initScoreboards(): void{
		$this->saveDefaultConfig();
		$this->saveResource("data.yml");
		$this->checkConfigs();

		$dataConfig = new Config($this->getDataFolder() . "data.yml", Config::YAML);
		foreach($dataConfig->getNested("scoreboards") as $world => $data){
			$world = strtolower($world);
			$this->scoreboards[$world] = $data;
			$this->scorelines[$world] = $data["lines"];
		}
	}

	/**
	 * Check if the configs is up-to-date.
	 */
	public function checkConfigs(): void{
		if((!$this->getConfig()->exists("config-version")) || ($this->getConfig()->get("config-version") !== self::CONFIG_VERSION)){
			rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
			$this->saveResource("config.yml");
			$this->getLogger()->critical("Your configuration file is outdated.");
			$this->getLogger()->notice("Your old configuration has been saved as config_old.yml and a new configuration file has been generated. Please update accordingly.");
		}

		$dataConfig = new Config($this->getDataFolder() . "data.yml", Config::YAML);
		if((!$dataConfig->exists("data-version")) || ($dataConfig->get("data-version") !== self::DATA_CONFIG_VERSION)){
			rename($this->getDataFolder() . "data.yml", $this->getDataFolder() . "data_old.yml");
			$this->saveResource("data.yml");
			$this->getLogger()->critical("Your data.yml file is outdated.");
			$this->getLogger()->notice("Your old data.yml has been saved as data_old.yml and a new data.yml file has been generated. Please update accordingly.");
		}
	}

	public function onEnable(): void{
		$this->dataManager = new DataManager($this);

		$this->getServer()->getCommandMap()->register("scorehud", new ScoreHudCommand($this));
		$this->setTimezone($this->getConfig()->get("timezone"));
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getScheduler()->scheduleRepeatingTask(new ScoreUpdateTask($this), (int) $this->getConfig()->get("update-interval") * 20);
		$this->getLogger()->info("ScoreHud Plugin Enabled.");
	}

	/**
	 * @param $timezone
	 * @return mixed
	 */
	private function setTimezone($timezone){
		if($timezone !== false){
			$this->getLogger()->notice("Server timezone successfully set to " . $timezone);

			return @date_default_timezone_set($timezone);
		}

		return false;
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
		$dataConfig = new Config($this->getDataFolder() . "data.yml", Config::YAML);

		if($this->getConfig()->get("per-world-scoreboards")){
			if(!$player->isOnline()){
				return;
			}
			$levelName = strtolower($player->getLevel()->getFolderName());
			if(!is_null($lines = $this->getScorelines($levelName))){
				if(empty($lines)){
					$this->getLogger()->error("Please set lines key for $levelName correctly for scoreboards in data.yml.");
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
			}else{
				ScoreFactory::removeScore($player);
			}
		}else{
			$lines = $dataConfig->get("score-lines");
			if(empty($lines)){
				$this->getLogger()->error("Please set score-lines in data.yml properly.");
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

	public function getScorelines(string $world): ?array{
		return !isset($this->scorelines[$world]) ? null : $this->scorelines[$world];
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

	/**
	 * @param Player $player
	 * @param string $string
	 * @return string
	 */
	public function process(Player $player, string $string): string{
		$string = str_replace("{name}", $player->getName(), $string);
		$string = str_replace("{money}", $this->dataManager->getPlayerMoney($player), $string);
		$string = str_replace("{online}", count($this->getServer()->getOnlinePlayers()), $string);
		$string = str_replace("{max_online}", $this->getServer()->getMaxPlayers(), $string);
		$string = str_replace("{rank}", $this->dataManager->getPlayerRank($player), $string);
		$string = str_replace("{prison_rank}", $this->dataManager->getRankUpRank($player), $string);
		$string = str_replace("{prison_next_rank_price}", $this->dataManager->getRankUpRankPrice($player), $string);
		$string = str_replace("{item_name}", $player->getInventory()->getItemInHand()->getName(), $string);
		$string = str_replace("{item_id}", $player->getInventory()->getItemInHand()->getId(), $string);
		$string = str_replace("{item_meta}", $player->getInventory()->getItemInHand()->getDamage(), $string);
		$string = str_replace("{item_count}", $player->getInventory()->getItemInHand()->getCount(), $string);
		$string = str_replace("{x}", intval($player->getX()), $string);
		$string = str_replace("{y}", intval($player->getY()), $string);
		$string = str_replace("{z}", intval($player->getZ()), $string);
		$string = str_replace("{faction}", $this->dataManager->getPlayerFaction($player), $string);
		$string = str_replace("{faction_power}", $this->dataManager->getFactionPower($player), $string);
		$string = str_replace("{load}", $this->getServer()->getTickUsage(), $string);
		$string = str_replace("{tps}", $this->getServer()->getTicksPerSecond(), $string);
		$string = str_replace("{level_name}", $player->getLevel()->getName(), $string);
		$string = str_replace("{level_folder_name}", $player->getLevel()->getFolderName(), $string);
		$string = str_replace("{ip}", $player->getAddress(), $string);
		$string = str_replace("{ping}", $player->getPing(), $string);
		$string = str_replace("{kills}", $this->dataManager->getPlayerKills($player), $string);
		$string = str_replace("{deaths}", $this->dataManager->getPlayerDeaths($player), $string);
		$string = str_replace("{kdr}", $this->dataManager->getPlayerKillToDeathRatio($player), $string);
		$string = str_replace("{prefix}", $this->dataManager->getPrefix($player), $string);
		$string = str_replace("{suffix}", $this->dataManager->getSuffix($player), $string);
		$string = str_replace("{time}", date($this->getConfig()->get("time-format")), $string);
		$string = str_replace("{date}", date($this->getConfig()->get("date-format")), $string);
		$string = str_replace("{cps}", $this->dataManager->getClicks($player), $string);
		$string = str_replace("{is_state}", $this->dataManager->getIsleState($player), $string);
		$string = str_replace("{is_blocks}", $this->dataManager->getIsleBlocks($player), $string);
		$string = str_replace("{is_members}", $this->dataManager->getIsleMembers($player), $string);
		$string = str_replace("{is_size}", $this->dataManager->getIsleSize($player), $string);
		$string = str_replace("{is_rank}", $this->dataManager->getIsleRank($player), $string);

		return $string;
	}
}