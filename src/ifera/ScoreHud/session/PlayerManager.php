<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\session;

use pocketmine\Player;

class PlayerManager{

	/** @var PlayerSession[] */
	private static $sessions = [];

	public static function create(Player $player) : void{
		self::$sessions[$player->getRawUniqueId()] = new PlayerSession($player);
	}

	public static function destroy(Player $player) : void{
		self::$sessions[$uuid = $player->getRawUniqueId()]->close();
		unset(self::$sessions[$uuid]);
	}

	public static function get(Player $player) : ?PlayerSession{
		return self::$sessions[$player->getRawUniqueId()] ?? null;
	}
}