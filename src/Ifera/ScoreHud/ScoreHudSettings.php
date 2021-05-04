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

namespace Ifera\ScoreHud;

use pocketmine\utils\Config;
use function in_array;

class ScoreHudSettings{

	public const PREFIX = "§8[§l§6S§eH§r§8]§r ";

	private static ?ScoreHud $plugin;
	private static ?Config $config;
	private static ?Config $scorehud;

	private function __construct(){}

	public static function init(ScoreHud $plugin): void{
		self::$plugin = $plugin;
		self::$config = $plugin->getConfig();
		self::$scorehud = $plugin->getScoreConfig();
	}

	public static function destroy(): void{
		self::$plugin = null;
		self::$config = null;
		self::$scorehud = null;
	}

	/*
	 * Settings from config.yml
	 */

	public static function isMultiWorld(): bool{
		return (bool) self::$config->getNested("multi-world.active", false);
	}

	/**
	 * If multi world support is enabled and scoreboard for a world is not found then
	 * check whether the user allows for using the default scoreboard instead.
	 */
	public static function useDefaultBoard(): bool{
		return self::isMultiWorld() && (bool) self::$config->getNested("multi-world.use-default", false);
	}

	public static function getDisabledWorlds(): array{
		return (array) self::$config->get("disabled-worlds", []);
	}

	public static function isInDisabledWorld(string $world): bool{
		return in_array($world, self::getDisabledWorlds());
	}

	public static function isTimezoneChanged(): bool{
		return self::$config->getNested("time.zone") !== false;
	}

	public static function getTimezone(): string{
		return (string) self::$config->getNested("time.zone", "America/New_York");
	}

	public static function getTimeFormat(): string{
		return (string) self::$config->getNested("time.format.time", "H:i:s");
	}

	public static function getDateFormat(): string{
		return (string) self::$config->getNested("time.format.date", "d-m-Y");
	}

	/*
	 * Settings from scorehud.yml
	 */

	public static function areFlickeringTitlesEnabled(): bool{
		return (bool) self::$scorehud->getNested("titles.flicker", false);
	}

	public static function getFlickerRate(): int{
		return ((int) self::$scorehud->getNested("titles.period", 5)) * 20;
	}

	public static function getTitles(): array{
		return (array) self::$scorehud->getNested("titles.lines", []);
	}

	public static function getTitle(): string{
		return (string) self::$scorehud->getNested("titles.title", "§l§aServer §dName");
	}

	public static function getDefaultBoard(): array{
		return (array) self::$scorehud->get("default-board", []);
	}

	/**
	 * Will return an array indexed by world name with their score lines.
	 */
	public static function getScoreboards(): array{
		return (array) self::$scorehud->get("scoreboards", []);
	}

	public static function getScoreboard(string $world): array{
		return (array) self::$scorehud->getNested("scoreboards." . $world . ".lines", []);
	}

	public static function worldExists(string $world): bool{
		return !empty(self::getScoreboard($world));
	}
}