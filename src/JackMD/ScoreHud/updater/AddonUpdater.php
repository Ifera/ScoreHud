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

namespace JackMD\ScoreHud\updater;

use JackMD\ScoreHud\addon\Addon;
use JackMD\ScoreHud\ScoreHud;
use JackMD\ScoreHud\updater\task\AddonUpdateNotifyTask;

class AddonUpdater{

	/** @var ScoreHud */
	private $plugin;

	/**
	 * AddonUpdater constructor.
	 *
	 * @param ScoreHud $plugin
	 */
	public function __construct(ScoreHud $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @param Addon $addon
	 */
	public function check(Addon $addon): void{
		$plugin = $this->plugin;
		$description = $addon->getDescription();

		$addonName = $description->getName();
		$addonVersion = $description->getVersion();

		if($addonVersion === "0.0.0"){
			$plugin->getLogger()->warning("Addon $addonName is outdated. A new version has been released. Download the latest version from https://github.com/JackMD/ScoreHud-Addons");

			return;
		}

		$plugin->getServer()->getAsyncPool()->submitTask(new AddonUpdateNotifyTask($addonName, $addonVersion));
	}
}