<?php
declare(strict_types = 1);

/**
 *     _____                    _   _           _
 *    /  ___|                  | | | |         | |
 *    \ `--.  ___ ___  _ __ ___| |_| |_   _  __| |
 *     `--. \/ __/ _ \| '__/ _ \  _  | | | |/ _` |
 *    /\__/ / (_| (_) | | |  __/ | | | |_| | (_| |
 *    \____/ \___\___/|_|  \___\_| |_/\__,_|\__,_|
 *
 * ScoreHud, a Scoreboard plugin for PocketMine-MP
 * Copyright (c) 2020 Ifera  < https://github.com/Ifera >
 *
 * Discord: Ifera#3717
 * Twitter: ifera_tr
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * ScoreHud is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace Ifera\ScoreHud\session;

use pocketmine\player\Player;

class PlayerManager{

	/** @var PlayerSession[] */
	private static $sessions = [];

	public static function create(Player $player): void{
		self::$sessions[$player->getUniqueId()->toString()] = $session = new PlayerSession($player);
		$session->handle();
	}

	public static function destroy(Player $player): void{
		if(!$player->isOnline()){
			return;
		}

		if(!isset(self::$sessions[$uuid = $player->getUniqueId()->toString()])){
			return;
		}

		self::$sessions[$uuid]->close();
		unset(self::$sessions[$uuid]);
	}

	public static function get(Player $player): ?PlayerSession{
		return self::$sessions[$player->getUniqueId()->toString()] ?? null;
	}

	public static function getNonNull(Player $player): PlayerSession{
		return self::$sessions[$player->getUniqueId()->toString()];
	}

	/**
	 * @return PlayerSession[]
	 */
	public static function getAll(): array{
		return self::$sessions;
	}

	public static function destroyAll(): void{
		foreach(self::$sessions as $session){
			self::destroy($session->getPlayer());
		}
	}
}