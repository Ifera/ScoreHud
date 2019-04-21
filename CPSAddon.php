<?php
declare(strict_types = 1);

/**
 * @name CPSAddon
 * @main JackMD\ScoreHud\Addons\CPSAddon
 * @depend CPS
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use JackMD\CPS\CPS;
	use pocketmine\Player;

	class CPSAddon extends AddonBase{

		/** @var CPS */
		private $cps;

		public function onEnable(): void{
			$this->cps = $this->getServer()->getPluginManager()->getPlugin("CPS");
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{cps}" => $this->cps->getClicks($player)
			];
		}
	}
}