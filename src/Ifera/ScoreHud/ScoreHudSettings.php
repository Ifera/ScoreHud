<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud;

use pocketmine\utils\Config;

class ScoreHudSettings{

	public const PREFIX = "§8[§6Score§eHud§8]§r ";

	/** @var ScoreHud */
	private static $plugin;
	/** @var Config */
	private static $config;
	/** @var Config */
	private static $scorehud;

	private function __construct(){}

	public static function init(ScoreHud $plugin): void{
		self::$plugin = $plugin;
		self::$config = $plugin->getConfig();
		self::$scorehud = $plugin->getScoreHudConfig();
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
	public static function useDefault(): bool{
		return self::isMultiWorld() && (bool) self::$config->getNested("multi-world.default", false);
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
}