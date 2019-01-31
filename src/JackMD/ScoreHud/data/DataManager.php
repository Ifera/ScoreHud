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

namespace JackMD\ScoreHud\data;

use _64FF00\PurePerms\PurePerms;
use FactionsPro\FactionMain;
use JackMD\CPS\CPS;
use JackMD\KDR\KDR;
use JackMD\ScoreHud\Main;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use rankup\rank\Rank;
use rankup\RankUp;
use room17\SkyBlock\session\iSession as SkyBlockSession;
use room17\SkyBlock\SkyBlock;

class DataManager{
	
	/** @var Main */
	private $plugin;
	
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}
	
	/**
	 * @param Player $player
	 * @return float|string
	 */
	public function getPlayerMoney(Player $player){
		/** @var EconomyAPI $economyAPI */
		$economyAPI = $this->plugin->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		if($economyAPI instanceof EconomyAPI){
			return $economyAPI->myMoney($player);
		}else{
			return "Plugin not found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return string
	 */
	public function getPlayerRank(Player $player): string{
		/** @var PurePerms $purePerms */
		$purePerms = $this->plugin->getServer()->getPluginManager()->getPlugin("PurePerms");
		if($purePerms instanceof PurePerms){
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
	public function getRankUpRank(Player $player){
		/** @var RankUp $rankUp */
		$rankUp = $this->plugin->getServer()->getPluginManager()->getPlugin("RankUp");
		if($rankUp instanceof RankUp){
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
	 * @return bool|Rank|string
	 */
	public function getRankUpRankPrice(Player $player){
		/** @var RankUp $rankUp */
		$rankUp = $this->plugin->getServer()->getPluginManager()->getPlugin("RankUp");
		if($rankUp instanceof RankUp){
			$nextRank = $rankUp->getRankStore()->getNextRank($player);
			if($nextRank !== false){
				return $nextRank->getPrice();
			}else{
				return "Max Rank";
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
		$factionsPro = $this->plugin->getServer()->getPluginManager()->getPlugin("FactionsPro");
		if($factionsPro instanceof FactionMain){
			$factionName = $factionsPro->getPlayerFaction($player->getName());
			if($factionName === null){
				return "No Faction";
			}
			return $factionName;
		}
		return "Plugin not found";
	}
	
	/**
	 * @param Player $player
	 * @return string
	 */
	public function getFactionPower(Player $player){
		/** @var FactionMain $factionsPro */
		$factionsPro = $this->plugin->getServer()->getPluginManager()->getPlugin("FactionsPro");
		if($factionsPro instanceof FactionMain){
			$factionName = $factionsPro->getPlayerFaction($player->getName());
			if($factionName === null){
				return "No Faction";
			}
			return $factionsPro->getFactionPower($factionName);
		}
		return "Plugin not found";
	}
	
	/**
	 * @param Player $player
	 * @return int|string
	 */
	public function getPlayerKills(Player $player){
		/** @var KDR $kdr */
		$kdr = $this->plugin->getServer()->getPluginManager()->getPlugin("KDR");
		if($kdr instanceof KDR){
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
		$kdr = $this->plugin->getServer()->getPluginManager()->getPlugin("KDR");
		if($kdr instanceof KDR){
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
		$kdr = $this->plugin->getServer()->getPluginManager()->getPlugin("KDR");
		if($kdr instanceof KDR){
			return $kdr->getProvider()->getKillToDeathRatio($player);
		}else{
			return "Plugin Not Found";
		}
	}
	
	/**
	 * @param Player $player
	 * @param null   $levelName
	 * @return string
	 */
	public function getPrefix(Player $player, $levelName = null): string{
		/** @var PurePerms $purePerms */
		$purePerms = $this->plugin->getServer()->getPluginManager()->getPlugin("PurePerms");
		if($purePerms instanceof PurePerms){
			$prefix = $purePerms->getUserDataMgr()->getNode($player, "prefix");
			if($levelName === null){
				if(($prefix === null) || ($prefix === "")){
					return "No Prefix";
				}
				return (string) $prefix;
			}else{
				$worldData = $purePerms->getUserDataMgr()->getWorldData($player, $levelName);
				if(empty($worldData["prefix"]) || $worldData["prefix"] == null){
					return "No Prefix";
				}
				return $worldData["prefix"];
			}
		}else{
			return "Plugin not found";
		}
	}
	
	/**
	 * @param Player $player
	 * @param null   $levelName
	 * @return string
	 */
	public function getSuffix(Player $player, $levelName = null): string{
		/** @var PurePerms $purePerms */
		$purePerms = $this->plugin->getServer()->getPluginManager()->getPlugin("PurePerms");
		if($purePerms instanceof PurePerms){
			$suffix = $purePerms->getUserDataMgr()->getNode($player, "suffix");
			if($levelName === null){
				if(($suffix === null) || ($suffix === "")){
					return "No Suffix";
				}
				return (string) $suffix;
			}else{
				$worldData = $purePerms->getUserDataMgr()->getWorldData($player, $levelName);
				if(empty($worldData["suffix"]) || $worldData["suffix"] == null){
					return "No Suffix";
				}
				return $worldData["suffix"];
			}
		}else{
			return "Plugin not found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return int|string
	 */
	public function getClicks(Player $player){
		/** @var CPS $cps */
		$cps = $this->plugin->getServer()->getPluginManager()->getPlugin("CPS");
		if($cps instanceof CPS){
			return $cps->getClicks($player);
		}else{
			return "Plugin Not Found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return int|string
	 */
	public function getIsleBlocks(Player $player){
		/** @var SkyBlock $sb */
		$sb = $this->plugin->getServer()->getPluginManager()->getPlugin("SkyBlock");
		if($sb instanceof SkyBlock){
			$session = $sb->getSessionManager()->getSession($player);
			if((is_null($session)) || (!$session->hasIsle())){
				return "No Island";
			}
			$isle = $session->getIsle();
			return $isle->getBlocksBuilt();
		}else{
			return "Plugin Not Found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return string
	 */
	public function getIsleSize(Player $player){
		/** @var SkyBlock $sb */
		$sb = $this->plugin->getServer()->getPluginManager()->getPlugin("SkyBlock");
		if($sb instanceof SkyBlock){
			$session = $sb->getSessionManager()->getSession($player);
			if((is_null($session)) || (!$session->hasIsle())){
				return "No Island";
			}
			$isle = $session->getIsle();
			return $isle->getCategory();
		}else{
			return "Plugin Not Found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return int|string
	 */
	public function getIsleMembers(Player $player){
		/** @var SkyBlock $sb */
		$sb = $this->plugin->getServer()->getPluginManager()->getPlugin("SkyBlock");
		if($sb instanceof SkyBlock){
			$session = $sb->getSessionManager()->getSession($player);
			if((is_null($session)) || (!$session->hasIsle())){
				return "No Island";
			}
			$isle = $session->getIsle();
			return count($isle->getMembers());
		}else{
			return "Plugin Not Found";
		}
	}
	
	/**
	 * @param Player $player
	 * @return string
	 */
	public function getIsleState(Player $player){
		/** @var SkyBlock $sb */
		$sb = $this->plugin->getServer()->getPluginManager()->getPlugin("SkyBlock");
		if($sb instanceof SkyBlock){
			$session = $sb->getSessionManager()->getSession($player);
			if((is_null($session)) || (!$session->hasIsle())){
				return "No Island";
			}
			$isle = $session->getIsle();
			return $isle->isLocked() ? "Locked" : "Unlocked";
		}else{
			return "Plugin Not Found";
		}
	}

	/**
	 * @param Player $player
	 * @return string
	 */
	public function getIsleRank(Player $player){
		/** @var SkyBlock $sb */
		$sb = $this->plugin->getServer()->getPluginManager()->getPlugin("SkyBlock");
		if($sb instanceof SkyBlock){
			$session = $sb->getSessionManager()->getSession($player);
			if((is_null($session)) || (!$session->hasIsle())){
				return "No Island";
			}
			switch($session->getRank()){
				case SkyBlockSession::RANK_DEFAULT:
					return "Member";
				case SkyBlockSession::RANK_OFFICER:
					return "Officer";
				case SkyBlockSession::RANK_LEADER:
					return "Leader";
				case SkyBlockSession::RANK_FOUNDER:
					return "Founder";
			}
			return "No Rank";
		}else{
			return "Plugin Not Found";
		}
	}
}
