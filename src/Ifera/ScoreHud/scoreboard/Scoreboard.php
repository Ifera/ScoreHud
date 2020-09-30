<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\scoreboard;

use Ifera\ScoreHud\session\PlayerSession;

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

	public function getProcessedTags(): array{
		$processedTags = [];

		foreach($this->tags as $key => $tag){
			$processedTags[$tag->getId()] = $tag->getValue();
		}

		return $processedTags;
	}
}