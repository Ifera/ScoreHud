<?php
declare(strict_types = 1);

/**
 * @name CPSAddon
 * @main JackMD\ScoreHud\Addons\CPSAddon
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use JackMD\CPS\CPS;
	use pocketmine\Player;

	class CPSAddon extends AddonBase{

		/**
		 * @param Player $player
		 * @return int|string
		 */
		public function getClicks(Player $player){
			/** @var CPS $cps */
			$cps = $this->getScoreHud()->getServer()->getPluginManager()->getPlugin("CPS");
			
			if($cps instanceof CPS){
				return $cps->getClicks($player);
			}else{
				return "Plugin Not Found";
			}
		}

		/**
		 * @param Player $player
		 * @param string $string
		 * @return array
		 */
		public function getProcessedTags(Player $player, string $string): array{
			$tags = [];

			if(strpos($string, "{cps}") !== false){
				$tags["{cps}"] = str_replace("{cps}", $this->getClicks($player), $string);
			}

			return $tags;
		}
	}
}