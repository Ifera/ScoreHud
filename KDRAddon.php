<?php
declare(strict_types = 1);

/**
 * @name KDRAddon
 * @main JackMD\ScoreHud\Addons\KDRAddon
 */
namespace JackMD\ScoreHud\Addons
{

	use JackMD\KDR\KDR;
	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;

	class KDRAddon extends AddonBase{

		/**
		 * @param Player $player
		 * @return int|string
		 */
		public function getPlayerKills(Player $player){
			/** @var KDR $kdr */
			$kdr = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("KDR");

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
			$kdr = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("KDR");

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
			$kdr = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("KDR");

			if($kdr instanceof KDR){
				return $kdr->getProvider()->getKillToDeathRatio($player);
			}else{
				return "Plugin Not Found";
			}
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{kdr}" => $this->getPlayerKillToDeathRatio($player),
				"{deaths}" => $this->getPlayerDeaths($player),
				"{kills}" => $this->getPlayerKills($player)
			];
		}
	}
}