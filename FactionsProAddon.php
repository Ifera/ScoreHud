<?php
declare(strict_types = 1);

/**
 * @name FactionsProAddon
 * @main JackMD\ScoreHud\Addons\FactionsProAddon
 * @depend FactionsPro
 */
namespace JackMD\ScoreHud\Addons
{

	use JackMD\ScoreHud\addon\AddonBase;
	use FactionsPro\FactionMain;
	use pocketmine\Player;

	class FactionsProAddon extends AddonBase{

		/** @var FactionMain */
		private $factionsPro;

		public function onEnable(): void{
			$this->factionsPro = $this->getServer()->getPluginManager()->getPlugin("FactionsPro");
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{faction}"       => $this->getPlayerFaction($player),
				"{faction_power}" => $this->getFactionPower($player)
			];
		}

		/**
		 * @param Player $player
		 * @return string
		 */
		public function getPlayerFaction(Player $player): string{
			$factionsPro = $this->factionsPro;
			$factionName = $factionsPro->getPlayerFaction($player->getName());

			if($factionName === null){
				return "No Faction";
			}

			return $factionName;
		}

		/**
		 * @param Player $player
		 * @return string
		 */
		public function getFactionPower(Player $player){
			$factionsPro = $this->factionsPro;
			$factionName = $factionsPro->getPlayerFaction($player->getName());

			if($factionName === null){
				return "No Faction";
			}

			return $factionsPro->getFactionPower($factionName);
		}
	}
}