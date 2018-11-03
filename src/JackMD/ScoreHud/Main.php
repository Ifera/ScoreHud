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

use _64FF00\PurePerms\PurePerms;
use FactionsPro\FactionMain;
use JackMD\KDR\KDR;
use JackMD\ScoreFactory\ScoreFactory;
use JackMD\ScoreHud\task\ScoreUpdateTask;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use rankup\RankUp;

class Main extends PluginBase{
	
	/** @var string */
	private const CONFIG_VERSION = "PewDiePie";
	
	public function onEnable(): void{
		$this->saveDefaultConfig();
		$this->checkConfig();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getScheduler()->scheduleRepeatingTask(new ScoreUpdateTask($this), (int) $this->getConfig()->get("update-interval") * 20);
		$this->getLogger()->info("ScoreHud Plugin Enabled.");
	}
	
	/**
	 * Check if the config is up-to-date.
	 */
	public function checkConfig(): void{
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		if((!$config->exists("config-version")) || ($config->get("config-version") !== self::CONFIG_VERSION)){
			rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
			$this->saveResource("config.yml");
			$this->getLogger()->critical("Your configuration file is outdated.");
			$this->getLogger()->notice("Your old configuration has been saved as config_old.yml and a new configuration file has been generated.");
			return;
		}
	}
	
	/**
	 * @param Player $player
	 * @param string $title
	 */
	public function addScore(Player $player, string $title): void{
		ScoreFactory::setScore($player, $title);
		$this->updateScore($player);
	}
	
	/**
	 * @param Player $player
	 */
	public function updateScore(Player $player): void{
		$i = 0;
		foreach($this->getConfig()->get("score-lines") as $line){
			$i++;
			if($i <= 15){
				ScoreFactory::setScoreLine($player, $i, $this->process($player, $line));
			}
		}
	}
	
	/**
	 * @param Player $player
	 * @return float|string
	 */
	private function getPlayerMoney(Player $player){
		/** @var EconomyAPI $economyAPI */
		$economyAPI = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		if($economyAPI !== null){
			return $economyAPI->myMoney($player);
		}else{
			return "Plugin not found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return string
	 */
	private function getPlayerRank(Player $player): string{
		/** @var PurePerms $purePerms */
		$purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
		if($purePerms !== null){
			$group = $purePerms->getUserDataMgr()->getData($player)['group'];
			if($group !== null){
				return $group;
			}else{
				return "No Rank";
			}
		}else{
			return "Plugin not found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return bool|int|string
	 */
	private function getPlayerPrisonRank(Player $player){
		/** @var RankUp $rankUp */
		$rankUp = $this->getServer()->getPluginManager()->getPlugin("RankUp");
		if($rankUp !== null){
			$group = $rankUp->getRankUpDoesGroups()->getPlayerGroup($player);
			if($group !== false){
				return $group;
			}else{
				return "No Rank";
			}
		}
		return "Plugin not found";
	}
	
	/**
	 * @param Player $player
	 * @return string
	 */
	public function getPlayerFaction(Player $player): string{
		/** @var FactionMain $factionsPro */
		$factionsPro = $this->getServer()->getPluginManager()->getPlugin("FactionsPro");
		if($factionsPro !== null){
			$factionName = $factionsPro->getPlayerFaction($player->getName());
			if($factionName == null){
				return "No Faction";
			}
			return $factionName;
		}
		return "Plugin not found";
	}
	
	/**
	 * @param Player $player
	 * @return int|string
	 */
	public function getPlayerKills(Player $player){
		/** @var KDR $kdr */
		$kdr = $this->getServer()->getPluginManager()->getPlugin("KDR");
		if($kdr !== null){
			return $kdr->getProvider()->getPlayerKillPoints($player);
		}else{
			return "Plugin Not Found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return int|string
	 */
	public function getPlayerDeaths(Player $player){
		/** @var KDR $kdr */
		$kdr = $this->getServer()->getPluginManager()->getPlugin("KDR");
		if($kdr !== null){
			return $kdr->getProvider()->getPlayerDeathPoints($player);
		}else{
			return "Plugin Not Found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return string
	 */
	public function getPlayerKillToDeathRatio(Player $player): string{
		/** @var KDR $kdr */
		$kdr = $this->getServer()->getPluginManager()->getPlugin("KDR");
		if($kdr !== null){
			return $kdr->getProvider()->getKillToDeathRatio($player);
		}else{
			return "Plugin Not Found";
		}
	}
	
	/**
	 * @param Player $player
	 * @param string $string
	 * @return string
	 */
	public function process(Player $player, string $string): string{
		$string = str_replace("{name}", $player->getName(), $string);
		$string = str_replace("{money}", $this->getPlayerMoney($player), $string);
		$string = str_replace("{online}", count($this->getServer()->getOnlinePlayers()), $string);
		$string = str_replace("{max_online}", $this->getServer()->getMaxPlayers(), $string);
		$string = str_replace("{rank}", $this->getPlayerRank($player), $string);
		$string = str_replace("{prison_rank}", $this->getPlayerPrisonRank($player), $string);
		$string = str_replace("{item_name}", $player->getInventory()->getItemInHand()->getName(), $string);
		$string = str_replace("{item_id}", $player->getInventory()->getItemInHand()->getId(), $string);
		$string = str_replace("{item_meta}", $player->getInventory()->getItemInHand()->getDamage(), $string);
		$string = str_replace("{item_count}", $player->getInventory()->getItemInHand()->getCount(), $string);
		$string = str_replace("{x}", intval($player->getX()), $string);
		$string = str_replace("{y}", intval($player->getY()), $string);
		$string = str_replace("{z}", intval($player->getZ()), $string);
		$string = str_replace("{faction}", $this->getPlayerFaction($player), $string);
		$string = str_replace("{load}", $this->getServer()->getTickUsage(), $string);
		$string = str_replace("{tps}", $this->getServer()->getTicksPerSecond(), $string);
		$string = str_replace("{level_name}", $player->getLevel()->getName(), $string);
		$string = str_replace("{level_folder_name}", $player->getLevel()->getFolderName(), $string);
		$string = str_replace("{ip}", $player->getAddress(), $string);
		$string = str_replace("{ping}", $player->getPing(), $string);
		$string = str_replace("{kills}", $this->getPlayerKills($player), $string);
		$string = str_replace("{deaths}", $this->getPlayerDeaths($player), $string);
		$string = str_replace("{kdr}", $this->getPlayerKillToDeathRatio($player), $string);
		return $string;
	}
}
