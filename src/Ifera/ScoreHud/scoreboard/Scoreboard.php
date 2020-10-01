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

use Ifera\ScoreHud\session\PlayerSession;
use jackmd\scorefactory\ScoreFactory;
use function array_keys;
use function array_values;
use function str_replace;

class Scoreboard{

	/** @var PlayerSession */
	private $session;
	/** @var string[] */
	private $lines = [];
	/** @var ScoreTag[] */
	private $tags = [];

	/** @var string[] */
	private $formattedLines = [];

	/**
	 * Scoreboard constructor.
	 *
	 * @param PlayerSession $session
	 * @param string[]      $lines
	 * @param ScoreTag[]    $tags
	 */
	public function __construct(PlayerSession $session, array $lines = [], array $tags = []){
		$this->session = $session;
		$this->lines = $lines;
		$this->tags = $tags;
	}

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

		foreach($this->tags as $key => $tag){
			$processedTags[$tag->getId()] = $tag->getValue();
		}

		return $processedTags;
	}

	public function update(): self{
		$i = 0;
		$tags = $this->getProcessedTags();

		foreach($this->lines as $index => $line){
			$i++;

			if($i > 15){
				break;
			}

			$this->formattedLines[$index] = str_replace(
				array_keys($tags),
				array_values($tags),
				$line
			);
		}

		return $this;
	}

	public function display(): self{
		$i = 0;

		foreach($this->formattedLines as $formattedLine){
			$i++;

			if($i > 15){
				break;
			}

			ScoreFactory::setScoreLine($this->session->getPlayer(), $i, $formattedLine);
		}

		return $this;
	}
}