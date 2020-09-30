<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\scoreboard;

use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\session\PlayerSession;
use Ifera\ScoreHud\utils\Utils;
use function array_merge;

class ScoreboardHelper{

	public static function createDefault(PlayerSession $session): ?Scoreboard{
		$tags = [];
		$lines = ScoreHud::getInstance()->getScoreHudConfig()->get("score-lines");

		foreach(self::resolveLines($lines) as $index => $tagName){
			$tag = new ScoreTag($tagName, "");

			$ev = new TagsResolveEvent($session->getPlayer(), $tag);
			$ev->call();

			$tags[] = $ev->getTag();
		}

		return new Scoreboard($session, $tags);
	}

	public static function resolveLines(array $lines): array{
		$tags = [];

		foreach($lines as $i => $line){
			$tags = array_merge($tags, Utils::resolveTags($line));
		}

		return $tags;
	}
}