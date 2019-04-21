<?php
declare(strict_types = 1);

/**
 * @name CombatLoggerAddon
 * @main JackMD\ScoreHud\Addons\CombatLoggerAddon
 * @depend CombatLogger
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use jacknoordhuis\combatlogger\CombatLogger;
	use pocketmine\Player;

	class CombatLoggerAddon extends AddonBase{

		/** @var CombatLogger */
		private $combatLogger;

		public function onEnable(): void{
			$this->combatLogger = $this->getServer()->getPluginManager()->getPlugin("CombatLogger");
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{combat_duration}" => $this->combatLogger->getTagDuration($player)
			];
		}
	}
}