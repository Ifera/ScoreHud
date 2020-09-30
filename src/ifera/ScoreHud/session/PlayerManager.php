<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\session;

use pocketmine\Player;

class PlayerManager{

	/** @var PlayerSession[] */
	private static $sessions = [];

	public static function create(Player $player) : void{
		self::$sessions[$player->getRawUniqueId()] = $session = new PlayerSession($player);
		$session->initialize();
	}

	public static function destroy(Player $player) : void{
		if(!isset(self::$sessions[$uuid = $player->getRawUniqueId()])){
			return;
		}

		self::$sessions[$uuid]->close();
		unset(self::$sessions[$uuid]);
	}

	public static function get(Player $player) : ?PlayerSession{
		return self::$sessions[$player->getRawUniqueId()] ?? null;
	}

	public static function getNonNull(Player $player) : PlayerSession{
		return self::$sessions[$player->getRawUniqueId()];
	}
}