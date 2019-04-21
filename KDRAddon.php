<?php
declare(strict_types = 1);

/**
 * @name KDRAddon
 * @main JackMD\ScoreHud\Addons\KDRAddon
 * @depend KDR
 */
namespace JackMD\ScoreHud\Addons
{

	use JackMD\KDR\KDR;
	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;

	class KDRAddon extends AddonBase{

		/** @var KDR */
		private $kdr;

		public function onEnable(): void{
			$this->kdr = $this->getServer()->getPluginManager()->getPlugin("KDR");
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{kdr}" => $this->kdr->getProvider()->getKillToDeathRatio($player),
				"{deaths}" => $this->kdr->getProvider()->getPlayerDeathPoints($player),
				"{kills}" => $this->kdr->getProvider()->getPlayerKillPoints($player)
			];
		}
	}
}