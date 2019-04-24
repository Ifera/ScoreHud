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
 * Copyright (c) 2018 JackMD  < https://github.com/JackMD >
 *
 * Discord: JackMD#3717
 * Twitter: JackMTaylor_
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

namespace JackMD\ScoreHud\addon;

use JackMD\ScoreHud\ScoreHud;
use pocketmine\Player;

/**
 * Instead of implementing this class, AddonBase class should be extended for Addon making.
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
	public function onEnable(): void;

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
	 * For example addons refer here: https://github.com/JackMD/ScoreHud-Addons
	 *
	 * @param Player $player
	 * @return array
	 */
	public function getProcessedTags(Player $player): array;
}
