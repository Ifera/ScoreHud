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
use pocketmine\Server;

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

	public function onEnable(): void{
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

	/**
	 * @return Server
	 */
	public function getServer(): Server{
		return $this->scoreHud->getServer();
	}
}
