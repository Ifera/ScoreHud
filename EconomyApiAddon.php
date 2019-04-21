<?php
declare(strict_types = 1);

/**
 * @name EconomyApiAddon
 * @main JackMD\ScoreHud\Addons\EconomyApiAddon
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use onebone\economyapi\EconomyAPI;
	use pocketmine\Player;

	class EconomyApiAddon extends AddonBase{

		/**
		 * @param Player $player
		 * @return float|string
		 */
		private function getPlayerMoney(Player $player){
			/** @var EconomyAPI $economyAPI */
			$economyAPI = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("EconomyAPI");

			if($economyAPI instanceof EconomyAPI){
				return $economyAPI->myMoney($player);
			}else{
				return "Plugin not found";
			}
		}

		/**
		 * @param Player $player
		 * @param string $string
		 * @return array
		 */
		public function getProcessedTags(Player $player, string $string): array{
			$tags = [];

			if(strpos($string, "{money}") !== false){
				$tags["{money}"] = str_replace("{money}", $this->getPlayerMoney($player), $string);
			}

			return $tags;
		}
	}
}