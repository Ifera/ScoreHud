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

		public function initiate(): void{
		}

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

		public function getProcessedData(Player $player, string $string): string{
			return str_replace("{money}", $this->getPlayerMoney($player), $string);
		}
	}
}