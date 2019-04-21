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
		 * @return string
		 */
		public function getProcessedString(Player $player, string $string): string{
			$data = [
				"{money}" => str_replace("{money}", $this->getPlayerMoney($player), $string)
			];

			if(strpos($string, "{money}") !== false){
				return $data["{money}"];
			}

			return $string;
		}
	}
}