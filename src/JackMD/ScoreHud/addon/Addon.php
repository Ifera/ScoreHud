<?php
declare(strict_types = 1);

namespace JackMD\ScoreHud\addon;

use JackMD\ScoreHud\ScoreHud;
use pocketmine\Player;

/**
 * Instead of implementing this class AddonBase class should be implemented for Addon making.
 * @see AddonBase
 *
 * Interface Addon
 *
 * @package JackMD\ScoreHud\addon
 */
interface Addon{

	/**
	 * Addon constructor.
	 *
	 * @param ScoreHud         $scoreHud
	 * @param AddonDescription $description
	 */
	public function __construct(ScoreHud $scoreHud, AddonDescription $description);

	/**
	 * This is called whenever an Addon is successfully enabled. Depends on your use case.
	 * Almost same as Plugin::onEnable().
	 */
	public function initiate(): void;

	/**
	 * Returns the ScoreHud plugin for whatever reason an addon would like to use it.
	 *
	 * @return ScoreHud
	 */
	public function getScoreHud(): ScoreHud;

	/**
	 * Returns the description containing name, main etc of the addon.
	 *
	 * @return AddonDescription
	 */
	public function getDescription(): AddonDescription;

	/**
	 * After doing the edits in your script.
	 * Return the final result to be used by ScoreHud using this.
	 *
	 * For example addons refer here: https://github.com/JackMD/ScoreHud/tree/addons
	 *
	 * @param Player $player
	 * @param string $string
	 * @return string
	 */
	public function getProcessedString(Player $player, string $string): string;
}
