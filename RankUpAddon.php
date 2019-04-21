<?php
declare(strict_types = 1);

/**
 * @name RankUpAddon
 * @main JackMD\ScoreHud\Addons\RankUpAddon
 * @depend RankUp
 */
namespace JackMD\ScoreHud\Addons
{

	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;
	use rankup\rank\Rank;
	use rankup\RankUp;

	class RankUpAddon extends AddonBase{

		/** @var RankUp */
		private $rankUp;

		public function onEnable(): void{
			$this->rankUp = $this->getServer()->getPluginManager()->getPlugin("RankUp");
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{prison_rank}"            => $this->getRankUpRank($player),
				"{prison_next_rank_price}" => $this->getRankUpRankPrice($player)
			];
		}

		/**
		 * @param Player $player
		 * @return bool|int|string
		 */
		public function getRankUpRank(Player $player){
			$group = $this->rankUp->getRankUpDoesGroups()->getPlayerGroup($player);

			if($group !== false){
				return $group;
			}else{
				return "No Rank";
			}
		}

		/**
		 * @param Player $player
		 * @return bool|Rank|string
		 */
		public function getRankUpRankPrice(Player $player){
			$nextRank = $this->rankUp->getRankStore()->getNextRank($player);

			if($nextRank !== false){
				return $nextRank->getPrice();
			}else{
				return "Max Rank";
			}
		}
	}
}