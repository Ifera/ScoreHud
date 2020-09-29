<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\scoreboard;

use Ifera\ScoreHud\session\PlayerSession;
use function array_count_values;
use function array_map;
use function max;
use function str_repeat;
use function strlen;

class Scoreboard{

	/** @var PlayerSession */
	private $session;
	/** @var ScoreTag[] */
	private $tags = [];

	/**
	 * Scoreboard constructor.
	 *
	 * @param PlayerSession $session
	 * @param ScoreTag[]    $tags
	 */
	public function __construct(PlayerSession $session, array $tags = []){
		$this->session = $session;
		$this->tags = $tags;
	}

	public function getSession(): PlayerSession{
		return $this->session;
	}

	public function getTags(): array{
		return $this->tags;
	}

	public function setTags(array $tags): void{
		$this->tags = $tags;
	}

	public function getTag(string $name, &$index = null): ?ScoreTag{
		$tag = null;

		foreach($this->tags as $key => $scoreTag){
			if($scoreTag->getName() === $name){
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
	 * Returns all the tags after fixing duplicated lines
	 */
	public function getProcessedTags(): array{
		$tags = array_map(
			function(ScoreTag $tag): string{
				return $tag->getValue();
			},
			$this->tags
		);

		$processedTags = [];
		$duplicateLines = [];

		foreach($this->tags as $key => $tag){
			$name = $tag->getName();
			$line = $tag->getValue();

			if($line === "" || $name === "{line}"){
				$tags[$key] = " ";
				$line = " ";
			}

			if(array_count_values($tags)[$line] > 1){
				$duplicateLines[] = $line;
				$line = $line . str_repeat(" ", array_count_values($duplicateLines)[$line]);
			}

			$line = " " . $line . (max(array_map("strlen", $tags)) === strlen($line) ? "  " : "");

			$processedTags[$name] = $line;
		}

		return $processedTags;
	}
}