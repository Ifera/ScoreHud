<?php
declare(strict_types = 1);

/**
 * @name CombatLoggerAddon
 * @main JackMD\ScoreHud\Addons\CombatLoggerAddon
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use jacknoordhuis\combatlogger\CombatLogger;
	use pocketmine\Player;

	class CombatLoggerAddon extends AddonBase{

		/**
		 * @param Player $player
		 * @return int|string
		 */
		public function getTagDuration(Player $player){
			/** @var CombatLogger $cl */
			$cl = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("CombatLogger");
			
			if($cl instanceof CombatLogger){
				return $cl->getTagDuration($player);
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
				"{combat_duration}" => $this->getTagDuration($player)
			];
		}
	}
}