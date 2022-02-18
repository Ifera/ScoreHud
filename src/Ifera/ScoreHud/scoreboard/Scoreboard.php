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

use Ifera\ScoreHud\ScoreHudSettings;
use Ifera\ScoreHud\session\PlayerSession;
use Ifera\ScoreHud\utils\HelperUtils;
use jackmd\scorefactory\ScoreFactory;
use function array_count_values;
use function array_keys;
use function array_map;
use function array_values;
use function max;
use function str_repeat;
use function str_replace;
use function strlen;

class Scoreboard{

	/** @var string[] */
	private $formattedLines = [];

	public function __construct(
		private PlayerSession $session,
		private array $lines = [],
		private array $tags = []
	) {}

	public function getSession(): PlayerSession{
		return $this->session;
	}

	/**
	 * @return string[]
	 */
	public function getLines(): array{
		return $this->lines;
	}

	/**
	 * Returns the line containing the particular tag on the scoreboard.
	 */
	public function getLineOfTag(ScoreTag $tag): int {
		foreach ($this->lines as $index => $line){
			if (trim($line) === "") continue;
			if (str_contains($line, $tag->getId())) return $index;
		}

		return -1;
	}

	public function getLine(int $index): string {
		return (string) $this->lines[$index];
	}

	/**
	 * @return ScoreTag[]
	 */
	public function getTags(): array{
		return $this->tags;
	}

	public function setTags(array $tags): void{
		$this->tags = $tags;
	}

	public function getTag(string $name, &$index = null): ?ScoreTag{
		$tag = null;

		foreach($this->tags as $key => $scoreTag){
			if($scoreTag->getName() === $name || $scoreTag->getId() === $name){
				$tag = $scoreTag;
				$index = $key;
				break;
			}
		}

		return $tag;
	}

	public function setTag(int $index, ScoreTag $tag): void{
		$this->tags[$index] = $tag;
	}

	/**
	 * Returns tags used by the scoreboard indexed by their
	 * id followed by its value.
	 */
	public function getProcessedTags(): array{
		$processedTags = [];

		foreach($this->tags as $tag){
			$processedTags[$tag->getId()] = $tag->getValue();
		}

		return $processedTags;
	}

	public function handleSingleTagUpdate(ScoreTag $tag): self{
		$player = $this->session->getPlayer();

		if(!$player->isOnline() || HelperUtils::isDisabled($player) || ScoreHudSettings::isInDisabledWorld($player->getWorld()->getFolderName())){
			return $this;
		}

		$tags = $this->getProcessedTags();
		$index = $this->getLineOfTag($tag);
		$line = $this->getLine($index);

		$line = str_replace(
			array_keys($tags),
			array_values($tags),
			$line
		);

		ScoreFactory::sendLine($player, $index + 1, ScoreFactory::setScoreLine($player, $index + 1, " " . $line . " "));

		return $this;
	}

	public function update(): self{
		$player = $this->session->getPlayer();

		if(!$player->isOnline() || HelperUtils::isDisabled($player) || ScoreHudSettings::isInDisabledWorld($player->getWorld()->getFolderName())){
			return $this;
		}

		$i = 0;
		$tags = $this->getProcessedTags();
		$duplicateLines = [];

		foreach($this->lines as $index => $line){
			$i++;

			if($i > 15){
				break;
			}

			if($line === ""){
				$this->lines[$index] = " ";
				$line = " ";
			}

			if(array_count_values($this->lines)[$line] > 1){
				$duplicateLines[] = $line;
				$line = $line . str_repeat(" ", array_count_values($duplicateLines)[$line]);
			}

			$line = " " . $line . (max(array_map("strlen", $this->lines)) === strlen($line) ? " " : "") . " ";

			$this->formattedLines[$index] = str_replace(
				array_keys($tags),
				array_values($tags),
				$line
			);
		}

		return $this;
	}

	public function display(): self{
		$player = $this->session->getPlayer();

		if(!$player->isOnline() || HelperUtils::isDisabled($player) || ScoreHudSettings::isInDisabledWorld($player->getWorld()->getFolderName())){
			return $this;
		}

		$i = 0;

		foreach($this->formattedLines as $formattedLine){
			$i++;

			if($i > 15){
				break;
			}

			ScoreFactory::setScoreLine($player, $i, $formattedLine);
		}

		ScoreFactory::sendLines($player);

		return $this;
	}
}