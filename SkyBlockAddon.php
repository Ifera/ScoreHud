<?php
declare(strict_types = 1);

/**
 * @name SkyBlockAddon
 * @main JackMD\ScoreHud\Addons\SkyBlockAddon
 */

namespace JackMD\ScoreHud\Addons
{

	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;
	use room17\SkyBlock\session\BaseSession as SkyBlockSession;
	use room17\SkyBlock\SkyBlock;

	class SkyBlockAddon extends AddonBase{

		/**
		 * @param Player $player
		 * @param string $string
		 * @return array
		 */
		public function getProcessedTags(Player $player, string $string): array{
			$tags = [];

			if(strpos($string, "{is_state}") !== false){
				$tags["{is_state}"] = str_replace("{is_state}", $this->getIsleState($player), $string);
			}

			if(strpos($string, "{is_blocks}") !== false){
				$tags["{is_blocks}"] = str_replace("{is_blocks}", $this->getIsleBlocks($player), $string);
			}

			if(strpos($string, "{is_members}") !== false){
				$tags["{is_members}"] = str_replace("{is_members}", $this->getIsleMembers($player), $string);
			}

			if(strpos($string, "{is_size}") !== false){
				$tags["{is_size}"] = str_replace("{is_size}", $this->getIsleSize($player), $string);
			}

			if(strpos($string, "{is_rank}") !== false){
				$tags["{is_rank}"] = str_replace("{is_rank}", $this->getIsleRank($player), $string);
			}

			return $tags;
		}

		/**
		 * @param Player $player
		 * @return string
		 */
		public function getIsleState(Player $player){
			/** @var SkyBlock $sb */
			$sb = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("SkyBlock");

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
		 * @return int|string
		 */
		public function getIsleBlocks(Player $player){
			/** @var SkyBlock $sb */
			$sb = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("SkyBlock");

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
		 * @return int|string
		 */
		public function getIsleMembers(Player $player){
			/** @var SkyBlock $sb */
			$sb = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("SkyBlock");

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
		public function getIsleSize(Player $player){
			/** @var SkyBlock $sb */
			$sb = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("SkyBlock");

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
		 * @return string
		 */
		public function getIsleRank(Player $player){
			/** @var SkyBlock $sb */
			$sb = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("SkyBlock");

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
}