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
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{is_state}" => $this->getIsleState($player),
				"{is_blocks}" => $this->getIsleBlocks($player),
				"{is_members}" => $this->getIsleMembers($player),
				"{is_size}" => $this->getIsleSize($player),
				"{is_rank}" => $this->getIsleRank($player)
			];
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