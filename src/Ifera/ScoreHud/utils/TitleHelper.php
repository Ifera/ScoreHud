<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\utils;

use Ifera\ScoreHud\ScoreHudSettings;

class TitleHelper{

	private static $titleIndex = 0;

	public static function getTitle(): string{
		$title = ScoreHudSettings::getTitle();

		if(ScoreHudSettings::areFlickeringTitlesEnabled()){
			$titles = ScoreHudSettings::getTitles();

			if(!isset($titles[self::$titleIndex])){
				self::$titleIndex = 0;
			}

			$title = $titles[self::$titleIndex];

			self::$titleIndex++;
		}

		return $title;
	}
}