<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\utils;

use pocketmine\Player;

class HelperUtils{

	private static $players = [];

	public static function disable(Player $player): void{
		self::$players[$player->getRawUniqueId()] = $player;
	}

	public static function destroy(Player $player): void{
		unset(self::$players[$player->getRawUniqueId()]);
	}

	public static function isDisabled(Player $player): bool{
		return isset(self::$players[$player->getRawUniqueId()]);
	}
}