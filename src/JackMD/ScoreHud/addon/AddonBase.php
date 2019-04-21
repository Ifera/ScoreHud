<?php

declare(strict_types = 1);

namespace JackMD\ScoreHud\addon;

use JackMD\ScoreHud\ScoreHud;

/**
 * Use of this class is encouraged instead of Addon.php.
 *
 * Please refer to Addon.php for details on what the methods below do.
 * @see Addon.php
 *
 * Class AddonBase
 *
 * @package JackMD\ScoreHud\addon
 */
abstract class AddonBase implements Addon{

	/** @var ScoreHud */
	private $scoreHud;
	/** @var AddonDescription */
	private $description;

	/**
	 * AddonBase constructor.
	 *
	 * @param ScoreHud         $scoreHud
	 * @param AddonDescription $description
	 */
	public function __construct(ScoreHud $scoreHud, AddonDescription $description){
		$this->scoreHud = $scoreHud;
		$this->description = $description;
	}

	public function initiate(): void{
	}

	/**
	 * @return ScoreHud
	 */
	public function getScoreHud(): ScoreHud{
		return $this->scoreHud;
	}

	/**
	 * @return AddonDescription
	 */
	final public function getDescription(): AddonDescription{
		return $this->description;
	}
}
