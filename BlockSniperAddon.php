<?php

declare(strict_types=1);

/**
 * @name BlockSniperAddon
 * @main BlockHorizons\ScoreHud\Addons\BlockSniperAddon
 * @depend BlockSniper
 */
namespace BlockHorizons\ScoreHud\Addons {

	use BlockHorizons\BlockSniper\brush\Brush;
	use BlockHorizons\BlockSniper\sessions\SessionManager;
	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;

	class BlockSniperAddon extends AddonBase{

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			$brush = SessionManager::getPlayerSession($player)->getBrush();
			return [
				"{brush_shape}" => $brush->getShape()->getName(),
				"{brush_type}" => $brush->getType()->getName(),
				"{brush_mode}" => $brush->mode === Brush::MODE_BRUSH ? "Brush" : "Selection",
				"{brush_size}" => $brush->size,
			];
		}
	}
}
