<?php
declare(strict_types = 1);

/**
 * @name RankUpAddon
 * @main JackMD\ScoreHud\Addons\RankUpAddon
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;
	use rankup\rank\Rank;
	use rankup\RankUp;

	class RankUpAddon extends AddonBase{

		/**
		 * @param Player $player
		 * @return bool|int|string
		 */
		public function getRankUpRank(Player $player){
			/** @var RankUp $rankUp */
			$rankUp = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("RankUp");

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
			$rankUp = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("RankUp");

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
		 * @param string $string
		 * @return array
		 */
		public function getProcessedTags(Player $player, string $string): array{
			$tags = [];

			if(strpos($string, "{prison_rank}") !== false){
				$tags["{prison_rank}"] = str_replace("{prison_rank}", $this->getRankUpRank($player), $string);
			}

			if(strpos($string, "{prison_next_rank_price}") !== false){
				$tags["{prison_next_rank_price}"] = str_replace("{prison_next_rank_price}", $this->getRankUpRankPrice($player), $string);
			}

			return $tags;
		}
	}
}