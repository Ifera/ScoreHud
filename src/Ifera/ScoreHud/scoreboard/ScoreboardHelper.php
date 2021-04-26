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

namespace Ifera\ScoreHud\scoreboard;

use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\ScoreHudSettings;
use Ifera\ScoreHud\session\PlayerSession;
use Ifera\ScoreHud\utils\Utils;
use function array_merge;

class ScoreboardHelper{

	private static function constructTag(PlayerSession $session, string $tagName): ScoreTag{
		$tag = new ScoreTag($tagName, "");

		$ev = new TagsResolveEvent($session->getPlayer(), $tag);
		$ev->call();

		return $ev->getTag();
	}

	public static function createDefault(PlayerSession $session): Scoreboard{
		$tags = [];

		foreach(self::resolveLines($lines = ScoreHudSettings::getDefaultBoard()) as $tagName){
			$tags[] = self::constructTag($session, $tagName);
		}

		return new Scoreboard($session, $lines, $tags);
	}

	public static function create(PlayerSession $session, string $world): Scoreboard{
		$tags = [];

		foreach(self::resolveLines($lines = ScoreHudSettings::getScoreboard($world)) as $tagName){
			$tags[] = self::constructTag($session, $tagName);
		}

		return new Scoreboard($session, $lines, $tags);
	}

	/**
	 * Separates the tags from the lines and returns all the tag names
	 */
	public static function resolveLines(array $lines): array{
		$tags = [];

		foreach($lines as $line){
			$tags = array_merge($tags, Utils::resolveTags($line));
		}

		return $tags;
	}
}