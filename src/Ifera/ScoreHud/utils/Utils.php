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

namespace Ifera\ScoreHud\utils;

use Ifera\ScoreHud\ScoreHudSettings;
use JackMD\ConfigUpdater\ConfigUpdater;
use jackmd\scorefactory\ScoreFactory;
use JackMD\UpdateNotifier\UpdateNotifier;
use RuntimeException;
use function preg_match_all;
use function preg_quote;

class Utils{

	private static $REGEX = "";

	/**
	 * Massive shout-out to Cortex/Marshall for this bit of code
	 * used from HRKChat
	 */
	private static function REGEX(): string{
		if(self::$REGEX === ""){
			self::$REGEX = "/(?:" . preg_quote("{") . ")((?:[A-Za-z0-9_\-]{2,})(?:\.[A-Za-z0-9_\-]+)+)(?:" . preg_quote("}") . ")/";
		}

		return self::$REGEX;
	}

	/**
	 * Massive shout-out to Cortex/Marshall for this bit of code
	 * used from HRKChat
	 */
	public static function resolveTags(string $line): array{
		$tags = [];

		if(preg_match_all(self::REGEX(), $line, $matches)) {
			$tags = $matches[1];
		}

		return $tags;
	}

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

	public static function setTimezone(): bool{
		return date_default_timezone_set(ScoreHudSettings::getTimezone());
	}
}