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
 * Twitter: JackMTaylor_
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

namespace Ifera\ScoreHud\utils;


use JackMD\ConfigUpdater\ConfigUpdater;
use JackMD\ScoreFactory\ScoreFactory;
use Ifera\ScoreHud\ScoreHud;
use JackMD\UpdateNotifier\UpdateNotifier;
use pocketmine\Server;
use RuntimeException;

class Utils{

	/**
	 * Checks if the required virions/libraries are present before enabling the plugin.
	 */
	public static function checkVirions(): void{
		$requiredVirions = [
			ScoreFactory::class,
			UpdateNotifier::class,
			ConfigUpdater::class
		];

		foreach($requiredVirions as $class){
			if(!class_exists($class)){
				throw new RuntimeException("ScoreHud plugin will only work if you use the plugin phar from Poggit.");
			}
		}
	}

	/**
	 * @param $timezone
	 * @return bool
	 */
	public static function setTimezone($timezone): bool{
		if($timezone !== false){
			Server::getInstance()->getLogger()->notice(ScoreHud::PREFIX . "Server timezone successfully set to " . $timezone);

			return date_default_timezone_set($timezone);
		}

		return false;
	}
}