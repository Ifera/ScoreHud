<?php
declare(strict_types = 1);

/**
 * @name FactionsProAddon
 * @main JackMD\ScoreHud\Addons\FactionsProAddon
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use FactionsPro\FactionMain;
	use pocketmine\Player;

	class FactionsProAddon extends AddonBase{

		/**
		 * @param Player $player
		 * @return string
		 */
		public function getPlayerFaction(Player $player): string{
			/** @var FactionMain $factionsPro */
			$factionsPro = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("FactionsPro");
			
			if($factionsPro instanceof FactionMain){
				$factionName = $factionsPro->getPlayerFaction($player->getName());
				
				if($factionName === null){
					return "No Faction";
				}
				
				return $factionName;
			}
			
			return "Plugin not found";
		}

		/**
		 * @param Player $player
		 * @return string
		 */
		public function getFactionPower(Player $player){
			/** @var FactionMain $factionsPro */
			$factionsPro = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("FactionsPro");
			
			if($factionsPro instanceof FactionMain){
				$factionName = $factionsPro->getPlayerFaction($player->getName());
				
				if($factionName === null){
					return "No Faction";
				}
				return $factionsPro->getFactionPower($factionName);
			}
			
			return "Plugin not found";
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{faction}" => $this->getPlayerFaction($player),
				"{faction_power}" => $this->getFactionPower($player)
			];
		}
	}
}